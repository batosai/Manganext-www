<?php

class Service_BooksV2 extends Service_Base
{
  const VERSION = '2.0';
  private $_table;

  public function __construct()
  {
    parent::__construct();
    $this->_table = Model_BookTable::getInstance();
  }

  public function getVersion()
  {
    return self::VERSION;
  }

  public function get()
  {
    $q = $this->_table->createQuery('b')->select('b.*')
      ->where("TIMESTAMPDIFF(MONTH, published_at, NOW()) > -1")
      ->andWhere("TIMESTAMPDIFF(MONTH, published_at, NOW()) < 2")
      ->andWhere('name != ""')
      ->orderBy('published_at ASC')
    ;

    if ($rows = $q->execute()) {
      return $rows->toArray();
    }
  }
}