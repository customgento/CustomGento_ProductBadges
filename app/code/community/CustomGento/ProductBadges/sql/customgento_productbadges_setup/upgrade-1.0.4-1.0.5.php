<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Add columns to table 'customgento_productbadges/badge_config'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('customgento_productbadges/badge_config'),
        'store_ids', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'length'    => 255,
            'default'   => '0',
            'comment'   => 'Store ids'
        ));

$installer->endSetup();