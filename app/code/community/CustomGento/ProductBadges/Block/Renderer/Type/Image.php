<?php
class CustomGento_ProductBadges_Block_Renderer_Type_Image
    extends CustomGento_ProductBadges_Block_Renderer_Type_Abstract
        implements CustomGento_ProductBadges_Block_Renderer_Type_Interface
{

    /**
     * @param string $badgeInternalId
     * @param int $productId
     * @return string
     */
    public function getBadgeHtml($badgeInternalId, $productId)
    {
        $badgeConfig = $this->_badgeConfigHelper->getBadgeConfig($badgeInternalId);

        if ($badgeConfig === false) {
            return '';
        }

        $imageUrl = Mage::getBaseUrl('media') . $badgeConfig->getBadgeImage();

        $imageUrl = $this->escapeHtml($imageUrl);

        return '<span class="product-badge product-badge--image product-badge--' . $badgeInternalId . '"><img src=" ' . $imageUrl . '" /></span>';
    }

}