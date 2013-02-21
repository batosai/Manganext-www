<?php

class Opsone_Validate_EmailAddress extends Zend_Validate_EmailAddress
{
  public function getMessages()
  {
    if ($this->_messages) {
      $this->_messages = array(self::INVALID => $this->_messageTemplates[self::INVALID]);
    }

    return $this->_messages;
  }
}
