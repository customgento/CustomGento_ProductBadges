<?php

class CustomGento_ProductBadges_Model_Cron
{

    /**
     * @return CustomGento_ProductBadges_Model_Cron
     */
    public function reindexProductBadges()
    {
        $this->_getProductBadgesIndexerResource()->rebuild();
        $this->_getCacheModel()->clearAllBadgeCache();

        return $this;
    }

    /**
     * @return CustomGento_ProductBadges_Model_Resource_Indexer_ProductBadges
     */
    protected function _getProductBadgesIndexerResource()
    {
        return Mage::getResourceModel('customgento_productbadges/indexer_productBadges');
    }

    /**
     * @return CustomGento_ProductBadges_Model_Cache
     */
    protected function _getCacheModel()
    {
        return Mage::getModel('customgento_productbadges/cache');
    }

}
