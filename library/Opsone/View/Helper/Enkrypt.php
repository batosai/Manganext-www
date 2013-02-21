<?php

class Opsone_View_Helper_Enkrypt extends Zend_View_Helper_Abstract
{
  private $_krypt;

  public function __construct()
  {
    $this->_krypt = Zend_Registry::get('Krypt');
  }

  public function enkrypt($data)
  {
    return $this->_krypt->encrypt($data);
  }
}