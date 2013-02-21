<?php

class SessionController extends Zend_Controller_Action
{
  private $_auth;
  private $_table;

  public function init()
  {
    $this->_auth = Zend_Auth::getInstance();
    $this->_table = Model_AdminUserTable::getInstance();
  }

  public function newAction()
  {
    $this->view->form = new Form_Signin();
  }

  public function createAction()
  {
    $email = $this->_request->getPost('email');
    $password = $this->_request->getPost('password');

    $user = $this->_table->fetchOneByCredentials($email, $password);

    if ($user)
    {
      $this->_auth->getStorage()->write($user->id);

      $this->_helper->redirector('index', 'index', 'index');

      return;
    }
    else {
       $result = new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $email);
    }

    if ($result->isValid()) {
      $this->_helper->redirector('index', 'index');
    }

    $this->_helper->flashMessenger(array(Zend_Log::ERR, 'Identification impossible'));

    $this->view->form = new Form_Signin();
    $this->view->form->email->setValue($email);

    $this->render('new');
  }

  public function destroyAction()
  {
    $this->_auth->clearIdentity();

    $this->_helper->redirector('new');
  }
}
