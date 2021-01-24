<?php

class CustomGento_ProductBadges_Model_Rule_Condition_Product_StockStatus
    extends CustomGento_ProductBadges_Model_Rule_Condition_Product_BaseCondition
{
    const ATTRIBUTE_NAME = 'stock_status';

    /**
     * Retrieve select option values
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        return Mage::getSingleton('cataloginventory/source_stock')->toOptionArray();
    }

    /**
     * Retrieve value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Retrieve input type
     *
     * @return string
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * Add special attributes
     *
     * @param array $attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes[self::ATTRIBUTE_NAME] = Mage::helper('customgento_productbadges')->__('Stock Status');
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return self::ATTRIBUTE_NAME;
    }
}
