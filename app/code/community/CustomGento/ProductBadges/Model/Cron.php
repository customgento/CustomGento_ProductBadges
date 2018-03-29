<?php

class CustomGento_ProductBadges_Model_Cron
{

    /**
     * @param Mage_Cron_Model_Schedule $schedule
     * @return CustomGento_ProductBadges_Model_Cron
     */
    public function reindexProductBadges(Mage_Cron_Model_Schedule $schedule)
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
