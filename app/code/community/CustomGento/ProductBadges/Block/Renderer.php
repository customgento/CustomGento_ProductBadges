<?php
class CustomGento_ProductBadges_Block_Renderer
    extends Mage_Core_Block_Abstract
{

    /** @var CustomGento_ProductBadges_Block_Renderer_Container */
    private $_containerRendererBlock;

    /** @var CustomGento_ProductBadges_Helper_RenderTypeConfig */
    private $_badgeConfigHelper;

    /** @var array */
    private $_badges = array();

    /** @var int */
    private $_productId;

    public function __construct()
    {
        $this->_containerRendererBlock = Mage::getBlockSingleton('customgento_productbadges/renderer_container');
        $this->_badgeConfigHelper = Mage::helper('customgento_productbadges/renderTypeConfig');
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

        foreach ($badges as $badgeCode => $value) {
            $badgeType = $this->_badgeConfigHelper->getBadgeRenderType($badgeCode);

            /** @var CustomGento_ProductBadges_Block_Renderer_Type_Interface $badge */
            try {
                $badge = Mage::getBlockSingleton('customgento_productbadges/renderer_type_' . $badgeType);

                $this->_containerRendererBlock->attachBadgeToContainer($badgeCode, $badge);
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