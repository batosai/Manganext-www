<?php

class Zend_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
  private $_messages;
  private $_type;

  public function __construct()
  {
    $this->_messages = array();

    $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

    $datas = $flashMessenger->hasCurrentMessages() ? $flashMessenger->getCurrentMessages() : $flashMessenger->getMessages();

    foreach ($datas as $data)
    {
        if ($data instanceof Zend_Form)
        {
            $this->_messages = $this->_arrayFlatten($data->getMessages());
            $this->_type = Zend_Log::ERR;

            if(!count($this->_messages))
            {
              $this->_messages[] = $data->getSuccess();
              $this->_type = Zend_Log::INFO;
            }
        }
        elseif(is_array($data))
        {
          $this->_type = Zend_Log::INFO;
          if(count($data) > 1) {
            $this->_type = $data[0];
          }
          $this->_messages[] = $data[1];
        }
        else {
          $this->_messages[] = $data;
          $this->_type = Zend_Log::INFO;
        }
    }

    $flashMessenger->clearCurrentMessages();
    $flashMessenger->clearMessages();
  }

  public function flashMessenger()
  {
    $html = $htm = '';

    if ($this->_messages)
    {
      switch ($this->_type)
      {
        case Zend_Log::ERR:
          $class = 'alert-error';
          $icon = 'icon';
          break;
        case Zend_Log::WARN:
          $class = 'alert-error';
          $icon = 'icon';
          break;
        case Zend_Log::NOTICE:
          $class = 'alert-success';
          $icon = 'icon';
          break;
        default:
          $class = 'alert-info';
          $icon = 'icon';
      }

      foreach ($this->_messages as $priority => $message)
      {
        $htm .= $this->view->escape($message) . '<br />';
        $htm .= '<div class="'.$icon.'"></div>';
      }
      $html  = '<div class="alert ' . $class . '" >';
      $html .= $htm;
      $html .= '</div>';
    }

    return $html;
  }

  private function _arrayFlatten($a, $f = array(), $key = null)
  {
    if (!$a || !is_array($a)) return '';

    foreach ($a as $k => $v)
    {
      if (is_array($v)) $f = $this->_arrayFlatten($v, $f, $k);
      else $f[$key . '_' . $k] = $v;
    }

    return $f;
  }
}
