<?php

class CustomGento_ProductBadges_Model_Condition_Transformer_Category
    extends CustomGento_ProductBadges_Model_Condition_Transformer_Abstract
{
    /**
     * @inheritdoc
     */
    public function transform(Mage_Rule_Model_Condition_Product_Abstract $condition)
    {
        $value = $condition->getValueParsed();
        $operator = $condition->getOperatorForValidate();

        $select = new \Zend_Db_Select($this->getDbAdapter());

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $select
            ->from(['category_product' => $this->getTableName('catalog/category_product_index')])
            ->where('category_product.product_id = e.entity_id')
            ->where("category_product.category_id IN ({$value})");

        return new \Zend_Db_Expr("{$this->getOperatorPrefix($operator)} EXISTS({$select})");
    }
}