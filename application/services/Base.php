<?php

class Service_Base
{
  protected $_view;

  public function __construct()
  {
    $this->_view = new Zend_View();
    $this->_view->setScriptPath(APPLICATION_PATH . '/views/mails');
    $this->_view->addHelperPath(APPLICATION_PATH . '/views/helpers');
    $this->_view->addHelperPath('Opsone/View/Helper', 'Opsone_View_Helper');
  }
}