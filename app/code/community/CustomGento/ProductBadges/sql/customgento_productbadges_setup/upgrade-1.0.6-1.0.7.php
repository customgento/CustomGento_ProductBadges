<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'customgento_productbadges/badge_config'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customgento_productbadges/queue'))
    ->addColumn('job_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Queue Job Id')
    ->addColumn('processor_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Alias of a class that will process a job')
    ->addColumn('job_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Description')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Job Creation Time')
    ->addColumn('status',  Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Status')
    ->setComment('CustomGento Product Badges Queue');
$installer->getConnection()->createTable($table);

$installer->endSetup();