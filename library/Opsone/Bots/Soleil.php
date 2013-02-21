<?php

class Opsone_Bots_Soleil extends Opsone_Bots_Base
{
  private $_baseUrl = 'http://www.soleilmanga.com';

  public function get()
  {
    if ($this->_url == '#') return;

    $content = $this->_file_get_contents($this->_url);

    $dom = new Zend_Dom_Query($content);
    $results = $dom->query('#content tr');

    foreach ($results as $result)
    {
      $html = $result->C14N();
      $html = preg_replace('#<span class="google-src-text" (.*)>(.*)<\/span>#imsUu', '', $html);

      $d = new Zend_Dom_Query($html);
      $r = $d->query('td');

      $data = array();
      foreach ($r as $i =>$rl)
      {
          if ($i == 0)
          {
            $d2 = new Zend_Dom_Query($rl->C14N());
            $r2 = $d2->query('span > a');

            foreach ($r2 as $i2 =>$rl2)
            {
              $data['name'] =  utf8_decode(iconv("UTF-8", "ISO-8859-1", $rl2->nodeValue));
              $data['url'] = $rl2->getAttribute('href');
              $data['image_src'] = $this->_baseUrl . '/' . $rl2->getAttribute('rel');
            }
          }
          else
          {
            $d2 = new Zend_Dom_Query($rl->C14N());
            $r2 = $d2->query('span');

            foreach ($r2 as $i2 =>$rl2)
            {
              if ($i == 1) {
                $data['number'] = $rl2->nodeValue;
              }
              elseif ($i == 2) {
                $data['date'] = $rl2->nodeValue;
              }
            }
          }
      }

      $this->_loadPage($data);
    }
  }

  private function _loadPage($data)
  {
    $contentFile = file_get_contents($data['url']);
    $this->_dom = new Zend_Dom_Query($contentFile);

    $book = new Model_Book();
    $book->editor_id = 6;
    $book->editor_name = 'Soleil Manga';

    $book->name = $data['name'];
    $book->number = $data['number'];
    $book->published_at = $this->_date($data['date']);

    $book->text = $this->_text('#page');
    $book->state = $this->_state('#page');
    $book->author_name = $this->_author('#page');
    $book->price = $this->_price('#page');

    $book->image_src = $data['image_src'];

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

  private function _text($value)
  {
    $element = $this->_getElement($value);
    $html = $element->C14N();

    $html = strip_tags(preg_replace('#<span class="google-src-text" (.*)>(.*)<\/span>#imsU', '', $html));

    preg_match('#RÉSUMÉ SÉRIE(.*)AUTRES TOMES#ims', $html, $matches);

    if (!count($matches)) return null;

    return trim($matches[1]);
  }

  private function _state($value)
  {
    $element = $this->_getElement($value);
    $html = $element->C14N();

    //$html = strip_tags(preg_replace('#<span class="google-src-text" (.*)>(.*)<\/span><\/span>#imsU', '', $html));

    preg_match('#Nombre de tomes :(.*)<span (.*)>(.*)<\/span>#imsU', $html, $matches);

    if (!count($matches)) return null;

    return 'Nombre de tomes : ' . trim($matches[3]);
  }

  private function _author($value)
  {
    $element = $this->_getElement($value);
    $html = $element->C14N();

    //$html = strip_tags(preg_replace('#<span class="google-src-text" (.*)>(.*)<\/span><\/span>#imsU', '', $html));

    preg_match('#Dessinateur :(.*)<span (.*)>(.*)<\/span>#imsU', $html, $matches);

    if (!count($matches)) return null;

    return trim($matches[3]);
  }

  private function _price($value)
  {
    $element = $this->_getElement($value);
    $html = strip_tags($element->C14N());

    preg_match('#Prix : (.*) ? #imsU', $html, $matches);

    if (!count($matches)) return null;

    return trim($matches[1]);
  }

  private function _date($value)
  {
    $d = explode('/', $value);
    $date = new DateTime();

    $date->setDate($d[2], $d[1], $d[0]);
    return $date->format('Y-m-d') . ' 00:00:00';
  }
}