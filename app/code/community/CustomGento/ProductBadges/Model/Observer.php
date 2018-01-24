<?php

class CustomGento_ProductBadges_Model_Observer
{

    public function customgentoProductbadgesBadgeConfigSaveAfter()
    {
        if (Mage::helper('customgento_productbadges')->shouldAutomaticallyReindexAfterBadgeSave()) {
            Mage::getResourceModel('customgento_productbadges/indexer_productBadges')->rebuild();
        }
    }
}
