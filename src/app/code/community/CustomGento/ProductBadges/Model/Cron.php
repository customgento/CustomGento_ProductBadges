<?php

class CustomGento_ProductBadges_Model_Cron
{
    /**
     * @return CustomGento_ProductBadges_Model_Cron
     */
    public function reindexProductBadges()
    {
        $this->_getProductBadgesIndexerResource()->rebuild();


        return $this;
    }

    /**
     * @return CustomGento_ProductBadges_Model_Resource_Indexer_ProductBadges
     */
    protected function _getProductBadgesIndexerResource()
    {
        return Mage::getResourceModel('customgento_productbadges/indexer_productBadges');
    }

}
