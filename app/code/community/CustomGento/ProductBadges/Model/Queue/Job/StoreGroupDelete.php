<?php
class CustomGento_ProductBadges_Model_Queue_Job_StoreGroupDelete
    extends CustomGento_ProductBadges_Model_Queue_Job_Abstract
{

    /**
     * Returns class alias for further usage later with Mage::getModel
     *
     * @return string
     */
    public function whoAmI()
    {
        return 'customgento_productbadges/queue_job_storeGroupDelete';
    }

    /**
     * @param Varien_Object $storeGroup
     * @return array
     */
    public function getPreparedDataForJobAction(Varien_Object $storeGroup)
    {
        /** @var Mage_Core_Model_Store_Group $storeGroup */
        $storeIds = $storeGroup->getStoreIds();

        return array(
            'store_group_id' => $storeGroup->getId(),
            'store_ids'      => $storeIds
        );
    }

    /**
     * @param array $data
     *
     * @return CustomGento_ProductBadges_Model_Queue_Job_StoreDelete
     */
    public function processJobAction(array $data)
    {
        $storeGroupId = $data['store_group_id'];

        /** @var Mage_Core_Model_Store_Group $storeGroup */
        $storeGroup = Mage::getModel('core/store_group');
        $storeGroup->load($storeGroupId);

        $storeGroupIdCheck = $storeGroup->getId();

        /**
         * Deletion of store group might not be successful and that is
         * why we check if store group still exists. We are not sure if
         * store group was deleted because we register this join our QUEUE
         * on store_group_delete_before ... it's not possible to know
         * store ids on store_group_delete_after
         */
        if (!empty($storeGroupIdCheck)) {
            return $this;
        }

        /**
         * It is possible that store group had not stores assigned
         */
        if (empty($data['store_ids'])) {
            return $this;
        }

        foreach ($data['store_ids'] as $storeId) {
            /** @var Mage_Core_Model_Store $store */
            $store = Mage::getModel('core/store')->load($storeId);
            $storeIdCheck = $store->getId();

            /**
             * Checking if store was deleted successfully
             */
            if (empty($storeIdCheck)) {
                $this->_getProductBadgesIndexerResource()
                    ->dropIndexTableByStoreId($storeId);
            }
        }

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