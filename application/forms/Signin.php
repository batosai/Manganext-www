<?php

class Form_Signin extends Form_Base
{
    public function init()
    {
      parent::init();

      $this->setAction( $this->_viewRenderer->url(array('controller' => 'session', 'action' => 'create'), 'default', true))
           ->setMethod('post')
           ->setAttrib('class', 'form-stacked')
           ->setTitle('Administration');

      $email =  $this->createElement('text', 'email', array('label' => 'Email'));
      $email->setRequired(true)
            ->addValidator('NotEmpty', true, array('messages' => 'email manquant'))
            ->setAttrib('class', 'medium')
            ->setDecorators(array('Email'));

      $password =  $this->createElement('password', 'password', array('label' => 'Mot de passe'));
      $password->setRequired(true)
               ->addValidator('NotEmpty', true, array('messages' => 'mot de passe manquant'))
               ->setAttrib('class', 'xlarge');

      $submit =  $this->createElement('submit', 'submit', array('label' => 'Connexion'));
      $submit->setAttrib('class', 'btn primary')
             ->setDecorators(array('Submit'));

      $this->addElement($email)
           ->addElement($password)
           ->addElement($submit);

      $this->defaultFilters();
    }
}