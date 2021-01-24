<?php

class CustomGento_ProductBadges_Model_Rule_Condition_Combine
    extends Mage_CatalogRule_Model_Rule_Condition_Combine
{
    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        /** @var CustomGento_ProductBadges_Model_Rule_Condition_Product_BaseCondition $productCondition */
        $productCondition  = Mage::getModel('customgento_productbadges/rule_condition_product_baseCondition');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        $attributes = array();
        foreach ($productAttributes as $code => $label) {
            $class = 'customgento_productbadges/rule_condition_product_baseCondition';

            $attributes[] = array(
                'value' => $class . '|' . $code,
                'label' => $label
            );
        }

        // Adding day interval attributes
        /** @var CustomGento_ProductBadges_Model_Rule_Condition_Product_DayInterval $dayIntervalCondition */
        $dayIntervalCondition = Mage::getModel('customgento_productbadges/rule_condition_product_dayInterval');
        $dayIntervalAttribute = $dayIntervalCondition->loadAttributeOptions()->getAttributeOption();
        foreach ($dayIntervalAttribute as $code => $label) {
            $class = 'customgento_productbadges/rule_condition_product_dayInterval';

            $attributes[] = array(
                'value' => $class . '|' . $code,
                'label' => $label
            );
        }

        /** @var CustomGento_ProductBadges_Helper_Config $configHelper */
        $configHelper = Mage::helper('customgento_productbadges/config');

        $rulesConfigs = $configHelper->getRulesConfigurations();

        /** @var CustomGento_ProductBadges_Model_Rule_Config $configModel */
        foreach ($rulesConfigs as $configModel) {
            $attributes[] = array(
                'value' => $configModel->getConditionClass() . '|' . $configModel->getInternalCode(),
                'label' => $configModel->getLabel()
            );
        }

        // Sort again alphabetically
        usort($attributes, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        $conditions = array(
            array(
                'value' => '',
                'label' => Mage::helper('rule')->__('Please choose a condition to add...')
            ),
            array(
                'value' => 'catalogrule/rule_condition_combine',
                'label' => Mage::helper('catalogrule')->__('Conditions Combination')
            ),
            array(
                'label' => Mage::helper('catalogrule')->__('Product Attribute'),
                'value' => $attributes
            ),
        );

        return $conditions;
    }

    /**
     * Returns the aggregator options
     *
     * @see Mage_Rule_Model_Condition_Combine::loadAggregatorOptions()
     * @return CustomGento_ProductBadges_Model_Rule_Condition_Combine
     */
    public function loadAggregatorOptions()
    {
        $this->setAggregatorOption(
            array(
                'all' => Mage::helper('rule')->__('ALL')
            )
        );

        return $this;
    }

    /**
     * Returns the value options
     *
     * @see Mage_Rule_Model_Condition_Combine::loadValueOptions()
     * @return CustomGento_ProductBadges_Model_Rule_Condition_Combine
     */
    public function loadValueOptions()
    {
        $this->setValueOption(
            array(
                1 => Mage::helper('rule')->__('TRUE')
            )
        );

        return $this;
    }
}
