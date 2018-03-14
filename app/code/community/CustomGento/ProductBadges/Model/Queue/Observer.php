<?php
class CustomGento_ProductBadges_Model_Queue_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerBadgeChange(Varien_Event_Observer $observer)
    {
        $queueRegisterJob = new CustomGento_ProductBadges_Model_Queue_RegisterJob();
        $badgeConfig = $observer->getBadgeConfig();

        $job = new CustomGento_ProductBadges_Model_Queue_Job_BadgeUpdate();

        $queueRegisterJob->attemptToRegisterJob($job, $badgeConfig);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerStoreDelete(Varien_Event_Observer $observer)
    {
        $queueRegisterJob = new CustomGento_ProductBadges_Model_Queue_RegisterJob();

        /** @var Mage_Core_Model_Store $store */
        $store = $observer->getEvent()->getStore();

        $job = new CustomGento_ProductBadges_Model_Queue_Job_StoreDelete();

        $queueRegisterJob->attemptToRegisterJob($job, $store);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerStoreGroupDelete(Varien_Event_Observer $observer)
    {
        $queueRegisterJob = new CustomGento_ProductBadges_Model_Queue_RegisterJob();

        /** @var Mage_Core_Model_Store_Group $store */
        $storeGroup = $observer->getEvent()->getStoreGroup();

        $job = new CustomGento_ProductBadges_Model_Queue_Job_StoreGroupDelete();

        $queueRegisterJob->attemptToRegisterJob($job, $storeGroup);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerProductUpdate(Varien_Event_Observer $observer)
    {
        $queueRegisterJob = new CustomGento_ProductBadges_Model_Queue_RegisterJob();

        /** @var Mage_Catalog_Model_Product $store */
        $product = $observer->getEvent()->getProduct();

        $job = new CustomGento_ProductBadges_Model_Queue_Job_ProductUpdate();

        $queueRegisterJob->attemptToRegisterJob($job, $product);
    }

}