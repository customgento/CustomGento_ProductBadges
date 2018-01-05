<?php
class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'customgento_productbadges';
        $this->_controller = 'adminhtml_customGentoProductBadges_badgeConfig';
        $this->_headerText = Mage::helper('customgento_productbadges')->__('Product Badges Configurations');
        $this->_addButtonLabel = Mage::helper('customgento_productbadges')->__('Add New Badge');
    }

}
