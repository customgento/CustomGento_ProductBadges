<?php

class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Grid_Column_Preview
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $renderType = $row->getData('render_type');

        /** @var CustomGento_ProductBadges_Block_Renderer_Type_Interface $badge */
        $badge = Mage::getBlockSingleton('customgento_productbadges/renderer_type_' . $renderType);

        return $badge->getBadgeHtml($row->getData('internal_code'));
    }

}