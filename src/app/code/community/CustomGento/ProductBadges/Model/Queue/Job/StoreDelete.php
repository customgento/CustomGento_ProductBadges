<?php

class CustomGento_ProductBadges_Model_Queue_Job_StoreDelete
    extends CustomGento_ProductBadges_Model_Queue_Job_Abstract
{
    /**
     * Returns class alias for further usage later with Mage::getModel
     *
     * @return string
     */
    public function whoAmI()
    {
        return 'customgento_productbadges/queue_job_storeDelete';
    }

    /**
     * @param Varien_Object $store
     *
     * @return array
     */
    public function getPreparedDataForJobAction(Varien_Object $store)
    {
        return array('store_id' => $store->getId());
    }

    /**
     * @param array $data
     *
     * @return CustomGento_ProductBadges_Model_Queue_Job_StoreDelete
     */
    public function processJobAction(array $data)
    {
        $this->_getProductBadgesIndexerResource()
            ->dropIndexTableByStoreId($data['store_id']);

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
