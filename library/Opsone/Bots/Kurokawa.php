<?php

class Opsone_Bots_Kurokawa extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.kurokawa.fr';

  public function get()
  {
    if ($this->_url == '#') return;

    $this->_url =  $this->_baseUrl . '/site/' . $this->_link($this->_url);

    $dom = new Zend_Dom_Query($this->_file_get_contents($this->_url));
    $results = $dom->query('.titre_ouvrage');

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
      $url =  $this->_baseUrl . '/site/' .$element->getAttribute('href');
      $this->_loadPage($url);
      break;
    }
  }

  private function _loadPage($url)
  {
    $contentFile = $this->_file_get_contents($url);
    $this->_dom = new Zend_Dom_Query($contentFile);

    $book = new Model_Book();
    $book->editor_id = 7;
    $book->editor_name = 'Kurokawa';
    $book->website = $url;

    $book->name = $this->_title('.titre_ouvrage_gd');
    $book->number = $this->_number('.titre_ouvrage_gd');

    $element = $this->_getElement('.texte_normal');
    $book->text = trim($element->nodeValue);

    $book->author_name = $this->_author('.auteur_noir');

    $element = $this->_getElement('#LIEN_ZOOM_IMAGE');
    if ($element) {
      $book->image_src = $element->getAttribute('href');
    }

    $date = $this->_date('td[class*="infos_"]');
    if (!$date) {
      return;
    }

    $book->published_at = $date;
    $book->price = $this->_price('.prix');

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

    $number = str_replace('T', 'Tome ', $temp[count($temp)-1]);

    return $number;
  }

  private function _author($value)
  {
    if ($element = $this->_getElement($value)) {
      $content = str_replace("\n", '', $element->C14N());
      preg_match('#<td(.+)>(.+)<br>(.+)<\/td>#U', $content, $matches);

      if (count($matches)) {
        $value = str_replace('</br>', ', ', $matches[2].$matches[3]);
      }
      else {
        preg_match('#<td(.+)>(.+)<\/td>#U', $content, $matches);

        if (isset($matches[2]))
          $value = $matches[2];
        else
          $value = null;
      }

      return $value;
    }
    return null;
  }

  private function _price($value)
  {
    $element = $this->_getElement($value);

    $price = str_replace(',', '.', $element->nodeValue);
    $price = trim(str_replace('€', '', $price));

    return $price;
  }

  private function _date($value)
  {
    $element = $this->_getElement($value);
    if ($element)
    {
      preg_match('#Date de parution : (.+)Nombre?#', $this->_formate($element->nodeValue), $matches);

      if (isset($matches[1]))
      {
        $value =  utf8_encode($matches[1]);
        $month = array('janvier' => '01', 'février' => '02', 'fevrier' => '02', 'mars' => '03', 'avril' => '04', 'mai' => '05', 'juin' => '06', 'juillet' => '07', 'août' => '08', 'aout' => '08', 'septembre' => '09', 'octobre' => '10', 'novembre' => '11', 'décembre' => '12', 'decembre' => '12');
        $date = new DateTime();

        $d = explode(' ', $value);

        if (count($d) != 3) return null;

        $date->setDate($d[2], $month[mb_strtolower($d[1])], $d[0]);
        return $date->format('Y-m-d') . ' 00:00:00';
      }
    }

    return null;
  }

  private function _link($url)
  {
    $output= $this->_file_get_contents($url);

    $content = str_replace("\n", '', $output);
    preg_match('#<!--MENU-->(.+)<!--FIN MENU-->#U', $content, $matches);

    $dom = new Zend_Dom_Query($matches[0]);

    $elements =  $dom->query('a[href*="page_a_paraitre_editions_kurokawa"]');
    foreach ($elements as $element) {
      break;
    }
    return $element->getAttribute('href');
  }
}