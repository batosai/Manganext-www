<?php

class Opsone_Bots_Akata extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.akata.fr';

  public function get()
  {
    if ($this->_url == '#') return;

    $dom = new Zend_Dom_Query(file_get_contents($this->_url));
    $results = $dom->query('#contenu-centre td');

    if (!count($results)) {
      $this->_alert();
    }

    foreach ($results as $result) {
      $this->_loadItem($result);
    }
  }

  private function _loadItem($item)
  {
    $this->_dom = new Zend_Dom_Query($item->C14N());

    $book = new Model_Book();
    $book->editor_id = 4;
    $book->editor_name = 'Delcourt / Akata'; 
    $book->website = $this->_url;

    $element = $this->_getElement('strong');
    if (!$element) {
      $element = $this->_getElement('span');
    }

    if ($element) {
      $book->name = $this->_title($element->nodeValue);

      $book->number = $this->_number($element->nodeValue);
    }
    else {
      return null;
    }

    $book->published_at = $this->_date('div');

    $element = $this->_getElement('img');
    if ($element) {
      $book->image_src = $this->_baseUrl . $element->getAttribute('src');
    }

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
    $value =  $this->_formate($value);
    preg_match('#(.+)\s([0-9]+)#', $value, $matches);

    return isset($matches[1]) ? $matches[1] : $value;
  }

  private function _number($value)
  {
    $value =  $this->_formate($value);
    preg_match('#(.+)\s([0-9]+)#', $value, $matches);

    return isset($matches[2]) ? 'Tome ' . $matches[2] : null;
  }

  private function _date($value)
  {
    $element = $this->_getElement($value);
    if ($element)
    {
      preg_match('#Sortie:(.+) ?#', $this->_formate($element->nodeValue), $matches);

      if (isset($matches[1]))
      {
        $value =  $this->_formate($matches[1]);
        $d = explode('/', $value);

        $date = new DateTime();

        if (count($d) == 2) {
          $date->setDate($d[1], $d[0], 15);
        }
        else {
          $date->setDate($d[2], $d[1], $d[0]);
        }

        return $date->format('Y-m-d') . ' 00:00:00';
      }
    }

    return null;
  }

}