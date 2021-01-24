<?php

class CustomGento_ProductBadges_Model_Rule_Condition_Product_Combine
    extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Init the product combine conditions and set the custom type
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setType('customgento_productbadges/rule_condition_product_combine');
    }

    /**
     * Retrieve the product options for the select field.
     *
     * @see Mage_Rule_Model_Condition_Abstract::getNewChildSelectOptions()
     * @return array Conditions as array
     */
    public function getNewChildSelectOptions()
    {
        $productCondition  = Mage::getModel('customgento_productbadges/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        $pAttributes = array();

        foreach ($productAttributes as $code => $label) {
            $pAttributes[] = array(
                'value' => 'customgento_productbadges/rule_condition_product|' . $code,
                'label' => $label
            );
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            array(
                array(
                    'value' => 'customgento_productbadges/rule_condition_product_combine',
                    'label' => Mage::helper('customgento_productbadges')->__('Conditions Combination')
                ),
                array(
                    'label' => Mage::helper('customgento_productbadges')->__('Product Attribute'),
                    'value' => $pAttributes
                ),
            )
        );

        return $conditions;
    }
}
