<?php
class CustomGento_ProductBadges_Block_Renderer_Type_Rectangle
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

        $badgeText = $badgeConfig->getBadgeText();

        $badgeText = $this->escapeHtml($badgeText);

        $badgeInternalId = $badgeConfig->getInternalCode();

        return '<span ' . $this->_customStyling($badgeConfig)
            . ' class="product-badge product-badge--rectangle product-badge--' . $badgeInternalId . '">' . $badgeText
            . '</span>';
    }

}
