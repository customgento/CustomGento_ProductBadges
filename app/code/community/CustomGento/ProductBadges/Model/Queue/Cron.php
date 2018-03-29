<?php
class CustomGento_ProductBadges_Model_Queue_Cron
{

    /**
     * @return CustomGento_ProductBadges_Model_Queue_Cron
     */
    public function processJobs()
    {
        $queueCollection = $this->_getQueueCollection()
            ->filterNotProcessedJobs();

        /** @var CustomGento_ProductBadges_Model_Queue $queueEntry */
        foreach ($queueCollection as $queueEntry) {
            $this->_getProcessModel()->attemptToProcessJob($queueEntry);
        }

        $this->_getQueueResourceModel()->removeOldJobs();

        return $this;
    }

    /**
     * @return CustomGento_ProductBadges_Model_Queue_ProcessJob
     */
    protected function _getProcessModel()
    {
        return Mage::getSingleton('customgento_productbadges/queue_processJob');
    }

    /**
     * @return CustomGento_ProductBadges_Model_Resource_Queue_Collection
     */
    protected function _getQueueCollection()
    {
        return Mage::getModel('customgento_productbadges/queue')
            ->getCollection();
    }

    /**
     * @return CustomGento_ProductBadges_Model_Resource_Queue
     */
    protected function _getQueueResourceModel()
    {
        return Mage::getModel('customgento_productbadges/queue')
            ->getResource();
    }

}