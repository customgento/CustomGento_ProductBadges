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

}