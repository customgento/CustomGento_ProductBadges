<?php

class CustomGento_ProductBadges_Model_Rule_Condition_Product_BaseCondition
    //@todo I have to check if I can get rid of this inheritance
    extends CustomGento_ProductBadges_Model_Rule_Condition_Product
{
    /**
     * Retrieve input type
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttribute() === 'type_id') {
            return 'select';
        }

        return parent::getInputType();
    }

    /**
     * Retrieve value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->getAttribute() === 'type_id') {
            return 'select';
        }

        return parent::getValueElementType();
    }

    /**
     * Retrieve select option values
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        if ($this->getAttribute() === 'type_id') {
            $types = array();
            foreach (Mage::getSingleton('catalog/product_type')->getOptionArray() as $key => $value) {
                $types[] = array('value' => $key, 'label' => $value);
            }

            return $types;
        }

        return parent::getValueSelectOptions();
    }

    /**
     * Collect validated attributes
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
     *
     * @return CustomGento_ProductBadges_Model_Rule_Condition_Product_BaseCondition
     */
    public function collectValidatedAttributes($productCollection)
    {
        $attribute         = $this->getAttribute();
        $skippedAttributes = array(
            'category_ids',
            'type_id',
            'stock_status',
            'day_interval'
        );

        if (!in_array($attribute, $skippedAttributes)) {
            if ($this->getAttributeObject()->isScopeGlobal()) {
                $attributes             = $this->getRule()->getCollectedAttributes();
                $attributes[$attribute] = true;
                $this->getRule()->setCollectedAttributes($attributes);
                $productCollection->addAttributeToSelect($attribute, 'left');
            } else {
                $this->_entityAttributeValues = $productCollection->getAllAttributeValues($attribute);
            }
        }

        return $this;
    }

    /**
     * Validate product attrbute value for condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();

        if ('type_id' == $attrCode) {
            return $this->validateAttribute($object->getTypeId());
        }

        return parent::validate($object);
    }
}
