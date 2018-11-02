<?php

class CustomGento_ProductBadges_Model_Queue_RegisterJob
{
    /**
     * @param CustomGento_ProductBadges_Model_Queue_Job_Abstract $job
     * @param Varien_Object                                      $model
     */
    public function attemptToRegisterJob(CustomGento_ProductBadges_Model_Queue_Job_Abstract $job, Varien_Object $model)
    {
        if (!$this->_getHelper()->isEnabled()) {
            return;
        }

        /** @var CustomGento_ProductBadges_Model_Queue $queue */
        $queue = Mage::getModel('customgento_productbadges/queue');

        $queue->setData('processor_model', $job->whoAmI());
        $queue->setData('job_data', serialize($job->getPreparedDataForJobAction($model)));
        $queue->setData('created_at', Mage::getSingleton('core/date')->gmtDate());
        $queue->setData('status', CustomGento_ProductBadges_Model_Queue_Job::STATUS_PENDING);

        $queue->save();
    }

    /**
     *
     * @return CustomGento_ProductBadges_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('customgento_productbadges');
    }
}
