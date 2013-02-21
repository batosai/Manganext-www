<?php
/**
 * Bootstrap
 *
 * @package    Bootstrap
 * @author     Opsone <contact@opsone.net>
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
  protected function _initConstants()
  {
    define('UPLOADS_PATH', realpath(APPLICATION_PATH . '/../data/uploads'));
  }

  protected function _initResourceLoader()
  {
    $this->getResourceLoader()->addResourceType('behavior', 'models/behaviors', 'Behavior');
  }

  protected function _initCache()
  {
    $this->bootstrap('cachemanager');
    $cache = $this->getResource('cachemanager')->getCache('default');

    Zend_Locale::setCache($cache);
    Zend_Locale_Format::setOptions(array('cache' => $cache));
    Zend_Date::setOptions(array('cache' => $cache));

    return $cache;
  }

  protected function _initConfig()
  {
    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini', $this->getEnvironment(), array('allowModifications' => true));

    return $config;
  }
  
  protected function _initKrypt()
  {
    return new Opsone_Krypt(';92(hl#0f-h]K.!_Fd@zsBW(l');
  }

  protected function _initViewHelpers()
  {
    $this->bootstrap('view');

    $view = $this->getResource('view');
    $view->addHelperPath('Opsone/View/Helper', 'Opsone_View_Helper');
  }

  protected function _initRegistry()
  {
    $this->bootstrap('cache');
    $this->bootstrap('log');
    $this->bootstrap('krypt');
    $this->bootstrap('config');

    Zend_Registry::set('Zend_Cache', $this->getResource('cache'));
    Zend_Registry::set('Zend_Log', $this->getResource('log'));
    Zend_Registry::set('Krypt', $this->getResource('krypt'));
    Zend_Registry::set('Zend_Config', $this->getResource('config'));
  }
  
  protected function _initSecureSession()
  {
    $this->bootstrap('session');
    $session = new Zend_Session_Namespace();

    if ($session->initialized === null)
    {
      Zend_Session::regenerateId();
      $session->initialized = true;
    }

    return $session;
  }

  protected function _initZFDebug()
  {
    $this->bootstrap('doctrine');
    $this->bootstrap('frontController');

    $options = array(
      'plugins' => array(
        'Html',
        'Exception',
        'File' => array(
          'base_path' => APPLICATION_PATH,
          'library' => array(
            'Opsone',
            'Doctrine',
            'ZFDoctrine'
          )
        ),
        'Variables',
        'Memory',
        'Time',
        'Log',
        'Doctrine'
      ),
    );

    if ($this->hasResource('db'))
    {
      $this->bootstrap('db');
      $db = $this->getResource('db');
      $options['plugins']['Database']['adapter'] = $db;
    }

    if ($this->hasResource('cache'))
    {
      $this->bootstrap('cache');
      $cache = $this->getResource('cache');
      $options['plugins']['Cache']['backend'] = $cache->getBackend();
    }

    if ($this->getEnvironment() == 'development')
    {
      $autoloader = Zend_Loader_Autoloader::getInstance();
      $autoloader->registerNamespace('ZFDebug');

      $debug = new ZFDebug_Controller_Plugin_Debug($options);

      $frontController = $this->getResource('frontController');
      $frontController->registerPlugin($debug);
    }
  }
}

