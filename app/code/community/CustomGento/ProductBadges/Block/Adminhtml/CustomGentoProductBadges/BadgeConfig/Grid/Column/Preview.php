<?php

class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Grid_Column_Preview
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        /** @var CustomGento_ProductBadges_Block_Renderer_Badge $badge */
        $badgeRenderer = Mage::getBlockSingleton('customgento_productbadges/renderer_badge');

        return $badgeRenderer->renderBadge($row);
    }
}
