<?php

class Opsone_View_Helper_CutString extends Zend_View_Helper_Abstract
{
  public function cutString($text, $max=100)
  {
    $text = strip_tags($text);
    if (mb_strlen($text) > $max)
    {
      $text = mb_substr($text, 0, $max);
      $last_space = mb_strrpos($text, ' ');
      $text = mb_substr($text, 0, $last_space) . '...';
    }

    return $text;
  }
}
