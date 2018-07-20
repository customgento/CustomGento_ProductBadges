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
        'badge_image', array(
            'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'length'   => 255,
            'comment'  => 'Badge Image'
        )
    );

$installer->endSetup();
