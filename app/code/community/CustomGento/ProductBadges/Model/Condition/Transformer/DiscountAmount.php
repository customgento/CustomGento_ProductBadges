<?php

class CustomGento_ProductBadges_Model_Condition_Transformer_DiscountAmount
    extends CustomGento_ProductBadges_Model_Condition_Transformer_Abstract
{
    /**
     * @inheritdoc
     */
    public function transform(Mage_Rule_Model_Condition_Product_Abstract $condition, $storeId, $fromId, $toId)
    {
        $value = $condition->getValueParsed();
        $operator = str_replace('==', '=', $condition->getOperatorForValidate());

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $select = new Zend_Db_Select($this->getDbAdapter());
        $select
            ->from(['t1' => $this->getTableName('catalog_product_entity_decimal')], ['entity_id'])
            ->joinLeft(
                ['t2' => $this->getTableName('catalog_product_entity_decimal')],
                't1.entity_id = t2.entity_id',
                []
            )
            ->where(
                "t1.attribute_id = ?", Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'price')
            )
            ->where(
                "t2.attribute_id = ?", Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'msrp')
            )
            ->where("t1.store_id = ?", Mage::app()->getStore()->getId())
            ->where("t2.store_id = ?", Mage::app()->getStore()->getId());

        if ($this->isScalarOperator($operator)) {
            $select->where("round(100-(t1.value*100/t2.value),2) {$operator} ?", $value);
        } else {
            $prefix = $this->getOperatorPrefix($operator);
            $select->where("round(100-(t1.value*100/t2.value),2) {$prefix} REGEXP ?", "(^|,){$value}(,|$)");
        }

        return new \Zend_Db_Expr("e.entity_id IN ({$select})");
    }
}