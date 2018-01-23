<?php

class CustomGento_ProductBadges_Model_Observer extends Varien_Event_Observer
{

    public function reindex()
    {
        if (Mage::helper('customgento_productbadges')->shouldAutomaticallyReindexAfterBadgeSave()) {
            Mage::getResourceModel('customgento_productbadges/indexer_productBadges')->rebuild();
        }
    }
}
