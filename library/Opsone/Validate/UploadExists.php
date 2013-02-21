<?php

class Opsone_Validate_UploadExists extends Zend_Validate_Abstract
{
  const DOES_NOT_EXIST = 'uploadExistsDoesNotExist';
  const IS_NOT_ALLOWED = 'uploadExistsIsNotAllowed';

  protected $_messageTemplates = array(
    self::DOES_NOT_EXIST => "File '%value%' does not exist",
    self::IS_NOT_ALLOWED => "File '%value%' is not allowed",
  );

  protected $_token;

  public function __construct($options = array())
  {
    if ($options instanceof Zend_Config) {
      $options = $options->toArray();
    }
    else if (!is_array($options)) {
      $options = func_get_args();
    }

    if (!array_key_exists('token', $options)) {
        $options['token'] = null;
    }

    $this->setToken($options['token']);
  }

  public function setToken($token)
  {
    $this->_token = $token;
  }

  public function getToken()
  {
    return $this->_token;
  }

  public function isValid($value)
  {
    if ($this->_token && $this->_token == $value) {
      return true;
    }

    if (!is_file($value))
    {
      $this->_error(self::DOES_NOT_EXIST);
      return false;
    }

    if (!preg_match('/^' . preg_quote(realpath(APPLICATION_PATH . '/../temp/uploads'), '/') . '/', $value))
    {
      $this->_error(self::IS_NOT_ALLOWED);
      return false;
    }

    return true;
  }
}
