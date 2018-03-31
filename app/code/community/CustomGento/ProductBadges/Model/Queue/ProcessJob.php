<?php
class CustomGento_ProductBadges_Model_Queue_ProcessJob
{

    /**
     * @param CustomGento_ProductBadges_Model_Queue $queueEntry
     */
    public function attemptToProcessJob(CustomGento_ProductBadges_Model_Queue $queueEntry)
    {
        /**
         * Mark a job as running
         */
        $queueEntry->setStatus(CustomGento_ProductBadges_Model_Queue_Job::STATUS_RUNNING);
        $queueEntry->setExecutedAt(Mage::getSingleton('core/date')->gmtDate());
        $queueEntry->save();


        try {
            $processorModel = $queueEntry->getProcessorModel();

            /** @var CustomGento_ProductBadges_Model_Queue_Job_Abstract $processorModel */
            $processorModel = Mage::getModel($processorModel);
            $processorModel->processJobAction(unserialize($queueEntry->getJobData()));

            /**
             * Mark a job as successfully executed
             */
            $queueEntry->setStatus(CustomGento_ProductBadges_Model_Queue_Job::STATUS_SUCCESS);
            $queueEntry->setFinishedAt(Mage::getSingleton('core/date')->gmtDate());
            $queueEntry->save();
        } catch (Exception $e) {
            /**
             * Mark a job as failed
             */
            $queueEntry->setStatus(CustomGento_ProductBadges_Model_Queue_Job::STATUS_ERROR);
            $queueEntry->setFinishedAt(Mage::getSingleton('core/date')->gmtDate());
            $queueEntry->save();
            Mage::logException($e);
        }

    }

}