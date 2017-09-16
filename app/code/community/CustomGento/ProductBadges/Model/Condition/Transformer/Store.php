<?php

class CustomGento_ProductBadges_Model_Condition_Transformer_Store
    extends CustomGento_ProductBadges_Model_Condition_Transformer_Abstract
{
    /**
     * @inheritdoc
     * @throws \CustomGento_ProductBadges_Exception_Transform
     */
    public function transform(Mage_Rule_Model_Condition_Product_Abstract $condition)
    {
        $attribute = $condition->getAttributeObject();
        $code = $attribute->getAttributeCode();
        $value = $condition->getValueParsed();

        $select = new Zend_Db_Select($this->getDbAdapter());
        $select
            ->from(['at_scope_' . $code => $attribute->getBackendTable()], ['entity_id'])
            ->where("e.entity_id = at_scope_{$code}.entity_id")
            ->where("at_scope_{$code}.attribute_id = ?", $attribute->getAttributeId())
        ;

        $operator = str_replace('==', '=', $condition->getOperatorForValidate());

        if ($this->isScalarOperator($operator)) {
            $select->where("at_scope_${code}.value {$operator} ?", $value);
        } else {
            $prefix = $this->getOperatorPrefix($operator);
            $value = (array) $value;
            
            /** @var array $value */
            foreach ($value as $_value) {
                $select->orWhere("at_scope_${code}.value {$prefix} REGEXP ?", "(^|,){$_value}(,|$)");
            }
        }

        return new Zend_Db_Expr("EXISTS ({$select})");
    }
}