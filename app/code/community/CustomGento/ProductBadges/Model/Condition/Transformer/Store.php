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
        $frontendInput = $attribute->getFrontendInput();

        $select = new Zend_Db_Select($this->getDbAdapter());

        $mainTable = 'at_scope_' . $code;

        $select
            ->from([$mainTable => $attribute->getBackendTable()], ['entity_id'])
            ->where("e.entity_id = at_scope_{$code}.entity_id")
            ->where("at_scope_{$code}.attribute_id = ?", $attribute->getAttributeId())
        ;

        $operator = str_replace('==', '=', $condition->getOperatorForValidate());

        if ($this->isScalarOperator($operator)) {
            $select->where("at_scope_{$code}.value {$operator} ?", $value);
        } else {
            $prefix = $this->getOperatorPrefix($operator);
            $value = (array) $value;
            
            /** @var array $value */
            foreach ($value as $_value) {
                if ($frontendInput == 'multiselect') {
                    $select->where("at_scope_{$code}.value {$prefix} REGEXP ?", "(^|,){$_value}(,|$)");
                }

                if ($frontendInput == 'text' || $frontendInput == 'textarea') {
                    $select->where("at_scope_{$code}.value LIKE ?", "%{$_value}%");
                }
            }
        }

        // Test for store id
        if ($attribute->isScopeStore()) {
            $select->where("at_scope_{$code}.store_id IN (0,1)");
        }

        return new Zend_Db_Expr("EXISTS ({$select})");
    }


}