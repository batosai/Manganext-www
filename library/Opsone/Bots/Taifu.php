<?php

class Opsone_Bots_Taifu extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.taifu-comics.com';

  public function get()
  {
    if ($this->_url == '#') return;

    $dom = new Zend_Dom_Query(file_get_contents($this->_url));
    $results = $dom->query('.sliderNews2 ul li');

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
      $url = $element->getAttribute('href');
      $this->_loadPage($url);
      break;
    }
  }

  private function _loadPage($url)
  {
    $contentFile = file_get_contents($url);
    $this->_dom = new Zend_Dom_Query($contentFile);

    $book = new Model_Book();
    $book->editor_id = 9;
    $book->editor_name = 'Taifu comics';
    $book->website = $url;

    $book->name = $this->_title('.bookinfo h2');

    $book->number = $this->_number('.bookinfo h2');

    $book->text = $this->_text('#blockExtensible .bookinfo');

    $book->author_name = $this->_author();

    $book->state = $this->_state('.bookinfo .infosBlock');

    $element = $this->_getElement('.bookinfo .cpModal');
    $book->image_src = $element->getAttribute('href');

    $book->published_at = $this->_date('.bookinfo .infosBlock');

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
    $value =  $this->_formate($element->nodeValue);
    preg_match('#(.+)\sVol\.([0-9]+)#U', $value, $matches);

    return isset($matches[1]) ? $matches[1] : $value;
  }

  private function _number($value)
  {
    $element = $this->_getElement($value);
    $value =  $this->_formate($element->nodeValue);
    preg_match('#(.+)\sVol\.([0-9]+)#U', $value, $matches);

    return isset($matches[2]) ? 'Tome '.$matches[2] : null;
  }

  private function _text($value)
  {
    $elements =  $this->_dom->query($value);
    $i = 1;
    foreach ($elements as $element) {
      if ($i == 2) {
        $value = str_replace('Résumé', '', $element->nodeValue);
        return trim($value);
      }
      $i++;
    }
    return false;
  }

  private function _author()
  {
    $element = $this->_getElement('.bookinfo a[title="Scénariste"]');
    $scenariste = trim($element->nodeValue);

    $element = $this->_getElement('.bookinfo a[title="Dessinateur"]');
    $dessinateur = trim($element->nodeValue);

    return $scenariste == $dessinateur ? $scenariste : $scenariste . ' / ' .$dessinateur;
  }

  private function _state($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->nodeValue);
    $content = str_replace("\t", '', $content);
    $content = preg_replace('!\s+!', ' ', $content);

    preg_match('#Nb de volume:(.+)Nb de pages#U', $content, $matches);

    return isset($matches[1]) ? trim($matches[1]) : null;
  }

  private function _date($value)
  {
    $element = $this->_getElement($value);
    $content = str_replace("\n", '', $element->nodeValue);
    $content = str_replace("\t", '', $content);

    preg_match('#Date de sortie:(.+)Pays#U', $content, $matches);

    if (!isset($matches[1])) return null;

    $month = array('janvier' => '01', 'février' => '02', 'fevrier' => '02', 'mars' => '03', 'avril' => '04', 'mai' => '05', 'juin' => '06', 'juillet' => '07', 'août' => '08', 'aout' => '08', 'septembre' => '09', 'octobre' => '10', 'novembre' => '11', 'décembre' => '12', 'decembre' => '12');
    $date = new DateTime();

    $d = explode(' ', trim($matches[1]));

    $date->setDate($d[2], $month[mb_strtolower($d[1])], $d[0]);
    return $date->format('Y-m-d') . ' 00:00:00';
  }

}