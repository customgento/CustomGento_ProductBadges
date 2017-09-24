<?php
class CustomGento_ProductBadges_Block_Renderer_Type_Abstract
    extends Mage_Core_Block_Abstract
{

    /** @var CustomGento_ProductBadges_Helper_RenderTypeConfig */
    private $_badgeConfigHelper;

    /** @var CustomGento_ProductBadges_Helper_Data_ProductAttribute */
    private $_productAttributeHelper;

    public function __construct()
    {
        $this->_badgeConfigHelper = Mage::helper('customgento_productbadges/renderTypeConfig');
        $this->_productAttributeHelper = Mage::helper('customgento_productbadges/data_productAttribute');
    }

    /**
     * @todo: This function is around 45 lines! This is a clear signs for bad design of the classes
     * used inside this function. Clear sign of code smell but good argument for further refactoring
     *
     * @param string $badgeInternalId
     * @param $productId
     *
     * @return string
     */
    protected function _getContent($badgeInternalId, $productId)
    {
        return Mage::getSingleton('customgento_productbadges/config_badgeText')->getBadgeText($badgeInternalId);

        $badgeSourceAttributeCode = $this->_badgeConfigHelper->getBadgeSourceAttributeCode($badgeInternalId);

        /**
         * Case when we read the badge content from badges table
         */
        // @todo: Rework this logic later
//        if ($this->_badgeConfigHelper->usesDirectSource($badgeInternalId)) {
//            /** @var CustomGento_ProductBadges_Model_ProductBadge $productBadgesModel */
//            $productBadgesModel = Mage::getSingleton('customgento_productbadges/productBadge');
//            $value = $productBadgesModel->fetchReadProductBadgeFieldValue($productId, $badgeInternalId);
//
//            if (false !== $value && 0 < $value) {
//                $template = $this->_badgeConfigHelper->getTemplateBadgeSourceAttribute($badgeInternalId);
//
//                if (false !== $template) {
//                    return str_replace('###PLACEHOLDER###', $value, $template);
//                }
//
//                return '';
//            }
//
//            return '';
//        }

        /**
         * Case when we read the badge content from product attribute
         */
        // @todo: Rework this logic later
//        if (false !== $badgeSourceAttributeCode) {
//            $template = $this->_badgeConfigHelper->getTemplateBadgeSourceAttribute($badgeInternalId);
//            $badgeSourceAttributeValue = $this->_productAttributeHelper->fetchProductAttributeBy_ProductId_AttributeCode_StoreId($productId, $badgeSourceAttributeCode);
//
//            $badgeSourceAttributeValue = $this->_transformValue($badgeInternalId, $badgeSourceAttributeValue);
//
//            if (false === $template || false === $badgeSourceAttributeValue) {
//                return '';
//            }
//
//            return str_replace('###PLACEHOLDER###', $badgeSourceAttributeValue, $template);
//        }
//
//        return $this->_badgeConfigHelper->getBadgeDefaultValue($badgeInternalId);
    }

    /**
     * @param $badgeInternalId
     * @param $badgeSourceAttributeValue
     *
     * @return false|string
     */
    private function _transformValue($badgeInternalId, $badgeSourceAttributeValue)
    {
        /** Note: Hardcoded things because the concept is not so clear */
        if ('has_discount' === $badgeInternalId) {
            /**
             * Note:
             *
             * For has_discount we expect that the initial value is between 0.00 and 1.00
             *
             * Sometimes we have values 0.00 and we don't show badges with content (0%)
             *
             * I also will try to limit the cases when we have data issue and somehow the discount is more than 100%
             * I just don't trust the data entry
             */

            if (0 == $badgeSourceAttributeValue) {
                return false;
            }

            $badgeSourceAttributeValue = $badgeSourceAttributeValue * 100;

            if (100 < $badgeSourceAttributeValue) {
                return false;
            }

            return intval($badgeSourceAttributeValue);
        }

        return $badgeSourceAttributeValue;
    }

}