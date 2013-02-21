<?php

class Opsone_Bots_Kana extends Opsone_Bots_Base
{
  public function get()
  {
    if ($this->_url == '#') return;

    $dom = new Zend_Dom_Query(file_get_contents($this->_url));
    $results = $dom->query('#planning .item');

    if (!count($results)) {
      $this->_alert();
    }

    foreach ($results as $result)
    {
        $book = new Model_Book();
        $book->editor_id = 1;
        $book->editor_name = 'Kana';

        $this->_dom = new Zend_Dom_Query($result->C14N());

        $element = $this->_getElement('a');
        if ($element)
        {
          $book->website = $this->_url != '#' ? $element->getAttribute('href') : null; 

          list($book->price, $book->text) =  $this->_loadPage($element->getAttribute('href'));
        }

        $element = $this->_getElement('.etat');
        $book->state = $this->_formate($element->nodeValue);

        $element = $this->_getElement('.infos .title');
        $book->name = $this->_formate($element->nodeValue);

        $element = $this->_getElement('.infos .subtitre');
        $book->number = $this->_formate($element->nodeValue);

        if ($element = $this->_getElement('.infos .auteur')) {
          $book->author_name = $this->_formate($element->nodeValue);
        }

        $element = $this->_getElement('.titre');
        if (isset($element->nodeValue)) {
          $value = str_replace('Sortie le ', '', $element->nodeValue);
          $book->published_at = $this->_date($value);
        }

        $element = $this->_getElement('img[src*="dlpdomain"]');
        $book->image_src = $element->getAttribute('src');

        if ($book->number) {
          $b = $this->_table->findOneByNameAndNumber($book->name, $book->number);
        }
        else {
          $b = $this->_table->findOneByName($book->name);
        }

        if ($b) {
          $params = $book->toArray();
          unset($params['id'], $params['created_at'], $params['updated_at']);

          if ($book->state != 'DVD') {
            $paramsOrigin = $b->toArray();
            unset($paramsOrigin['id'], $paramsOrigin['created_at'], $paramsOrigin['updated_at']);

            if (count(array_diff($params, $paramsOrigin))) {
              $b->fromArray($params);
              $b->save();
            }

            $this->_image($b);
          }
        }
        elseif ($book->state != 'DVD') {
          $book->save();
          $this->_image($book);
        }
    }

    $nodes = $dom->query('.onglet *');
    $active = false;
    foreach ($nodes as $node)
    {
      if ($active) {
        $this->_url = $node->getAttribute('href');
        $active = false;
      }

      if ($node->getAttribute('class') == 'moisCourant')
        $active = true;
    }
    $this->get();
  }

  private function _loadPage($url)
  {
    if ($this->_url == '#') return;

    $dom = new Zend_Dom_Query(file_get_contents($url));

    $elements =  $dom->query('.price span');
    $price = null;

    foreach ($elements as $element) {
      $price = $this->_formate($element->nodeValue);
    }

    $elements =  $dom->query('#resumer p');
    $summary = null;

    foreach ($elements as $element) {
      $summary = trim($element->nodeValue);//$this->_formate($element->nodeValue);
    }

    if (!$summary) {
      $elements = $dom->query('#content-serie p');

      foreach ($elements as $element) {
        $summary = trim($element->nodeValue);//$this->_formate($element->nodeValue);
      }
    }

    return array($price, $summary);
  }

  protected function _image($book)
  {
    if ($book->image_src && @fopen($book->image_src, 'r'))
    {
      $dst = APPLICATION_PATH . '/../public/img/';
      $medium = $dst . 'medium/' . $book->id .'.jpg';
      $thumbnail = $dst . 'thumbnails/' . $book->id .'.jpg';

      copy($book->image_src, $thumbnail);

      $tmp = explode('-', $book->image_src);
      $size = end($tmp);
      unset($tmp[count($tmp)-1]);
      $tmp = implode('-', $tmp);
      copy($tmp . '-I258x392.jpg', $medium);

      $book->image = $this->_config->baseUrl . 'img/thumbnails/' . $book->id .'.jpg';
      $book->save();
    }
    else
    {
      $book->image_src = null;
      $book->save();
    }
  }

  private function _date($value)
  {
    $value = $this->_formate($value);
    $month = array('janvier' => '01', 'février' => '02', 'fevrier' => '02', 'mars' => '03', 'avril' => '04', 'mai' => '05', 'juin' => '06', 'juillet' => '07', 'août' => '08', 'aout' => '08', 'septembre' => '09', 'octobre' => '10', 'novembre' => '11', 'décembre' => '12', 'decembre' => '12');
    $date = new DateTime();

    $d = explode(' ', $value);

    $date->setDate($d[2], $month[mb_strtolower($d[1])], $d[0]);
    return $date->format('Y-m-d') . ' 00:00:00';
  }
}