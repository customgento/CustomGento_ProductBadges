<?php

class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_badges_badge_config_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('customgento_productbadges')->__('Product Badge Config'));
    }
}
