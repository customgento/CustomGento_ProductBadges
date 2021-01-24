<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->dropColumn(
        $installer->getTable('customgento_productbadges/badge_config'),
        'internal_code'
    );

$installer->endSetup();
