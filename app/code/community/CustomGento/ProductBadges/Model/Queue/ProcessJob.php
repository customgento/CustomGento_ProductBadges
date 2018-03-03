<?php
class CustomGento_ProductBadges_Model_Queue_ProcessJob
{

    /**
     * @param CustomGento_ProductBadges_Model_Queue $queueEntry
     */
    public function attemptToProcessJob(CustomGento_ProductBadges_Model_Queue $queueEntry)
    {
        $processorClassName = $queueEntry->getProcessorModel();

        /** @var CustomGento_ProductBadges_Model_Queue_Job_Abstract $processorModel */
        $processorModel = new $processorClassName();
        $processorModel->processJobAction(unserialize($queueEntry->getJobData()));
    }

}