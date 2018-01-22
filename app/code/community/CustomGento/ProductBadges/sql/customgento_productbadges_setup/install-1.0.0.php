<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'customgento_productbadges/badge_config'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('customgento_productbadges/badge_config'))
    ->addColumn('badge_config_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Badge Config Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => true,
        'default'   => null
        ), 'From Date')
    ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => true,
        'default'   => null
        ), 'To Date')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Active')
    ->addColumn('render_type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Render Type')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Conditions Serialized')
    ->addIndex($installer->getIdxName('customgento_productbadges/badge_config', array('is_active', 'to_date', 'from_date')),
        array('is_active', 'to_date', 'from_date'))
    ->setComment('CustomGento Product Badges Config');
$installer->getConnection()->createTable($table);

$installer->endSetup();