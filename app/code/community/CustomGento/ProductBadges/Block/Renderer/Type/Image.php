<?php
class CustomGento_ProductBadges_Block_Renderer_Type_Image
    extends CustomGento_ProductBadges_Block_Renderer_Type_Abstract
        implements CustomGento_ProductBadges_Block_Renderer_Type_Interface
{

    /**
     * @param CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig
     * @return string
     */
    public function getBadgeHtml(CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig)
    {
        if ($badgeConfig === false) {
            return '';
        }

        $imageUrl = Mage::getBaseUrl('media') . $badgeConfig->getBadgeImage();

        $imageUrl = $this->escapeHtml($imageUrl);

        $badgeInternalId = $badgeConfig->getInternalCode();

        return '<span class="product-badge product-badge--image product-badge--' . $badgeInternalId . '"><img src=" ' . $imageUrl . '" /></span>';
    }

}