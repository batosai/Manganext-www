<?php

class Opsone_Validate_Uri extends Zend_Validate_Abstract
{
  const INVALID_URI = 'uriInvalid';

  protected $_messageTemplates = array(
    self::INVALID_URI => 'Invalid uri given'
  );

  public function isValid($uri)
  {
    if (!Zend_Uri::check($uri))
    {
      $this->_error(self::INVALID_URI);
      return false;
    }

    return true;
  }
}
