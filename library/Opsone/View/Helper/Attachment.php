<?php

class Opsone_View_Helper_Attachment extends Zend_View_Helper_Abstract
{
  public function attachment(Doctrine_Record $record, $field, $dimensions = null)
  {
    if (is_null($dimensions)) {
      return $this->view->url(array('controller' => 'attachments', 'action' => 'get', 'file' => $this->view->enkrypt($record->{$field}), 'name' => basename($record->{$field})), 'default', true);
    }

    return $this->view->url(array('controller' => 'attachments', 'action' => 'image', 'file' => $this->view->enkrypt($record->{$field}), 'dimensions' => $dimensions, 'name' => basename($record->{$field})), 'default', true);
  }
}
