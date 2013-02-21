<?php
/**
 * IndexController
 *
 * @package    Controllers
 * @subpackage Default
 * @author     Opsone <contact@opsone.net>
 */
class IndexController extends Zend_Controller_Action
{
    private $_table;

    public function init()
    {
      $this->_table = Model_ApnsDeviceTable::getInstance();

      $this->view->breadcrumb = array(
        array(
          'name' => 'Accueil',
          'active' => true
        )
      );
    }

    public function indexAction()
    {
      $q = $this->_table->createQuery('t')->select('t.*')
                                          ->orderBy('t.created DESC');

      $paginator = new Zend_Paginator(new ZFDoctrine_Paginator_Adapter_DoctrineQuery($q));
      $paginator->setCurrentPageNumber($this->_getParam('page'));
      $paginator->setItemCountPerPage(20);

      $this->view->paginator = $paginator;
    }

}

