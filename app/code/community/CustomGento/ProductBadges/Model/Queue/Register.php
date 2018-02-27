<?php
class CustomGento_ProductBadges_Model_Queue_Register
{

    /**
     * @param CustomGento_ProductBadges_Model_Queue_Job_Abstract $job
     * @param Varien_Object $model
     */
    public function attemptToRegisterJob(CustomGento_ProductBadges_Model_Queue_Job_Abstract $job, Varien_Object $model)
    {
        /** @var CustomGento_ProductBadges_Model_Queue $queue */
        $queue = Mage::getModel('customgento_productbadges/queue');

        $queue->setData('processor_model', $job->whoAmI());
        $queue->setData('job_data', serialize($job->getPreparedDataForJobAction($model)));
        $queue->setData('created_at', Mage::getSingleton('core/date')->gmtDate());
        $queue->setData('status', 'pending');

        $queue->save();
    }

}