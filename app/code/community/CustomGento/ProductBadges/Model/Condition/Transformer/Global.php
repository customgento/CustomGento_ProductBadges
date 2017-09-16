<?php

class CustomGento_ProductBadges_Model_Condition_Transformer_Global
    extends CustomGento_ProductBadges_Model_Condition_Transformer_Abstract
{
    /**
     * @inheritdoc
     * @throws \CustomGento_ProductBadges_Exception_Transform
     */
    public function transform(Mage_Rule_Model_Condition_Product_Abstract $condition)
    {
        $value = $condition->getValueParsed();
        $operator = $condition->getOperatorForValidate();
        $column = $condition->getAttributeObject()->getAttributeCode();

        if (!in_array($column, ['sku', 'attribute_set_id', 'type_id', 'created_at', 'updated_at'])) {
            $column = "at_{$column}.value";
        }

        $operator = str_replace('==', '=', $operator);
        if ($this->isScalarOperator($operator)) {
            return new Zend_Db_Expr($this->getDbAdapter()->quoteInto("{$column} {$operator} ?", $value));
        }

        $expr = [];

        $value = (array) $value;
        /** @var array $value */
        foreach ($value as $_value) {
            $expr[] = $this->getDbAdapter()
                ->quoteInto("{$column} {$this->getOperatorPrefix($operator)} REGEXP ?", "(^|,){$_value}(,|$)");
        }

        return new Zend_Db_Expr('(' . implode(') OR (', $expr) . ')');
    }
}