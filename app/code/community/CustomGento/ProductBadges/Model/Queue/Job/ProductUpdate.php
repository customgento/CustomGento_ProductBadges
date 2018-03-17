<?php
class CustomGento_ProductBadges_Model_Queue_Job_ProductUpdate
    extends CustomGento_ProductBadges_Model_Queue_Job_Abstract
{

    /**
     * @param Varien_Object $product
     * @return array
     */
    public function getPreparedDataForJobAction(Varien_Object $product)
    {
        return array('product_id' => $product->getId());
    }

    /**
     * @param array $data
     */
    public function processJobAction(array $data)
    {
        $this->_getProductBadgesIndexerResource()->rebuild(null, array($data['product_id']));
        $this->_getCacheHelper()->clearProductBadgesCache($data['product_id']);
    }

    /**
     * @return CustomGento_ProductBadges_Model_Resource_Indexer_ProductBadges
     */
    protected function _getProductBadgesIndexerResource()
    {
        return Mage::getResourceModel('customgento_productbadges/indexer_productBadges');
    }

    /**
     * @return CustomGento_ProductBadges_Helper_Cache
     */
    protected function _getCacheHelper()
    {
        return Mage::helper('customgento_productbadges/cache');
    }

}