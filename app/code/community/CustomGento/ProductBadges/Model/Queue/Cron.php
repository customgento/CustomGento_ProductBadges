<?php
class CustomGento_ProductBadges_Model_Queue_Cron
{

    public function processJobs()
    {
        $queueCollection = Mage::getModel('customgento_productbadges/queue')
            ->getCollection();

        /** @var CustomGento_ProductBadges_Model_Queue $queueEntry */
        foreach ($queueCollection as $queueEntry) {
            $this->_getProcessModel()->attemptToProcessJob($queueEntry);
        }

    }

    /**
     * @return CustomGento_ProductBadges_Model_Queue_ProcessJob
     */
    protected function _getProcessModel()
    {
        return Mage::getSingleton('customgento_productbadges/queue_processJob');
    }

}