<?php
/**
 * Class CustomGento_ProductBadges_Model_Queue
 */

class CustomGento_ProductBadges_Model_Queue
    extends Mage_Core_Model_Abstract
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customgento_productbadges_queue';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'queue';

    /**
     * Set resource model and Id field name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('customgento_productbadges/queue');
        $this->setIdFieldName('job_id');
    }

}