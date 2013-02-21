<?php

/**
 * Model_Base_ApnsMessage
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $pid
 * @property integer $fk_device
 * @property string $message
 * @property timestamp $delivery
 * @property enum $status
 * @property timestamp $created
 * @property timestamp $modified
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Model_Base_ApnsMessage extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('apns_messages');
        $this->hasColumn('pid', 'integer', 8, array(
             'type' => 'integer',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '8',
             ));
        $this->hasColumn('fk_device', 'integer', 8, array(
             'type' => 'integer',
             'unsigned' => true,
             'notnull' => true,
             'length' => '8',
             ));
        $this->hasColumn('message', 'string', 25, array(
             'type' => 'string',
             'default' => 'NULL',
             'length' => '25',
             ));
        $this->hasColumn('delivery', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));
        $this->hasColumn('status', 'enum', null, array(
             'type' => 'enum',
             'fixed' => 1,
             'values' => 
             array(
              0 => 'queued',
              1 => 'delivered',
              2 => 'failed',
             ),
             'notnull' => true,
             'default' => 'active',
             ));
        $this->hasColumn('created', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));
        $this->hasColumn('modified', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             'default' => '0000-00-00 00:00:00',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}