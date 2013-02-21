<?php
/**
 * ApnsController
 *
 * @package    Controllers
 * @subpackage Default
 * @author     Opsone <contact@opsone.net>
 */
class ApnsController extends Zend_Controller_Action
{
    private $_table;

    public function init()
    {
      $this->_table = Model_ApnsDeviceTable::getInstance();
    }

    public function indexAction()
    {
      $args = (!empty($_GET)) ? $_GET:array('task'=>$argv[1]);
      $apns = new Easyapns_APNS($args);

      if (isset($_GET['deviceuid'], $_GET['locale'])) {
        $app = $this->_table->findOneByDeviceuid($_GET['deviceuid']);
        $app->launch += 1;
        $app->locale = $_GET['locale'];
        $app->save();
      }

      exit;
    }
}

