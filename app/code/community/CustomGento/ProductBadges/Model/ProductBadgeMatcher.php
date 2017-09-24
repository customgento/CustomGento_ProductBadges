<?php
class CustomGento_ProductBadges_Model_ProductBadgeMatcher
{

    /**
     * @param array $productIds
     *
     * @return array
     */
    public function getDataWithProductIdAsKey(array $productIds)
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('read');

        $badgesIndexTable = $this->_getBadgeIndexTableName();

        if (false === $badgesIndexTable) {
            return array();
        }

        $select = $readConnection
            ->select()
            ->from($badgesIndexTable)
            ->where('product_id IN(?)',  $productIds);

        $data = $readConnection->fetchAll($select);

        $transformedData = array();

        foreach($data as $productBadgeData) {
            $transformedData[$productBadgeData['product_id']] = $productBadgeData;
        }

        return $transformedData;
    }

    /**
     * @return string|false
     */
    private function _getBadgeIndexTableName()
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');

        $storeId = Mage::app()->getStore()->getStoreId();

        $badgesIndexTable = $resource->getTableName('customgento_productbadges_badges_index_' . $storeId);

        if (!$resource->getConnection('read')->isTableExists($badgesIndexTable)) {
            return false;
        }

        return $badgesIndexTable;
    }

}