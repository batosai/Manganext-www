<?php

class Opsone_Bots_Glenat extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.glenatmanga.com';

  public function get()
  {
    if ($this->_url == '#') return;

    $dom = new Zend_Dom_Query(file_get_contents($this->_url));
    $results = $dom->query('#col_milieu h2 a');

    if (!count($results)) {
      $this->_alert();
    }

    foreach ($results as $result)
    {
        $url = $this->_baseUrl . $result->getAttribute('href');
        $this->_loadPage($url);
    }
  }

  private function _loadPage($url)
  {
    $book = new Model_Book();
    $book->editor_id = 2;
    $book->editor_name = 'Glénat';

    $this->_dom = new Zend_Dom_Query(file_get_contents($url));

    $element = $this->_getElement('.couverture');
    $book->image_src = $this->_baseUrl . $element->getAttribute('src');

    $element = $this->_getElement('#Alerte p strong');
    $value = str_replace('A paraître le ', '', $element->nodeValue);
    $book->published_at = $this->_date($value);

    $book->name = $this->_title('.fiche_droite h1');
    $book->number = $this->_number('.fiche_droite h1');

    $element = $this->_text('.fiche_droite > p');
    $book->text = $element->nodeValue;

    //$book->editor_name = $this->_editor('.fiche_droite p');

    $value = $this->_price('.fiche_droite p');
    $book->price =  str_replace(' ?', '', $value);

    $book->state = $this->_formateState($this->_state('.fiche_droite p'));

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

    if (count($temp) > 1)
      unset($temp[count($temp)-1]);

    return implode(' - ', $temp);
  }

  private function _number($value)
  {
    $element = $this->_getElement($value);
    $temp = explode(' - ', trim($element->nodeValue));

    if (!count($temp) || count($temp) == 1) return null;

    return $temp[count($temp)-1];
  }

  private function _editor($value)
  {
    $element = $this->_getElement($value);

    preg_match('#Editeur Japonais :(.+)#', $this->_formate($element->nodeValue), $matches);
    if (isset($matches[1]))
      return $this->_formate($matches[1]);
    return null;
  }

  private function _price($value)
  {
    $element = $this->_getElement($value);

    preg_match('#Prix:(.+) ?#', $this->_formate($element->nodeValue), $matches);
    if (isset($matches[1]))
      return $this->_formate($matches[1]);
    return null;
  }

  private function _state($value)
  {
    $element = $this->_getElement($value);

    preg_match('#Nombre de tomes associés : (.+)Editeur#', $element->nodeValue, $matches);
    if (isset($matches[1]))
      return $this->_formate($matches[1]);
    return null;
  }

  private function _text($value)
  {
    $elements =  $this->_dom->query($value);
    $i = 1;
    foreach ($elements as $element) {
      if ($i == 2) {
        return $element;
      }
      $i++;
    }
  }

  protected function _image($book)
  {
    if ($book->image_src && @fopen($book->image_src, 'r'))
    {
      if ($book->image_src == $this->_baseUrl . '/img/interface/default_BIG.jpg') {
        $book->image_src = null;
        $book->save();
        return;
      }
      parent::_image($book);
    }
  }

  private function _formateState($value)
  {
    if ($value = $this->_formate($value))
    {
      $value = str_replace('?', 'é', $value);
      $res = explode(' ', $value);

      if (count($res) == 1) {
        $value .= $res[0] > 1 ? ' tomes associés' : ' tome associé';
      }
    }
    return $value;
  }

  private function _date($value)
  {
    $value = $this->_formate($value);
    $date = new DateTime();

    $d = explode('/', $value);

    $date->setDate($d[2], $d[1], $d[0]);
    return $date->format('Y-m-d') . ' 00:00:00';
  }
}