<?php

class CustomGento_ProductBadges_Helper_Data
    extends Mage_Core_Helper_Abstract
{

    CONST CUSTOMGENTO_PRODUCT_BADGES_ENABLED_XML_CONFIG_PATH = 'customgento_productbadges_global_config/general/enabled';

    /** @var array */
    private $_productBadgesData;

    /**
     * @param Mage_Eav_Model_Entity_Collection_Abstract $productCollection
     * @return $this
     */
    public function initProductBadgeCollection(Mage_Eav_Model_Entity_Collection_Abstract $productCollection)
    {
        if (!$this->isEnabled()) {
            return $this;
        }

        $productIds = $productCollection->getLoadedIds();

        /** @var CustomGento_ProductBadges_Model_ProductBadgeMatcher $productBadgeMatcher */
        $productBadgeMatcher = Mage::getModel('customgento_productbadges/productBadgeMatcher');

        $this->_productBadgesData = $productBadgeMatcher->getDataWithProductIdAsKey($productIds);

        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public function generateBadgesHtml(Mage_Catalog_Model_Product $product)
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $badgesCacheHtml = $this->_getBadgeCacheModel()->getProductBadgesCache($product->getId());

        if (!empty($badgesCacheHtml)) {
            return $badgesCacheHtml;
        }

        $badges = isset($this->_productBadgesData[$product->getId()]) ? $this->_productBadgesData[$product->getId()]
            : array();
        $badges = $this->_filerBadgesData($badges);

        return $this->_createBadgesRendererBlock($badges, $product->getId())->toHtml();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public function generateSingleProductBadgesHtml(Mage_Catalog_Model_Product $product)
    {
        if (!$this->isEnabled()) {
            return '';
        }

        $badgesCacheHtml = $this->_getBadgeCacheModel()->getProductBadgesCache($product->getId());

        if (!empty($badgesCacheHtml)) {
            return $badgesCacheHtml;
        }

        /** @var CustomGento_ProductBadges_Model_ProductBadgeMatcher $productBadgeMatcher */
        $productBadgeMatcher = Mage::getModel('customgento_productbadges/productBadgeMatcher');

        $singleProductBadges = $productBadgeMatcher->getDataWithProductIdAsKey(array($product->getId()));

        if (!empty($singleProductBadges[$product->getId()])) {
            $badges = $singleProductBadges[$product->getId()];
            $badges = $this->_filerBadgesData($badges);
            return $this->_createBadgesRendererBlock($badges, $product->getId())->toHtml();
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::CUSTOMGENTO_PRODUCT_BADGES_ENABLED_XML_CONFIG_PATH);
    }

    /**
     * @param array $badges
     * @return array
     */
    private function _filerBadgesData(array $badges)
    {
        unset($badges['product_id']);
        $badges = array_filter($badges);

        return $badges;
    }

    /**
     * @param array $badges
     * @param int $productId
     *
     * @return CustomGento_ProductBadges_Block_Renderer
     */
    private function _createBadgesRendererBlock(array $badges, $productId)
    {
        /** @var CustomGento_ProductBadges_Block_Renderer $badgesBlock */
        $badgesBlock = Mage::app()->getLayout()->createBlock('customgento_productbadges/renderer', 'product_badges');
        $badgesBlock->init($badges, $productId);

        return $badgesBlock;
    }

    /**
     * @return CustomGento_ProductBadges_Model_Cache
     */
    protected function _getBadgeCacheModel()
    {
        return Mage::getModel('customgento_productbadges/cache');
    }

}