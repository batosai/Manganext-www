<?php

/**
 * Model_BookTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Model_BookTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object Model_BookTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Model_Book');
    }
}