<?php

class CustomGento_ProductBadges_Block_Renderer_Badge
{
    /**
     * @param CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig
     *
     * @return string
     */
    public function renderBadge(CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig)
    {
        try {
            $renderType = $badgeConfig->getRenderType();
            /** @var CustomGento_ProductBadges_Block_Renderer_Type_Interface $badgeRenderer */
            $badgeRenderer = Mage::getBlockSingleton('customgento_productbadges/renderer_type_' . $renderType);

            return $badgeRenderer->getBadgeHtml($badgeConfig);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return '';
    }
}
