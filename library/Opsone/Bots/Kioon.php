<?php

class Opsone_Bots_Kioon extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.ki-oon.com';

  public function get()
  {
    if ($this->_url == '#') return;

    $dom = new Zend_Dom_Query(file_get_contents($this->_url));
    $results = $dom->query('#content figure a');

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

    foreach ($results as $element) {
      $url =  $this->_baseUrl . $element->getAttribute('href');
      $this->_loadPage($url);
      break;
    }
  }

  private function _loadPage($url)
  {
    $contentFile = file_get_contents($url);
    $this->_dom = new Zend_Dom_Query($contentFile);

    $book = new Model_Book();
    $book->editor_id = 8;
    $book->editor_name = 'Ki-oon';
    $book->website = $url;

    $element = $this->_getElement('.colGD h1');
    $book->name = $this->_formate($element->nodeValue);

    $book->number = $this->_number('.colGD .content');

    $book->author_name = $this->_author('.colGD .content');

    $element = $this->_getElement('.colGD .thickbox');
    $book->image_src = $element->getAttribute('href');

    $book->published_at = $this->_date('.colGD .content');

    $book->price = $this->_price('.colGD .content');

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

  private function _number($value)
  { 
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->nodeValue);
    $content = str_replace("\t", '', $content);

    preg_match('#T([0-9]{2})Auteur#U', $content, $matches);

    if (!isset($matches[1])) return null;

    return 'Tome '.$matches[1];
  }

  private function _author($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->nodeValue);
    $content = str_replace("\t", '', $content);

    preg_match('#Auteur :(.+)Parution#U', $content, $matches);

    if (!isset($matches[1])) return null;

    return $matches[1];
  }

  private function _price($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->nodeValue);
    $content = str_replace("\t", '', $content);

    preg_match('#Prix de vente :(.+)€#U', $content, $matches);

    if (!isset($matches[1])) return null;

    return trim(str_replace(',', '.', $matches[1]));
  }

  private function _date($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->nodeValue);
    $content = str_replace("\t", '', $content);

    preg_match('#Parution :(.+)Format#U', $content, $matches);

    if (!isset($matches[1])) return null;

    $date = new DateTime();

    $d = explode('-', $matches[1]);

    $date->setDate($d[2], $d[1], $d[0]);
    return $date->format('Y-m-d') . ' 00:00:00';
  }

  protected function _image($book)
  {
    if ($book->image_src && @fopen($book->image_src, 'r'))
    {
      if ($book->image_src == $this->_baseUrl.'/') {
        $book->image_src = null;
        $book->save();
        return;
      }

      parent::_image($book);
    }
  }
}