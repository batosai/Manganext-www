<?php

class Opsone_Bots_Tonkam extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.tonkam.com';

  public function get()
  {
    if ($this->_url == '#') return;

    $dom = new Zend_Dom_Query($this->_file_get_contents($this->_url));
    $results = $dom->query('#page_bg .planning_run');

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
      $url =  $this->_baseUrl . '/' .$element->getAttribute('href');
      $this->_loadPage($url);
    }
  }

  private function _loadPage($url)
  {
    $contentFile = $this->_file_get_contents($url);
    $this->_dom = new Zend_Dom_Query($contentFile);

    $book = new Model_Book();
    $book->editor_id = 10;
    $book->editor_name = 'Tonkam';
    $book->website = $url;

    $book->name = $this->_title('#livre_titre h5');

    $book->number = $this->_number('#livre_titre h5');

    $book->text = $this->_text('#livre_resume');

    $book->author_name = $this->_author('#livre_detail');

    if ($element = $this->_getElement('.img_gauche')) {
      $book->image_src = $this->_baseUrl . '/' . $element->getAttribute('src');
    }

    $book->published_at = $this->_date('#livre_detail');

    $book->price = $this->_price('#livre_detail');

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
    $content = str_replace("\n", '', $element->C14N());

    preg_match('#<h5>(.+)<br>(.+)<\/h5>#U', $content, $matches);

    $title = htmlspecialchars_decode($matches[1]);

    return $this->_replaceSpecialChar($title);
  }

  private function _number($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->C14N());

    preg_match('#<h5>(.+)<br>(.+)<\/h5>#U', $content, $matches);

    preg_match('#<span>(.+)<\/span>#U', $matches[2], $matches);

    if (!$matches)
      return null;

    $temp = strip_tags($matches[1]);

    preg_match('#vol. ([0-9]+)#U', $temp, $matches);

    if ($matches)
      return 'Tome ' . $matches[1];
    return $temp;
  }

  private function _text($value)
  {
    if ($element = $this->_getElement($value)) {
      $content = str_replace("\n", '', strip_tags($element->C14N()));
      $content = str_replace("\t", '', $content);

      return str_replace('Résumé', '', trim($this->_replaceSpecialChar($content)));
    }
    return null;
  }

  private function _author($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->C14N());
    $content = str_replace("\t", '', $content);

    preg_match('#Auteur\(s\) : (.+) Collection#U', $this->_trim(strip_tags($content)), $matches);

    if (!isset($matches[1])) return null;

    return $matches[1];
  }

  private function _price($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->C14N());
    $content = str_replace("\t", '', $content);

    preg_match('#Prix : (.+) Date#U', $this->_trim(strip_tags($content)), $matches);

    preg_match('#TK ([0-9]+) \((.+)€\)#U', $matches[1], $matches);

    if (!isset($matches[2])) return null;

    return $matches[2];
  }

  private function _date($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->C14N());
    $content = str_replace("\t", '', $content);

    preg_match('#Date de parution : (.+) ISBN?#', $this->_trim(strip_tags($content)), $matches);

    if (!isset($matches[1])) return null;

    $date = new DateTime();

    $d = explode('/', $matches[1]);

    $date->setDate($d[2], $d[1], $d[0]);
    return $date->format('Y-m-d') . ' 00:00:00';
  }

}