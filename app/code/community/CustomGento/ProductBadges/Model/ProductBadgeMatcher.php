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

        // @todo add current store in table name, currently it's hardcoded
        $badgesIndexTable = $resource->getTableName('customgento_productbadges_badges_index_' . 1);

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

}