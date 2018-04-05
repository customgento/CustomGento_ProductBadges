<?php

class CustomGento_ProductBadges_Model_Condition_Transformer_StockStatus
    extends CustomGento_ProductBadges_Model_Condition_Transformer_Abstract
{
    /**
     * @inheritdoc
     */
    public function transform(Mage_Rule_Model_Condition_Product_Abstract $condition, $fromId, $toId, $storeId)
    {
        $value = $condition->getValueParsed();
        $operator = $condition->getOperatorForValidate();

        $select = new \Zend_Db_Select($this->getDbAdapter());

        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $select
            ->from(['product_stock' => $this->getTableName('cataloginventory/stock_item')], 'product_id')
            ->where("product_stock.is_in_stock = {$value}");

        return new \Zend_Db_Expr("e.entity_id {$this->getOperatorPrefix($operator)} IN ({$select})");
    }
}