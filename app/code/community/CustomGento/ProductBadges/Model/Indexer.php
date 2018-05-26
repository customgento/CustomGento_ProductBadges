<?php
class CustomGento_ProductBadges_Model_Indexer
    extends Mage_Index_Model_Indexer_Abstract
{

    protected $_matchedEntities = array();

    protected function _construct()
    {
        $this->_init('customgento_productbadges/indexer_productBadges');
    }

    /**
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'CustomGento Product Badges';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Index product badges';
    }

    /**
     * @param Mage_Index_Model_Event $event
    */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
    }

}