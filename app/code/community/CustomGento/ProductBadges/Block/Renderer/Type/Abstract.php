<?php
class CustomGento_ProductBadges_Block_Renderer_Type_Abstract
    extends Mage_Core_Block_Abstract
{

    /** @var CustomGento_ProductBadges_Model_Config_RenderTypeData */
    protected $_badgeConfigHelper;

    protected function _construct()
    {
        $this->_badgeConfigHelper = Mage::getModel('customgento_productbadges/config_renderTypeData');
    }

}