<?php

class CustomGento_ProductBadges_Model_Observer
{

    public function customgentoProductbadgesBadgeConfigSaveCommitAfter()
    {
        if (Mage::helper('customgento_productbadges')->shouldAutomaticallyReindexAfterBadgeSave()) {
            Mage::getResourceModel('customgento_productbadges/indexer_productBadges')->rebuild();
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return CustomGento_ProductBadges_Model_Observer
     */
    public function processStoreDelete(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Model_Store $store */
        $store = $observer->getEvent()->getStore();

        $this->_getProductBadgesIndexerResource()
            ->dropIndexTableForStore($store);

        return $this;
    }

    /**
     * @return CustomGento_ProductBadges_Model_Resource_Indexer_ProductBadges
     */
    protected function _getProductBadgesIndexerResource()
    {
        return Mage::getResourceModel('customgento_productbadges/indexer_productBadges');
    }

}
