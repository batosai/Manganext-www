<?php

/**
 * Model_Base_Tracking
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $id
 * @property string $name
 * @property string $system_name
 * @property string $model
 * @property string $system_version
 * @property string $localized_model
 * @property string $locale
 * @property string $network
 * @property integer $launch
 * @property string $version
 * @property timestamp $last_at
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Model_Base_Tracking extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('trackings');
        $this->hasColumn('id', 'string', 255, array(
             'type' => 'string',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '255',
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('system_name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('model', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('system_version', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('localized_model', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('locale', 'string', 10, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '10',
             ));
        $this->hasColumn('network', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('launch', 'integer', 8, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '8',
             ));
        $this->hasColumn('version', 'string', 20, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '20',
             ));
        $this->hasColumn('last_at', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => false,
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}