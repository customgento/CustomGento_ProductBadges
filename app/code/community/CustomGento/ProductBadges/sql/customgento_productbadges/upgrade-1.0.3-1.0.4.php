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
        'badge_background_color', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'length'    => 255,
            'comment'   => 'Badge Background Color'
        ));

$installer->getConnection()
    ->addColumn(
        $installer->getTable('customgento_productbadges/badge_config'),
        'badge_text_color', array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => true,
            'length'    => 255,
            'comment'   => 'Badge Text Color'
        ));

$installer->endSetup();