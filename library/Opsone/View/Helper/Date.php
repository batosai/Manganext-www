<?php

class Opsone_View_Helper_Date extends Zend_View_Helper_Abstract
{
  private $_inputFormat = 'yyyy-MM-dd';

  public function date($date, $format = 'dd/MM/yyyy')
  {
    if ($date && Zend_Date::isDate($date, $this->_inputFormat))
    {
      $date = new Zend_Date($date, $this->_inputFormat);
      return $date->toString($format);
    }

    return $date;
  }
}
