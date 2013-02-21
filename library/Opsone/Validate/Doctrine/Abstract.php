<?php

abstract class Opsone_Validate_Doctrine_Abstract extends Zend_Validate_Db_Abstract
{
  public function setAdapter($adapter)
  {
    if (!($adapter instanceof Doctrine_Connection)) {
      throw new Zend_Validate_Exception('Adapter option must be an instance of Doctrine_Connection');
    }

    $this->_adapter = $adapter;
    return $this;
  }

  public function getAdapter()
  {
    if ($this->_adapter === null)
    {
      $this->_adapter = Doctrine_Manager::getInstance()->getConnectionForComponent($this->_table);
      if (null === $this->_adapter) {
        throw new Zend_Validate_Exception('No database adapter present');
      }
    }

    return $this->_adapter;
  }

  protected function _query($value)
  {
    $adapter = $this->getAdapter();

    $q = Doctrine_Query::create($adapter)->select($this->_field)
                                         ->from($this->_table)
                                         ->where($this->_adapter->quoteIdentifier($this->_field) . ' = ?', $value);
    if (null !== $this->_exclude)
    {
      if (is_array($this->_exclude)) {
        $q->addWhere($this->_adapter->quoteIdentifier($this->_exclude['field']) . ' != ?', $this->_exclude['value']);
      } else {
        $q->addWhere($this->_exclude);
      }
    }

    $q->limit(1);

    return $q->fetchOne();
  }
}
