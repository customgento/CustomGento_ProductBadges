<?php

class CustomGento_ProductBadges_Model_Cron
{

    public function reindexProductBadges()
    {
        Mage::getResourceModel('customgento_productbadges/indexer_productBadges')->rebuild();
    }
}
