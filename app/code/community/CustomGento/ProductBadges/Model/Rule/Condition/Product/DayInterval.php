<?php

class CustomGento_ProductBadges_Model_Rule_Condition_Product_DayInterval
    extends Mage_Rule_Model_Condition_Product_Abstract
{

    /**
     * Init the product found conditions and set the custom type
     */
    public function _construct()
    {
        parent::_construct();
        $this->setType('customgento_productbadges/rule_condition_product_dayInterval');
    }

    /**
     * Load attribute options
     *
     * @return CustomGento_ProductBadges_Model_Rule_Condition_Product_DayInterval
     */
    public function loadAttributeOptions()
    {
        $attributes = $this->_getAllDateAttributes();

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return array
     */
    public function getOperatorSelectOptions()
    {
        $opt = [];

        foreach ($this->getOperatorOptions() as $v => $l) {
            $opt[] = ['value' => $v, 'label' => $l];
        }

        return $opt;
    }

    /**
     * @return array
     */
    public function getOperatorOptions() {
        return [
            '>=' => $this->_getHelper()->__('is newer than X days'),
            '==' => $this->_getHelper()->__('is exactly X days ago'),
            '<=' => $this->_getHelper()->__('is older than X days')
        ];
    }

    /**
     * @param string $option
     * @return string
     */
    public function getOperatorOption($option)
    {
        $options = $this->getOperatorOptions();
        return $options[$option];
    }

    /**
     * @return array
     */
    protected function _getAllDateAttributes()
    {
        $productAttributes = Mage::getResourceModel('catalog/product_attribute_collection');

        $dateAttributes = array(
            'created_at' => Mage::helper('customgento_productbadges')->__('Created At'),
            'updated_at' => Mage::helper('customgento_productbadges')->__('Updated At')
        );

        foreach ($productAttributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ($attribute->isAllowedForRuleCondition() && $attribute->getFrontendInput() === 'date') {
                $dateAttributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }

        return $dateAttributes;
    }

    public function asHtml()
    {
        return $this->_getHelper()->__('If') . parent::asHtml();
    }

    /**
     *
     * @return CustomGento_ProductBadges_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('customgento_productbadges');
    }

}
