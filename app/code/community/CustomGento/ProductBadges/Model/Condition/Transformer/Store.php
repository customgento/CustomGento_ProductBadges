<?php

class CustomGento_ProductBadges_Model_Condition_Transformer_Store
    extends CustomGento_ProductBadges_Model_Condition_Transformer_Abstract
{
    /**
     * @inheritdoc
     */
    public function transform(Mage_Rule_Model_Condition_Product_Abstract $condition, $storeId, $fromId, $toId)
    {
        $attribute = $condition->getAttributeObject();

        // Case when attribute has store scope
        if ($attribute->isScopeStore()) {

            $storeMatchesData = $this->_getMatchesForStoreView($attribute, $condition, $storeId, $fromId, $toId);

            $alreadyExistInStoreViewIds = $this->_getExistingInStoreScope($attribute, $storeId, $fromId, $toId);

            $defaultStoreMatchesData = $this->_getExistingInDefaultScope($attribute, $condition, $fromId, $toId, $alreadyExistInStoreViewIds);

            $matches = array_merge($storeMatchesData, $defaultStoreMatchesData);

            if (empty($matches)) {
                // We return false in order to mark that sub-query had no result
                return new Zend_Db_Expr("1 != 1");
            }

            return new Zend_Db_Expr("`e`.`entity_id` IN (" . implode(",", $matches) . ")");
        }

        // Case when attribute has global scope
        $scopeTable = $this->_getScopeTable($attribute);
        $select = new Zend_Db_Select($this->getDbAdapter());
        $select->where("`e`.`entity_id` = `{$scopeTable}`.`entity_id`");
        $this->attachAttributeValueCondition($select, $condition, $attribute);

        return new Zend_Db_Expr("EXISTS ({$select})");
    }

    /**
     * @param Zend_Db_Select $select
     * @param Mage_Rule_Model_Condition_Product_Abstract $condition
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     */
    protected function attachAttributeValueCondition(
        Zend_Db_Select $select,
        Mage_Rule_Model_Condition_Product_Abstract $condition,
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute
    )
    {
        $value = $condition->getValueParsed();
        $frontendInput = $attribute->getFrontendInput();

        $scopeTable = $this->_getScopeTable($attribute);

        $select
            ->from([$scopeTable => $attribute->getBackendTable()], ['entity_id'])
            ->where("`{$scopeTable}`.`attribute_id` = ?", $attribute->getAttributeId())
        ;

        $operator = str_replace('==', '=', $condition->getOperatorForValidate());

        if ($this->isScalarOperator($operator)) {
            $select->where("`{$scopeTable}`.`value` {$operator} ?", $value);
        } else {
            $prefix = $this->getOperatorPrefix($operator);
            $orAndCondition = $this->orAndCondition($operator);
            $value = (array) $value;

            $subConditions = [];

            /** @var array $value */
            foreach ($value as $_value) {
                if ($frontendInput == 'multiselect') {
                    $subConditions[] = $this
                        ->getDbAdapter()
                        ->quoteInto("`{$scopeTable}`.`value` {$prefix} REGEXP ?", "(^|,){$_value}(,|$)");
                }

                if ($frontendInput == 'text' || $frontendInput == 'textarea') {
                    $subConditions[] = $this
                        ->getDbAdapter()
                        ->quoteInto("`{$scopeTable}`.`value` LIKE ?", "%{$_value}%");
                }
            }

            $select->where(implode(" {$orAndCondition} ", $subConditions));
        }
    }

    /**
     * @param $attribute
     * @param Mage_Rule_Model_Condition_Product_Abstract $condition
     * @param $storeId
     * @param $fromId
     * @param $toId
     *
     * @return array
     */
    private function _getMatchesForStoreView(
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute,
        Mage_Rule_Model_Condition_Product_Abstract $condition,
        $storeId,
        $fromId,
        $toId
    )
    {
        $storeViewSelect = new Zend_Db_Select($this->getDbAdapter());
        $this->attachAttributeValueCondition($storeViewSelect, $condition, $attribute);

        $scopeTable = $this->_getScopeTable($attribute);

        // Fetch the matches for currently scanned Store View Id
        $storeViewSelect->where("`{$scopeTable}`.`store_id` = ?", $storeId)
            ->where("`{$scopeTable}`.`entity_id` >= ?", $fromId)
            ->where("`{$scopeTable}`.`entity_id` <= ?", $toId);

        return $this->getDbAdapter()->fetchCol($storeViewSelect);
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param int $storeId
     * @param int $fromId
     * @param int $toId
     *
     * @return array
     */
    private function _getExistingInStoreScope(
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute,
        $storeId,
        $fromId,
        $toId
    )
    {
        // Search for NON candidates for search in default Store View Id
        $alreadyExistInStoreSelect = new Zend_Db_Select($this->getDbAdapter());

        $scopeTable = $this->_getScopeTable($attribute);

        $alreadyExistInStoreSelect
            ->from([$scopeTable => $attribute->getBackendTable()], ['entity_id'])
            ->where("`{$scopeTable}`.`store_id` = ?", $storeId)
            ->where("`{$scopeTable}`.`entity_id` >= ?", $fromId)
            ->where("`{$scopeTable}`.`entity_id` <= ?", $toId)
            ->where("`{$scopeTable}`.`attribute_id` = ?", $attribute->getAttributeId());


        return $this->getDbAdapter()->fetchCol($alreadyExistInStoreSelect);
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param Mage_Rule_Model_Condition_Product_Abstract $condition
     * @param int $fromId
     * @param int $toId
     * @param array $alreadyExistInStoreViewIds
     *
     * @return array
     */
    private function _getExistingInDefaultScope(
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute,
        Mage_Rule_Model_Condition_Product_Abstract $condition,
        $fromId,
        $toId,
        array $alreadyExistInStoreViewIds
    )
    {
        // Search for matches in default Store View Id
        $defaultStoreViewSelect = new Zend_Db_Select($this->getDbAdapter());
        $this->attachAttributeValueCondition($defaultStoreViewSelect, $condition, $attribute);

        $scopeTable = $this->_getScopeTable($attribute);

        $defaultStoreViewSelect->where("`{$scopeTable}`.`store_id` = ?", Mage_Core_Model_App::ADMIN_STORE_ID)
            ->where("`{$scopeTable}`.`entity_id` >= ?", $fromId)
            ->where("`{$scopeTable}`.`entity_id` <= ?", $toId)
            ->where("`{$scopeTable}`.`attribute_id` = ?", $attribute->getAttributeId());

        if (!empty($alreadyExistInStoreViewIds)) {
            $defaultStoreViewSelect->where("`{$scopeTable}`.`entity_id` NOT IN (?)", implode(',', $alreadyExistInStoreViewIds));
        }

        return $this->getDbAdapter()->fetchCol($defaultStoreViewSelect);
    }

    /**
     * @param $attribute
     * @return string
     */
    private function _getScopeTable($attribute)
    {
        return $scopeTable = "at_scope_{$attribute->getAttributeCode()}";
    }

}