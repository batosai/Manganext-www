<?php

class Service_BooksV1 extends Service_Base
{
  const VERSION = '1.0';
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

  public function get($updated_at = null)
  {
    $q = $this->_table->createQuery('b')->select('b.*')->orderBy('published_at ASC');

    if ($updated_at) {
      $q->where('updated_at > ?', $updated_at);
    }

    if ($rows = $q->execute())
    {
      return $rows->toArray();
    }
  }

  public function getNew($updated_at = null)
  {
    $q = $this->_table->createQuery('b')->select('b.*')
                                        ->where('published_at <= NOW()')
                                        ->andWhere("TIMESTAMPDIFF(MONTH, published_at, NOW()) < 2")
                                        ->andWhere('name != ""')
                                        ->orderBy('published_at DESC');

    if ($updated_at) {
      $q->andWhere('updated_at > ?', $updated_at);
    }

    if ($rows = $q->execute())
    {
      return $rows->toArray();
    }
  }

  public function getFuture($updated_at = null)
  {
    $q = $this->_table->createQuery('b')->select('b.*')
                                        ->where('published_at > NOW()')
                                        ->andWhere('name != ""')
                                        ->orderBy('published_at ASC');

    if ($updated_at) {
      $q->andWhere('updated_at > ?', $updated_at);
    }

    if ($rows = $q->execute())
    {
      return $rows->toArray();
    }
  }
}//http://localhost/opsone/manga_www/services/books?method=get&format=json&key=ea5af636cd2c0c07242ee43c07cbefb3&v=1