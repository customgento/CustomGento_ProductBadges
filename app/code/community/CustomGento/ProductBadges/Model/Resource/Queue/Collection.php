<?php
class CustomGento_ProductBadges_Model_Resource_Queue_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Set resource model and determine field mapping
     */
    protected function _construct()
    {
        $this->_init('customgento_productbadges/queue');
    }

    /**
     * @return CustomGento_ProductBadges_Model_Resource_Queue_Collection
     */
    public function filterNotProcessedJobs()
    {
        $this->addFieldToFilter('status', array('eq' => CustomGento_ProductBadges_Model_Queue_Job::STATUS_PENDING));
        return $this;
    }

}