<?php

class Opsone_Bots_Kaze extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.kaze-manga.fr';

  public function get()
  {
    if ($this->_url == '#') return;

    $dom = new Zend_Dom_Query(file_get_contents($this->_url));
    $results = $dom->query('.mod_hikashop_listing .hikashop_listing_item');

    if (!count($results)) {
      $this->_alert();
    }

    foreach ($results as $result) {
      $this->_loadItem($result);
    }

    // next month

    $month = date('m') + 1;
    $year = date('Y');

    if ($month > 12) {
      $month = '0' . ($month-12);
      $year += 1;
    }
    elseif($month < 10) {
      $month = '0'. $month;
    }

    $dom = new Zend_Dom_Query(file_get_contents($this->_url."/$year-$month-01"));
    $results = $dom->query('.mod_hikashop_listing .hikashop_listing_item');

    if (!count($results)) {
      $this->_alert();
    }

    foreach ($results as $result) {
      $this->_loadItem($result);
    }
  }

  private function _loadItem($item)
  {
    $dom = new Zend_Dom_Query($item->C14N());
    $results = $dom->query('a');
    $date = null;

    foreach ($results as $element) {

      $dom = new Zend_Dom_Query($element->C14N());
      $r = $dom->query('.manga_date');

      foreach ($r as $item) {
        $date =  $this->_date($item->nodeValue);

        break;
      }

      $url =  $this->_baseUrl . $element->getAttribute('href');
      $this->_loadPage($url, $date);
      break;
    }
  }

  private function _loadPage($url, $date=null)
  {
    $contentFile = file_get_contents($url);
    $this->_dom = new Zend_Dom_Query($contentFile);

    $book = new Model_Book();
    $book->editor_id = 5;
    $book->editor_name = 'Kazé';
    $book->website = $url;

    $book->name = $this->_title('h1');
    $book->number = $this->_number('h1');

    $element = $this->_getElement('.history');
    $book->text = trim($element->nodeValue);

    $element = $this->_getElement('span[itemprop="author"]');
    if (isset($element->nodeValue)) {
      $book->author_name = $element->nodeValue;
    }

    $element = $this->_getElement('.packshot a');
    $book->image_src = $element->getAttribute('href');

    if ($date) {
      $book->published_at = $date;
    }
    else {
      return;
    }

    $book->price = $this->_price('span[itemprop="price"]');
 
    if ($book->number) {
      $b = $this->_table->findOneByNameAndNumber($book->name, $book->number);
    }
    else {
      $b = $this->_table->findOneByName($book->name);
    }

    if ($b) {
      $params = $book->toArray();
      unset($params['id'], $params['created_at'], $params['updated_at']);

      $paramsOrigin = $b->toArray();
      unset($paramsOrigin['id'], $paramsOrigin['created_at'], $paramsOrigin['updated_at']);

      if (count(array_diff($params, $paramsOrigin))) {
        $b->fromArray($params);
        $b->save();
      }

      $this->_image($b);
    }
    else {
      $book->save();
      $this->_image($book);
    }
  }

  private function _title($value)
  {
    $element = $this->_getElement($value);
    $temp = explode(' - ', trim($element->nodeValue));
    unset($temp[count($temp)-1]);

    return implode(' - ', $temp);
  }

  private function _number($value)
  {
    $element = $this->_getElement($value);
    $temp = explode(' - ', trim($element->nodeValue));

    if (!count($temp)) return null;

    return $temp[count($temp)-1];
  }

  private function _price($value)
  {
    $element = $this->_getElement($value);
    preg_match('#(.+) €#', $element->nodeValue, $matches);

    return isset($matches[1]) ? str_replace(',', '.', trim($matches[1])) : null;
  }

  private function _date($contentFile)
  {
    preg_match('#([0-9]{2})\/([0-9]{2})\/([0-9]{2})#', strip_tags($contentFile), $matches);

    $date = new DateTime();

    $date->setDate("20{$matches[3]}", $matches[2], $matches[1]);

    return $date->format('Y-m-d') . ' 00:00:00';
  }

  protected function _image($book)
  {
    if ($book->image_src && @fopen($book->image_src, 'r'))
    {
      if ($book->image_src == 'http://product.kaze.fr//default/web/manga_0x600.png') {
        $book->image_src = null;
        $book->save();
        return;
      }
      parent::_image($book);
    }
  }
}