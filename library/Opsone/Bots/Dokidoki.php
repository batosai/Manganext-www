<?php

class Opsone_Bots_Dokidoki extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.doki-doki.fr';

  public function get()
  {
    if ($this->_url == '#') return;

    for ($i=0;$i<3;$i++)
    {
      $month = date('m') + $i;
      $year = date('Y');

      if ($month > 12) {
        $month = '0' . ($month-12);
        $year += 1;
      }
      elseif($month < 10) {
        $month = '0'. $month;
      }

      $dom = new Zend_Dom_Query($this->_file_get_contents($this->_url . $month.'|'.$year));

      $results = $dom->query('.newalbumcata');

      if (!count($results)) {
        $this->_alert();
      }

      foreach ($results as $result) {
        $this->_loadItem($result);
      }
    }

  }

  private function _loadItem($item)
  {
    $dom = new Zend_Dom_Query($item->C14N());
    $results = $dom->query('a');

    foreach ($results as $element) {
      $url =  $this->_baseUrl . '/' .$element->getAttribute('href');
      $this->_loadPage($url);
      break;
    }
  }

  private function _loadPage($url)
  {
    $contentFile = $this->_file_get_contents($url);
    $this->_dom = new Zend_Dom_Query($contentFile);

    $book = new Model_Book();
    $book->editor_id = 11;
    $book->editor_name = 'Doki-doki';
    $book->website = $url;

    $element = $this->_getElement('.infosalbum h1');
    $book->name = $element->nodeValue;

    $book->number = $this->_number('.infosalbum h3');


    $book->text = $this->_text('.infosalbum .resume');

    $book->author_name = $this->_author('.infosalbum .auteurs');

    if ($element = $this->_getElement('.album_couv_codifs img')) {
      $book->image_src = $this->_baseUrl . '/' . $element->getAttribute('src');
    }

    $book->published_at = $this->_date('.dateparution');
    if (!$book->published_at) {
      return;
    }

    $book->price = $this->_price('.albumprix');

    $book->state = $this->_state('.album_couv_codifs');

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
    if ($element = $this->_getElement($value))
    {
      $text = $this->_formate($element->nodeValue);

      $text = str_replace('Volume', 'Tome', $text);

      return $text;
    }
    return null;
  }

  private function _text($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->nodeValue);
    $content = str_replace("\t", '', $content);
    $content = str_replace("\r\r", ' ', $content);
    return trim($content);
  }

  private function _author($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->nodeValue);
    $content = str_replace("\t", '', $content);

    return trim($content);
  }

  private function _price($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->C14N());
    $content = str_replace("\t", '', $content);
    $content = str_replace(",", '.', $content);

    preg_match('#Prix France : (.+) €#U', $this->_trim(strip_tags($content)), $matches);

    if (!isset($matches[1])) return null;

    return $matches[1];
  }


  private function _state($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->C14N());
    $content = str_replace("\t", '', $content);

    preg_match('#Nb. vol. Japon : (.+) Nb. vol. France#U', $this->_trim(strip_tags($content)), $matches);

    if (!isset($matches[1])) return null;

    return 'Nb. vol. Japon : ' . $matches[1];
  }

  private function _date($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->nodeValue);
    $content = str_replace("\t", '', $content);

    $content = str_replace("Parution le", '', $content);
    $content = str_replace("Paru le", '', $content);

    $value = $this->_formate($content);
    $month = array('janvier' => '01', 'fevrier' => '02', 'février' => '02', 'mars' => '03', 'avril' => '04', 'mai' => '05', 'juin' => '06', 'juillet' => '07', 'aout' => '08', 'août' => '08', 'septembre' => '09', 'octobre' => '10', 'novembre' => '11', 'decembre' => '12', 'décembre' => '12');
    $date = new DateTime();

    $d = explode(' ', $value);

    if (count($d) == 3) {
      $date->setDate($d[2], $month[mb_strtolower($d[1])], $d[0]);
      return $date->format('Y-m-d') . ' 00:00:00';
    }

    return false;
  }
}