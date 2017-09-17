<?php
class CustomGento_ProductBadges_Helper_Data
    extends Mage_Core_Helper_Abstract
{

    CONST EPETWORLD_PRODUCT_BADGES_ENABLED_XML_CONFIG_PATH = 'epetworld_product_badges/general/enabled';

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

        var_dump($this->_productBadgesData);

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

        $badges = isset($this->_productBadgesData[$product->getId()]) ? $this->_productBadgesData[$product->getId()] : array();
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

        /** @var Epetworld_ProductBadges_Model_ProductBadge $producBadgeModel */
        $productBadgeModel = Mage::getModel('epetworld_product_badges/productBadge')->load($product->getId(), 'product_id');

        if ($id = $productBadgeModel->getId()) {
            $badges = $productBadgeModel->getData();
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
        return true;
        return Mage::getStoreConfigFlag(self::EPETWORLD_PRODUCT_BADGES_ENABLED_XML_CONFIG_PATH);
    }

    /**
     * @param array $badges
     * @return array
     */
    private function _filerBadgesData(array $badges)
    {
        unset($badges['entity_id']);
        unset($badges['product_id']);
        $badges = array_filter($badges);

        return $badges;
    }

    /**
     * @param array $badges
     * @param int $productId
     *
     * @return Epetworld_ProductBadges_Block_Renderer
     */
    private function _createBadgesRendererBlock(array $badges, $productId)
    {
        /** @var Epetworld_ProductBadges_Block_Renderer $badgesBlock */
        $badgesBlock = Mage::app()->getLayout()->createBlock('epetworld_product_badges/renderer', 'product_badges');
        $badgesBlock->init($badges, $productId);

        return $badgesBlock;
    }


}