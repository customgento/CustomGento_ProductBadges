<?php
class CustomGento_ProductBadges_Model_Rule_Condition_Product_BaseCondition
    //@todo I have to check if I can get rid of this inheritance
    extends CustomGento_ProductBadges_Model_Rule_Condition_Product
{
    /**
     * Add special attributes
     *
     * @param array $attributes
     */
//    protected function _addSpecialAttributes(array &$attributes)
//    {
//        parent::_addSpecialAttributes($attributes);

//        $attributes['price'] = Mage::helper('customgento_productbadges')->__('Price');
//        $attributes['type_id'] = Mage::helper('customgento_productbadges')->__('Product Type');
//        $attributes[CustomGento_ProductBadges_Model_Rule_Condition_Vitafy_Stock::ATTRIBUTE_NAME] = Mage::helper('customgento_productbadges')->__('Stock Status');
//        $attributes[CustomGento_ProductBadges_Model_Rule_Condition_Vitafy_Discount::ATTRIBUTE_NAME] = Mage::helper('customgento_productbadges')->__('Discount amount (%)');
//    }

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
        if ($this->getAttribute()==='type_id') {
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
        if ($this->getAttribute()==='type_id') {
            $types = [];
            foreach (Mage::getSingleton('catalog/product_type')->getOptionArray() as $key => $value ) {
                $types[] = ['value' => $key, 'label' => $value];
            }
            return $types;
        }

        return parent::getValueSelectOptions();
    }

    /**
     * Collect validated attributes
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
     * @return Mage_CatalogRule_Model_Rule_Condition_Product
     */
    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();
        $skippedAttributes = [
            'category_ids',
            'type_id',
            'stock_status',
        ];

        if (!in_array($attribute, $skippedAttributes)) {
            if ($this->getAttributeObject()->isScopeGlobal()) {
                $attributes = $this->getRule()->getCollectedAttributes();
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