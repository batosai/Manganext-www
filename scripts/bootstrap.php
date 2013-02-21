<?php

// Define path to application directory
defined('APPLICATION_PATH')
  || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Ensure library/ is on include_path
set_include_path(realpath(APPLICATION_PATH . '/../library'));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// Define some CLI options
$getopt = new Zend_Console_Getopt(array(
  'env|e=w' => 'Application environment (development or production)',
  'help|h' => 'Help',
));

try {
  $getopt->parse();
}
catch (Zend_Console_Getopt_Exception $e)
{
  // Bad options passed: report usage
  echo $e->getUsageMessage();
  exit;
}

// If help requested, report usage message
if ($getopt->getOption('h'))
{
  echo $getopt->getUsageMessage();
  exit;
}

if (!$getopt->getOption('e'))
{
  echo $getopt->getUsageMessage();
  exit;
}

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', $getopt->getOption('e'));

// Create application, bootstrap, and run
$application = new Zend_Application(
  APPLICATION_ENV,
  APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = 80;

ini_set('display_startup_errors', '1');
ini_set('display_errors', '1');

$front = $application->getBootstrap()->getResource('frontcontroller');
$front->getRouter()->addDefaultRoutes();