<?php

class CustomGento_ProductBadges_Model_Condition_Transformer_Static
    extends CustomGento_ProductBadges_Model_Condition_Transformer_Abstract
{

    /**
     * @param Mage_Rule_Model_Condition_Product_Abstract $condition
     * @return string|Zend_Db_Expr
     */
    public function transform(Mage_Rule_Model_Condition_Product_Abstract $condition)
    {
        $value = $condition->getValueParsed();
        $operator = $condition->getOperatorForValidate();
        $column = $condition->getAttributeObject()->getAttributeCode();

        $operator = str_replace('==', '=', $operator);

        return new Zend_Db_Expr($this->getDbAdapter()->quoteInto("{$column} {$operator} ?", $value));
    }
}