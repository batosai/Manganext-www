<?php

class ServicesController extends Zend_Controller_Action
{
  private $_server;
  private $_version;

  public function init()
  {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $this->_version = $this->_getParam('v', 1);

    $this->_server = new Zend_Json_Server();
    
    if($this->_getParam('key') != 'ea5af636cd2c0c07242ee43c07cbefb3')
    {
      exit;
    }

    if(in_array($this->_getParam('format'),  array('json', 'jsonp')))
    {
      $jsonRequest = new Zend_Json_Server_Request();

      $jsonRequest->loadJson(
        json_encode(array('jsonrpc' => '2.0',
        'method' => $this->_getParam('method'),
        'params' => array_merge($this->getRequest()->getQuery(), $this->getRequest()->getPost()),
        'id' => time()// 0 = app desactivé.
      )));

      $this->_server->setRequest($jsonRequest);
    }

    if(!isset($jsonRequest) && !$this->_server->getRequest()->getRawJson())
    {
      $this->_server = new Zend_Rest_Server();
    }
  }

  public function booksAction()
  {
    if ($this->_getParam('v') == 1) {
      $this->_server->setClass('Service_BooksV1');
    }
    else {
      $this->_server->setClass('Service_BooksV2');
    }

    if ($this->_getParam('format') == 'json') {
      echo $this->_server->handle();
    }
    else //jsonp
    {
      $this->_server->setAutoEmitResponse(false);
      echo 'function MangaNext' . ucfirst($this->_getParam('method')) . '(){ return ' . $this->_server->handle() . ';}';
    }
  }
}
