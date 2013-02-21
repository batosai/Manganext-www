<?php
/**
 * Plugin_Auth
 *
 * @package    Plugins
 * @author     Opsone <contact@opsone.net>
 */
class Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
  public function preDispatch(Zend_Controller_Request_Abstract $request)
  {
    if ($request->getControllerName() != 'apns')
    {
      $auth = Zend_Auth::getInstance();
      $auth->setStorage(new Zend_Auth_Storage_Session('Zend_Auth'));

      $controllers = array('error', 'session', 'services');

      if (!in_array($request->getControllerName(), $controllers) && !$auth->hasIdentity())
      {

        $request->setControllerName('session')
                ->setActionName('new')
                ->setDispatched(false);

        return;
      }

      if ($auth->hasIdentity())
      {
        $user = Model_AdminUserTable::getInstance()->find($auth->getIdentity());

        $request->setParam('currentUser', $user);

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->view->currentUser = $user;
      }
    }
  }
}
