<?php
class CustomGento_ProductBadges_Model_Resource_Queue
    extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('customgento_productbadges/queue', 'job_id');
    }

    /**
     * Remove already sent messages
     *
     * @return Mage_Core_Model_Resource_Email_Queue
     */
    public function removeOldJobs()
    {
        $days = 1;

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            'created_at < NOW() - INTERVAL '.$days.' DAY');
        return $this;
    }

    /**
     * @param array $jobIds
     * @return $this
     */
    public function markJobsAsPicked(array $jobIds)
    {
        $bind = array('status' => CustomGento_ProductBadges_Model_Queue_Job::STATUS_PICKED);

        $condition = $this->_getWriteAdapter()
            ->quoteInto('job_id IN (?)', $jobIds);

        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            $bind,
            $condition
        );

        return $this;
    }

}