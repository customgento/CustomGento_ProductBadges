<?php

/**
 * Class CustomGento_ProductBadges_Model_BadgeConfig
 */
class CustomGento_ProductBadges_Model_BadgeConfig
    extends Mage_Rule_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'customgento_productbadges_badge_config';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getBadgeConfig() in this case
     *
     * @var string
     */
    protected $_eventObject = 'badge_config';

    /**
     * Set resource model and Id field name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('customgento_productbadges/badgeConfig');
        $this->setIdFieldName('badge_config_id');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return Mage_CatalogRule_Model_Rule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('customgento_productbadges/rule_condition_combine');
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return Mage_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('rule/action_collection');
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @param int $fromId
     * @param int $toId
     * @param int $storeId
     *
     * @return array Matching product IDs
     */
    public function getMatchingProductIds($fromId, $toId, $storeId)
    {
        $this->setCollectedAttributes(array());

        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $productCollection->addAttributeToSelect('entity_id');
        $productCollection->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $productCollection->addAttributeToFilter(
            'visibility',
            Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds()
        );

        $productCollection->addStoreFilter($storeId);

        $this->getConditions()->collectValidatedAttributes($productCollection);

        $select = $productCollection->getSelect();
        $select
            ->where('`e`.`entity_id` >= ?', $fromId)
            ->where('`e`.`entity_id` <= ?', $toId);
        // only apply the filter if conditions have been defined! otherwise, all products should be matched
        $conditions = $this->getConditions();
        if (!empty($conditions->getConditions())) {
            $select->where($this->transformConditionToSql($conditions, $fromId, $toId, $storeId));
        }

        $productIds = $productCollection->getAllIds();

        return $productIds;
    }

    /**
     * Transform rule condition to sql
     *
     * @param Mage_Rule_Model_Condition_Abstract $condition Rule condition $condition
     * @param int                                $fromId
     * @param int                                $toId
     * @param int                                $storeId
     *
     * @return Zend_Db_Expr
     * @throws CustomGento_ProductBadges_Exception_Transform
     */
    protected function transformConditionToSql(Mage_Rule_Model_Condition_Abstract $condition, $fromId, $toId, $storeId)
    {

        switch (true) {
            case $condition instanceof Mage_Rule_Model_Condition_Combine:
                $conditions = array_map(
                    Closure::bind(
                        function (Mage_Rule_Model_Condition_Abstract $condition) use ($fromId, $toId, $storeId) {
                            return $this->transformConditionToSql($condition, $fromId, $toId, $storeId);
                        },
                        $this
                    ),
                    $condition->getConditions()
                );

                $operator = $condition->getData('aggregator') === 'all' ? 'AND' : 'OR';

                return new \Zend_Db_Expr('(' . implode(") {$operator} (", $conditions) . ')');
            case $condition instanceof Mage_Rule_Model_Condition_Product_Abstract:

                return $this->transformProductConditionToSql($condition, $fromId, $toId, $storeId);
            default:
                $conditionClass = get_class($condition);
                throw new CustomGento_ProductBadges_Exception_Transform("Invalid '{$conditionClass}' condition.");
        }
    }

    /**
     * Transform product rule condition to sql
     *
     * @param Mage_Rule_Model_Condition_Product_Abstract $condition Rule condition
     * @param int                                        $fromId
     * @param int                                        $toId
     * @param int                                        $storeId
     *
     * @return Zend_Db_Expr
     * @throws CustomGento_ProductBadges_Exception_Transform
     */
    protected function transformProductConditionToSql(
        Mage_Rule_Model_Condition_Product_Abstract $condition,
        $fromId,
        $toId,
        $storeId
    ) {
        $attribute   = $condition->getAttributeObject();
        $transformer = null;

        switch (true) {
            case 'category_ids' === $attribute->getAttributeCode():
                $transformer = 'category';
                break;
            case $attribute->getData('backend_type') == 'static':
                $transformer = 'static';
                break;
            case CustomGento_ProductBadges_Model_Rule_Condition_Product_StockStatus::ATTRIBUTE_NAME
                === $attribute->getAttributeCode():
                $transformer = 'stockstatus';
                break;
            default:
                $transformer = 'store';
                break;
        }

        /** @var CustomGento_ProductBadges_Model_Condition_Transformer_Interface $transformer */
        $transformer = Mage::getSingleton('customgento_productbadges/condition_transformer_' . $transformer);

        if (!$transformer instanceof CustomGento_ProductBadges_Model_Condition_Transformer_Interface) {
            throw new CustomGento_ProductBadges_Exception_Transform("Couldn't transform condition!");
        }

        return $transformer->transform($condition, $fromId, $toId, $storeId);
    }

    /**
     * Get database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getDbAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }

    /**
     * Get table name
     *
     * @param string $alias Model alias
     *
     * @return string
     */
    protected function getTableName($alias)
    {
        return Mage::getSingleton('core/resource')->getTableName($alias);
    }

    /**
     * Log debug information
     *
     * @param string $message Message
     * @param int    $level   Log level
     */
    protected function log($message, $level = Zend_Log::INFO)
    {
        Mage::log($message, $level, 'customegento_product_badges.log');
    }

    /**
     * Creates an internal code from the given entity ID.
     *
     * @param $id
     *
     * @return string
     */
    public static function getInternalCodeFromId($id)
    {
        // make sure that we produce a valid column name for the index table
        $id = (int)$id;

        return 'badge_' . $id;
    }

    /**
     * @return string
     */
    public function getInternalCode()
    {
        return static::getInternalCodeFromId($this->getId());
    }
}