<?php

class Opsone_View_Helper_Datetime extends Zend_View_Helper_Abstract
{
  private $_inputFormat = 'yyyy-MM-dd HH:mm:ss';

  public function datetime($date, $format = 'dd/MM/yyyy HH:mm')
  {
    if ($date && Zend_Date::isDate($date, $this->_inputFormat))
    {
      $date = new Zend_Date($date, $this->_inputFormat);
      return $date->toString($format);
    }

    return $date;
  }
}
