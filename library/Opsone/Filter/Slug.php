<?php

class Opsone_Filter_Slug implements Zend_Filter_Interface
{
  public function filter($value)
  {
    $value = preg_replace(array('/[ÀÁÂÃÄÅ]/u', '/[àáâãäå]/u'), array('A', 'a'), $value);
    $value = preg_replace(array('/[ÒÓÔÕÖØ]/u', '/[òóôõöø]/u'), array('O', 'o'), $value);
    $value = preg_replace(array('/[ÈÉÊË]/u', '/[èéêë]/u'), array('E', 'e'), $value);
    $value = preg_replace(array('/[ÌÍÎÏ]/u', '/[ìíîï]/u'), array('I', 'i'), $value);
    $value = preg_replace(array('/[ÙÚÛÜ]/u', '/[ùúûü]/u'), array('U', 'u'), $value);
    $value = preg_replace(array('/Ÿ/u', '/ÿ/u'), array('Y', 'y'), $value);
    $value = preg_replace(array('/Ñ/u','/ñ/u'), array('N','n'), $value);
    $value = preg_replace(array('/[^\w]/i', '/[\s]/', '/-+/'), '-', $value);
    $value = preg_replace('/-$/', '', $value);
    $value = mb_strtolower($value);

    return $value;
  }
}
