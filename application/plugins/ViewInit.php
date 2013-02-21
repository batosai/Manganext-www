<?php
/**
 * Plugin_ViewInit
 *
 * @package    Plugins
 * @subpackage Default
 * @author     Opsone <contact@opsone.net>
 */
 
class Plugin_ViewInit extends Zend_Controller_Plugin_Abstract
{
  public function preDispatch(Zend_Controller_Request_Abstract $request)
  {
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    $viewRenderer->view->moduleName = $request->getModuleName();
    $viewRenderer->view->controllerName = $request->getControllerName();
    $viewRenderer->view->actionName = $request->getActionName();
  }
}