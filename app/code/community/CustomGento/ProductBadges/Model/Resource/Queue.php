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

}