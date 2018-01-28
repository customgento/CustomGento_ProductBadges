<?php
class CustomGento_ProductBadges_Block_Renderer_Type_Circle
    extends CustomGento_ProductBadges_Block_Renderer_Type_Abstract
        implements CustomGento_ProductBadges_Block_Renderer_Type_Interface
{

    /**
     * @param string $badgeInternalId
     * @return string
     */
    public function getBadgeHtml($badgeInternalId)
    {
        $badgeConfig = $this->_badgeConfigHelper->getBadgeConfig($badgeInternalId);

        if ($badgeConfig === false) {
            return '';
        }

        $badgeText = $badgeConfig->getBadgeText();

        $badgeText = $this->escapeHtml($badgeText);

        return '<span ' . $this->_customStyling($badgeConfig) . ' class="product-badge product-badge--circle product-badge--' . $badgeInternalId . '"><span>' . $badgeText . '</span></span>';
    }

}