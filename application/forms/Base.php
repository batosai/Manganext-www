<?php

class Form_Base extends Zend_Form
{
  protected $_title;
  protected $_success;
  protected $_viewRenderer;
  protected $_config;

  public function init()
  {
    $this->_viewRenderer = $this->getView();
    $this->_success = '';

    $this->setDisableLoadDefaultDecorators(true);

    $this->addPrefixPath('Form_Decorator',APPLICATION_PATH.'/forms/decorators','decorator');
    $this->addElementPrefixPath('Form_Decorator',APPLICATION_PATH.'/forms/decorators','decorator');

    $this->defaultDecorators();
  }

  protected function defaultDecorators()
  {
    $this->setDecorators(array('FormCustom'))
         ->setElementDecorators(array('ElementCustom'));
  }

  protected function defaultFilters()
  {
    $this->setElementFilters(array(
      'StripTags',
      'StringTrim',
      array('Null', Zend_Filter_Null::STRING)
    ));
  }
  
  public function createElement($type, $name, $options = null)
  {
    $element =  parent::createElement($type, $name, $options);

    $element->setDecorators(array('ElementCustom'));

    return $element;
  }

  public function getTitle()
  {
    return $this->_title;
  }

  public function setTitle($title)
  {
    $this->_title = $title;

    return $this;
  }

  public function getSuccess()
  {
    return $this->_success;
  }

  public function setSuccess($val)
  {
    $this->_success = $val;

    return $this;
  }
}
