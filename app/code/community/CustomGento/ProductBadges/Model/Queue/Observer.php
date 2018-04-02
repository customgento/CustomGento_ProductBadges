<?php
class CustomGento_ProductBadges_Model_Queue_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerBadgeChange(Varien_Event_Observer $observer)
    {
        /** @var CustomGento_ProductBadges_Model_Queue_RegisterJob $queueRegisterJob */
        $queueRegisterJob = Mage::getModel('customgento_productbadges/queue_registerJob');
        $badgeConfig = $observer->getBadgeConfig();

        /** @var CustomGento_ProductBadges_Model_Queue_Job_BadgeUpdate $job */
        $job = Mage::getModel('customgento_productbadges/queue_job_badgeUpdate');;

        $queueRegisterJob->attemptToRegisterJob($job, $badgeConfig);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerStoreDelete(Varien_Event_Observer $observer)
    {
        /** @var CustomGento_ProductBadges_Model_Queue_RegisterJob $queueRegisterJob */
        $queueRegisterJob = Mage::getModel('customgento_productbadges/queue_registerJob');

        /** @var Mage_Core_Model_Store $store */
        $store = $observer->getEvent()->getStore();

        /** @var CustomGento_ProductBadges_Model_Queue_Job_StoreDelete $job */
        $job = Mage::getModel('customgento_productbadges/queue_job_storeDelete');

        $queueRegisterJob->attemptToRegisterJob($job, $store);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerStoreGroupDelete(Varien_Event_Observer $observer)
    {
        /** @var CustomGento_ProductBadges_Model_Queue_RegisterJob $queueRegisterJob */
        $queueRegisterJob = Mage::getModel('customgento_productbadges/queue_registerJob');

        /** @var Mage_Core_Model_Store_Group $store */
        $storeGroup = $observer->getEvent()->getStoreGroup();

        /** @var CustomGento_ProductBadges_Model_Queue_Job_StoreGroupDelete $job */
        $job = Mage::getModel('customgento_productbadges/queue_job_storeGroupDelete');

        $queueRegisterJob->attemptToRegisterJob($job, $storeGroup);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerProductUpdate(Varien_Event_Observer $observer)
    {
        /** @var CustomGento_ProductBadges_Model_Queue_RegisterJob $queueRegisterJob */
        $queueRegisterJob = Mage::getModel('customgento_productbadges/queue_registerJob');

        /** @var Mage_Catalog_Model_Product $store */
        $product = $observer->getEvent()->getProduct();

        /** @var CustomGento_ProductBadges_Model_Queue_Job_ProductUpdate $job */
        $job = Mage::getModel('customgento_productbadges/queue_job_productUpdate');

        $queueRegisterJob->attemptToRegisterJob($job, $product);
    }

}