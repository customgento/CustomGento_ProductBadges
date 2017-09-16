<?php
class CustomGento_ProductBadges_Block_Renderer
    extends Mage_Core_Block_Abstract
{

    /** @var CustomGento_ProductBadges_Block_Renderer_Container */
    private $_containerRendererBlock;

    /** @var CustomGento_ProductBadges_Helper_Badge_Config */
    private $_badgeConfigHelper;

    /** @var array */
    private $_badges = array();

    /** @var int */
    private $_productId;

    public function __construct()
    {
        $this->_containerRendererBlock = Mage::getBlockSingleton('epetworld_product_badges/renderer_container');
        $this->_badgeConfigHelper = Mage::helper('epetworld_product_badges/badge_config');
    }

    /**
     * @param array $badges
     * @param $productId
     */
    public function init(array $badges, $productId)
    {
        $this->_badges = $badges;
        $this->_productId = $productId;
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
            'PRODUCT_BADGES_PRODUCT_ID',
            Mage::app()->getStore()->getId(),
            $this->_productId
        );
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return 3600;
    }

    /**
     * @param array $badges
     * @param $productId
     *
     * @return string
     */
    public function generateBadgesHtml(array $badges, $productId)
    {
        if (empty($badges)) {
            return '';
        }

        foreach ($badges as $badgeName => $value) {
            $containerInternalName = $this->_badgeConfigHelper->getBadgeContainerName($badgeName);
            $badgeType = $this->_badgeConfigHelper->getBadgeRenderType($badgeName);

            /** @var CustomGento_ProductBadges_Block_Renderer_Type_Interface $badge */
            try {
                $badge = Mage::getBlockSingleton('epetworld_product_badges/renderer_type_' . $badgeType);

                $this->_containerRendererBlock->attachBadgeToContainer($containerInternalName, $badgeName, $badge);
            } catch(Exception $e) {
                Mage::logException($e);
            }
        }

        $badgesHtml = $this->_containerRendererBlock->generateContainersHtml($productId);
        $this->_containerRendererBlock->clearState();

        return $badgesHtml;
    }

    /**
     * Override this method in descendants to produce html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!empty($this->_badges) && !empty($this->_productId)) {
            return $this->generateBadgesHtml($this->_badges, $this->_productId);
        }

        return '';
    }

}