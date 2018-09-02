<?php

class CustomGento_ProductBadges_Model_Condition_Transformer_DayInterval
    extends CustomGento_ProductBadges_Model_Condition_Transformer_Abstract
{
    /**
     * @inheritdoc
     */
    public function transform(Mage_Rule_Model_Condition_Product_Abstract $condition, $fromId, $toId, $storeId)
    {
        $operator = $condition->getOperatorForValidate();
        $operator = str_replace('==', '=', $operator);

        $days = $condition->getValueParsed();

        $attribute = $attribute = $condition->getAttributeObject();

        // In case we have date attribute from catalog_product_entity table
        if ('static' == $attribute->getData('backend_type')) {
            $attributeCode = $attribute->getAttributeCode();

            return new Zend_Db_Expr(
                $this->getDbAdapter()
                    ->quoteInto("{$attributeCode} {$operator} NOW() - INTERVAL  ? DAY", $days)
            );
        }

        return $this->_transformEav($attribute, $operator, $days, $fromId, $toId, $storeId);
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string                                    $operator
     * @param int                                       $days
     * @param int                                       $fromId
     * @param int                                       $toId
     * @param int                                       $storeId
     * @return                                          Zend_Db_Expr
     */
    protected function _transformEav(
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute,
        $operator,
        $days,
        $fromId,
        $toId,
        $storeId
    )
    {
        // Case when attribute has store scope
        if ($attribute->isScopeStore()) {
            $storeMatchesData = $this->_getMatchesForStoreView($attribute, $operator, $days, $fromId, $toId, $storeId);

            $alreadyExistInStoreViewIds = $this->_getExistingInStoreScope($attribute, $fromId, $toId, $storeId);

            $defaultStoreMatchesData = $this->_getExistingInDefaultScope(
                $attribute,
                $operator,
                $days,
                $fromId,
                $toId,
                $alreadyExistInStoreViewIds
            );

            $matches = array_merge($storeMatchesData, $defaultStoreMatchesData);

            if (empty($matches)) {
                // We return false in order to mark that sub-query had no result
                return new Zend_Db_Expr("1 != 1");
            }

            return new Zend_Db_Expr("`e`.`entity_id` IN (" . implode(",", $matches) . ")");
        }

        // Case when attribute has global scope
        $scopeTable = $this->_getScopeTable($attribute);
        $select     = new Zend_Db_Select($this->getDbAdapter());
        $select->where("`e`.`entity_id` = `{$scopeTable}`.`entity_id`");
        $this->attachAttributeValueCondition($select, $operator, $days, $attribute);

        return new Zend_Db_Expr("EXISTS ({$select})");
    }

    /**
     * @param Zend_Db_Select                             $select
     * @param string                                     $operator
     * @param int                                        $days
     * @param Mage_Catalog_Model_Resource_Eav_Attribute  $attribute
     */
    protected function attachAttributeValueCondition(
        Zend_Db_Select $select,
        $operator,
        $days,
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute
    ) {
        $scopeTable = $this->_getScopeTable($attribute);

        $select
            ->from(array($scopeTable => $attribute->getBackendTable()), array('entity_id'))
            ->where("`{$scopeTable}`.`attribute_id` = ?", $attribute->getAttributeId());

        $select->where("`{$scopeTable}`.`value` {$operator} NOW() - INTERVAL  ? DAY", $days);
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute  $attribute
     * @param string                                     $operator
     * @param int                                        $days
     * @param int                                        $fromId
     * @param int                                        $toId
     * @param int                                        $storeId
     *
     * @return array
     */
    protected function _getMatchesForStoreView(
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute,
        $operator,
        $days,
        $fromId,
        $toId,
        $storeId
    ) {
        $storeViewSelect = new Zend_Db_Select($this->getDbAdapter());
        $this->attachAttributeValueCondition($storeViewSelect, $operator, $days, $attribute);

        $scopeTable = $this->_getScopeTable($attribute);

        // Fetch the matches for currently scanned Store View Id
        $storeViewSelect->where("`{$scopeTable}`.`store_id` = ?", $storeId)
            ->where("`{$scopeTable}`.`entity_id` >= ?", $fromId)
            ->where("`{$scopeTable}`.`entity_id` <= ?", $toId);

        return $this->getDbAdapter()->fetchCol($storeViewSelect);
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param int                                       $fromId
     * @param int                                       $toId
     * @param int                                       $storeId
     *
     * @return array
     */
    protected function _getExistingInStoreScope(
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute,
        $fromId,
        $toId,
        $storeId
    ) {
        // Search for NON candidates for search in default Store View Id
        $alreadyExistInStoreSelect = new Zend_Db_Select($this->getDbAdapter());

        $scopeTable = $this->_getScopeTable($attribute);

        $alreadyExistInStoreSelect
            ->from(array($scopeTable => $attribute->getBackendTable()), array('entity_id'))
            ->where("`{$scopeTable}`.`store_id` = ?", $storeId)
            ->where("`{$scopeTable}`.`entity_id` >= ?", $fromId)
            ->where("`{$scopeTable}`.`entity_id` <= ?", $toId)
            ->where("`{$scopeTable}`.`attribute_id` = ?", $attribute->getAttributeId());


        return $this->getDbAdapter()->fetchCol($alreadyExistInStoreSelect);
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute  $attribute
     * @param                                            $operator
     * @param                                            $days
     * @param int                                        $fromId
     * @param int                                        $toId
     * @param array                                      $alreadyExistInStoreViewIds
     *
     * @return array
     */
    protected function _getExistingInDefaultScope(
        Mage_Catalog_Model_Resource_Eav_Attribute $attribute,
        $operator,
        $days,
        $fromId,
        $toId,
        array $alreadyExistInStoreViewIds
    ) {
        // Search for matches in default Store View Id
        $defaultStoreViewSelect = new Zend_Db_Select($this->getDbAdapter());
        $this->attachAttributeValueCondition($defaultStoreViewSelect, $operator, $days, $attribute);

        $scopeTable = $this->_getScopeTable($attribute);

        $defaultStoreViewSelect->where("`{$scopeTable}`.`store_id` = ?", Mage_Core_Model_App::ADMIN_STORE_ID)
            ->where("`{$scopeTable}`.`entity_id` >= ?", $fromId)
            ->where("`{$scopeTable}`.`entity_id` <= ?", $toId)
            ->where("`{$scopeTable}`.`attribute_id` = ?", $attribute->getAttributeId());

        if (!empty($alreadyExistInStoreViewIds)) {
            $defaultStoreViewSelect->where(
                "`{$scopeTable}`.`entity_id` NOT IN (?)",
                implode(',', $alreadyExistInStoreViewIds)
            );
        }

        return $this->getDbAdapter()->fetchCol($defaultStoreViewSelect);
    }

    /**
     * @param $attribute
     *
     * @return string
     */
    protected function _getScopeTable($attribute)
    {
        return $scopeTable = "at_scope_{$attribute->getAttributeCode()}";
    }

}
