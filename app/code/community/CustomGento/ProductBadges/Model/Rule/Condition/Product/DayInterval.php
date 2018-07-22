<?php

class CustomGento_ProductBadges_Model_Rule_Condition_Product_DayInterval
    extends CustomGento_ProductBadges_Model_Rule_Condition_Product_BaseCondition
{
    const ATTRIBUTE_NAME = 'day_interval';

    /**
     * Init the product found conditions and set the custom type
     */
    public function _construct()
    {
        parent::_construct();
        $this->setType('customgento_productbadges/rule_condition_product_dayInterval');
    }

    /**
     * Add special attributes
     *
     * @param array $attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes[self::ATTRIBUTE_NAME] = Mage::helper('customgento_productbadges')->__('Day Interval');
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return self::ATTRIBUTE_NAME;
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElementHtml()
                . $this->getAttributeElementHtml();

        $html .= Mage::helper('customgento_productbadges')->
            __("If %s %s %s day(s)",
                $this->getQualifiedAttributesHtml(),
                $this->getOperatorHtml(),
                $this->getValueHtml()
            );
        if ($this->getId() != '1') {
            $html.= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * @return string
     */
    protected function getQualifiedAttributesHtml()
    {
        $attributeCode = '';

        $value = $this->getValue();

        if (!empty($value['attribute_code'])) {
            $attributeCode = $value['attribute_code'];
        }

        $values = $this->_getAllDateAttributes();

        $field = $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__value__attribute_code', 'select', array(
            'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][value][attribute_code]',
            'values' => $values,
            'value'=> $attributeCode,
            'value_name'=> !empty($attributeCode) ? $values[$attributeCode] : Mage::helper('customgento_productbadges')->__('(Choose attribute)')
        ))->setRenderer(Mage::getBlockSingleton('rule/editable'));

        return $field->toHtml();
    }

    /**
     * @return string
     */
    protected function getOperatorHtml()
    {
        $operators = $this->_getOperatorOptions();

        $operator = $this->getOperator();

        $field = $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__operator', 'select', array(
            'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][operator]',
            'values' => $operators,
            'value'=> $operator,
            'value_name'=> !empty($operator) ? $this->_defaultOperatorOptions[$operator] : Mage::helper('customgento_productbadges')->__('(Choose condition)')
        ))->setRenderer(Mage::getBlockSingleton('rule/editable'));

        return $field->toHtml();
    }

    /**
     * @return string
     */
    protected function getValueHtml()
    {
        $days = '';

        $value = $this->getValue();

        if (!empty($value['days'])) {
            $days = $value['days'];
        }

        $field = $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__value__days', 'text', array(
            'name'=>'rule['.$this->getPrefix().']['.$this->getId().'][value][days]',
            'value'=> $days,
            'value_name'=> $days
        ))->setRenderer(Mage::getBlockSingleton('rule/editable'));

        return $field->toHtml();
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return 'conditions';
    }

    /**
     * @return string
     */
    public function getAttributeElementHtml()
    {
        $field = $this->getForm()->addField($this->getPrefix().'__'.$this->getId().'__attribute', 'hidden', array(
            'name' => 'rule['.$this->getPrefix().']['.$this->getId().'][attribute]',
            'value' => $this->getAttribute(),
            'no_span' => true,
            'class'   => 'hidden'
        ));

        return $field->toHtml();
    }

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

    /**
     * @return array
     */
    protected function _getOperatorOptions()
    {
        $options = array();

        $dateOperatorCodes = $this->_defaultOperatorInputByType['date'];

        foreach ($dateOperatorCodes as $code) {
            $options[$code] = $this->_defaultOperatorOptions[$code];
        }

        return $options;
    }

}
