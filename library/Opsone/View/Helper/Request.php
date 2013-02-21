<?php

class Opsone_View_Helper_Request extends Zend_View_Helper_Abstract
{
  public function request()
  {
    $frontController = Zend_Controller_Front::getInstance();

    return $frontController->getRequest();
  }
}
