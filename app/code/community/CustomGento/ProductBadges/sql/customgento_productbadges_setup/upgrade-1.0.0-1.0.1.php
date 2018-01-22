<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Add column to table 'customgento_productbadges/badge_config'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('customgento_productbadges/badge_config'),
        'render_container' ,array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'length'    => 255,
            'comment'   => 'Render Container'
        ));

$installer->getConnection()
    ->addColumn(
        $installer->getTable('customgento_productbadges/badge_config'),
        'internal_code' ,array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'length'    => 255,
            'after'     => 'badge_config_id',
            'comment'   => 'Internal Code'
        ));

$installer->endSetup();