<?php
class CustomGento_ProductBadges_Block_Renderer_Type_Circle
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
        $badgeContent = $this->_getContent($badgeInternalId, $productId);
        if (empty($badgeContent)) {
            return '';
        }

        return '<span class="product-badge product-badge--circle product-badge--' . $badgeInternalId . '"><span>' . $badgeContent . '</span></span>';
    }

}