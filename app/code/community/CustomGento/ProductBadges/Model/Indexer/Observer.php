<?php

class CustomGento_ProductBadges_Model_Indexer_Observer
{

    const QUEUE_LAST_JOB_ID_GLOBAL_VARIABLE_NAME = 'before_badge_index_start_last_queue_job_id';

    /**
     * @param Varien_Event_Observer $observer
     * @return CustomGento_ProductBadges_Model_Queue_Observer
     */
    public function beforeIndexerStart(Varien_Event_Observer $observer)
    {
        /** @var CustomGento_ProductBadges_Model_Queue $queueRegisterJob */
        $queue = Mage::getModel('customgento_productbadges/queue');

        /** @var CustomGento_ProductBadges_Model_Resource_Queue_Collection $jobsCollection */
        $jobsCollection = $queue->getCollection()
                            ->setOrder('job_id', Varien_Data_Collection_Db::SORT_ORDER_DESC);

        /** @var CustomGento_ProductBadges_Model_Queue $queueJob */
        $queueJob = $jobsCollection->getFirstItem();

        $jobId = $queueJob->getId();

        if (!empty($jobId)) {
            Mage::register(self::QUEUE_LAST_JOB_ID_GLOBAL_VARIABLE_NAME, $jobId);
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return CustomGento_ProductBadges_Model_Queue_Observer
     */
    public function afterIndexerFinish(Varien_Event_Observer $observer)
    {
        $lastQueueJobIdBeforeIndexStart = Mage::registry(self::QUEUE_LAST_JOB_ID_GLOBAL_VARIABLE_NAME);

        if (!empty($lastQueueJobIdBeforeIndexStart)) {
            /** @var CustomGento_ProductBadges_Model_Queue $queueRegisterJob */
            $queue = Mage::getModel('customgento_productbadges/queue');

            /** @var CustomGento_ProductBadges_Model_Resource_Queue $queueResource */
            $queueResource = $queue->getResource();

            $queueResource->removeJobsOlderThanJobId($lastQueueJobIdBeforeIndexStart);
        }

        $this->_getCacheModel()->clearAllBadgeCache();

        return $this;
    }

    /**
     * @return CustomGento_ProductBadges_Model_Cache
     */
    protected function _getCacheModel()
    {
        return Mage::getModel('customgento_productbadges/cache');
    }

}
