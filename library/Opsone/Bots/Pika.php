<?php

class Opsone_Bots_Pika extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.pika.fr';

  public function get()
  {
    if ($this->_url == '#') return;

    $dom = new Zend_Dom_Query(file_get_contents($this->_url));
    $results = $dom->query('map[name="tab_map"] area');

    if (!count($results)) {
      $this->_alert();
    }

    foreach ($results as $result) {
      $this->_loadItem($this->_baseUrl . $result->getAttribute('href'));
    }
  }

  private function _loadItem($url)
  {
    $dom = new Zend_Dom_Query(file_get_contents($url));
    $results = $dom->query('#books .row');

    foreach ($results as $result)
    {
      $d = new Zend_Dom_Query($result->C14N());

      $elements =  $d->query('div');
      $params = array();
      foreach ($elements as $i => $element) {
        if ($i == 1)
          $params['published_at'] = $this->_date($element->nodeValue);
        if ($i == 3) {
          $params['name'] = $this->_title($element->nodeValue);
          $params['number'] = $this->_number($element->nodeValue);
        }
        if ($i == 4)
          $params['author'] = $this->_formate($element->nodeValue);
      }

      $elements =  $d->query('a');
      foreach ($elements as $element) {
        $url =  $this->_baseUrl . $element->getAttribute('href');
        $this->_loadPage($url, $params);
        break;
      }
    }
  }

  private function _loadPage($url, $params)
  {
    $contentFile = file_get_contents($url);
    $this->_dom = new Zend_Dom_Query($contentFile);

    $book = new Model_Book();
    $book->fromArray($params);
    $book->editor_id = 3;
    $book->editor_name = 'Pika EDITION'; 

    $book->text = $this->_text('#text_resume');

    $book->state = $this->_state($contentFile);

    $element = $this->_getElement('#image_fr');
    if ($element) {
      $book->image_src = $this->_baseUrl . $element->getAttribute('src');
    }
    
    $book->price = $this->_price($contentFile);

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
    $value = str_replace('• ', '', $value);
    preg_match('#(.+)\s([0-9]+)#', $value, $matches);

    return isset($matches[1]) ? $matches[1] : $value;
  }

  private function _number($value)
  {
    $value =  $this->_formate($value);
    preg_match('#(.+)\s([0-9]+)#', $value, $matches);

    return isset($matches[2]) ? 'Tome ' . $matches[2] : null;
  }

  private function _text($value)
  {
    $element = $this->_getElement($value);
    if (!$element) return null;

    $content = str_replace("\n", '', $element->C14N());
    $content = str_replace("<strong>RÉSUMÉ</strong>", '', $content);

    return trim(strip_tags($content));
  }

  private function _state($contentFile)
  {
    preg_match_all('#([0-9]+) volumes parus#', $contentFile, $matches);

    $state = '';

    if (isset($matches[1][1])) {
      $state .= $matches[1][1] . ' au japon';
    }
    elseif (isset($matches[1][0])) {
      $state .= $matches[1][0] . ' en france';
    }

    mb_regex_encoding("UTF-8");
    mb_ereg_search_init($contentFile, 'Série complète');

    if (mb_ereg_search()) {
      $state .= ' (Série complète)';
    }
    else {
      $state .= ' (Série en cours)';
    }

    return $state;
  }

  private function _price($contentFile)
  {
    preg_match('#prix de vente : (.+) €#', strip_tags($contentFile), $matches);

    if (!isset($matches[1])) return null;

    $value = str_replace(',', '.', $matches[1]);
    return $value;
  }

  private function _date($value)
  {
    $value = $this->_formate($value);
    $date = new DateTime();

    $d = explode('/', $value);

    $date->setDate('20'.$d[2], $d[1], $d[0]);
    return $date->format('Y-m-d') . ' 00:00:00';
  }

  protected function _image($book)
  {
    if ($book->image_src && @fopen($book->image_src, 'r'))
    {
      if ($book->image_src == $this->_baseUrl . '/new//sites/all/themes/pika/images/no-image.jpg') {
        $book->image_src = null;
        $book->save();
        return;
      }
      parent::_image($book);
    }
  }
}