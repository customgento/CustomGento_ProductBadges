<?php
class CustomGento_ProductBadges_Model_Queue_Cron
{

    /**
     * @return CustomGento_ProductBadges_Model_Queue_Cron
     */
    public function processJobs()
    {
        $this->_getQueueResourceModel()->removeOldJobs();

        $pickedJobsCollection = $this->_getQueueCollection()
            ->filterPickedJobs();

        $runningJobsCollection = $this->_getQueueCollection()
            ->filterRunningJobs();

        // Lock mechanism in case crons start overlapping
        if (count($pickedJobsCollection) > 0 || count($runningJobsCollection) > 0) {
            return $this;
        }

        $queueCollection = $this->_getQueueCollection()
            ->filterNotProcessedJobs()
            ->load();

        // Mark as picked the jobs are part of locking mechanism
        $jobIds = $queueCollection->getAllIds();
        if (!empty($jobIds)) {
            $this->_getQueueResourceModel()->markJobsAsPicked($jobIds);
        }

        /** @var CustomGento_ProductBadges_Model_Queue $queueEntry */
        foreach ($queueCollection as $queueEntry) {
            $this->_getProcessModel()->attemptToProcessJob($queueEntry);
        }

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