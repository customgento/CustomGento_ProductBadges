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
        return new Mage_Rule_Model_Action_Collection();
    }


    //Test code
    /**
     * Get array of product ids which are matched by rule
     * @param int $fromId
     * @param int $toId
     *
     * @return array Matching product IDs
     * @throws CustomGento_ProductBadges_Exception_Transform
     */
    public function getMatchingProductIds($fromId, $toId)
    {
        $this->log('Start matching products for rule');

        $productIds = array();
        $this->setCollectedAttributes(array());
        //$websiteIds = explode(',', $this->getWebsiteIds());
        $websiteIds = array(1);

        if ($websiteIds) {
            /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
            $productCollection = clone Mage::getResourceModel('catalog/product_collection');
            $productCollection->addAttributeToSelect('entity_id');
            $productCollection->addWebsiteFilter($websiteIds);

            $this->getConditions()->collectValidatedAttributes($productCollection);

//            try {
                $select = $productCollection->getSelect();
                $select
                    ->where('`e`.`entity_id` >= ?', $fromId)
                    ->where('`e`.`entity_id` <= ?', $toId)
                    ->where($this->transformConditionToSql($this->getConditions())
                );

                $this->log('SQL: ' . $select);
                $productIds = $productCollection->getAllIds();
//            } catch (Exception $e) {
//                Mage::logException($e);
//                $this->log('Exception: ' . $e, Zend_Log::ERR);
//
//                $productCollection = clone Mage::getResourceModel('catalog/product_collection');
//                $productCollection->addWebsiteFilter($websiteIds);
//
//                $this->getConditions()->collectValidatedAttributes($productCollection);
//
//                // Fallback to default implementation
//                Mage::getSingleton('core/resource_iterator')->walk(
//                    $productCollection->getSelect(),
//                    array(array($this, 'callbackValidateProduct')),
//                    array(
//                        'attributes' => $this->getCollectedAttributes(),
//                        'product' => Mage::getModel('catalog/product'),
//                    )
//                );
//            }
        }

        $this->log('Finish matching products');

        return $productIds;
    }

    /**
     * Transform rule condition to sql
     *
     * @param Mage_Rule_Model_Condition_Abstract $condition Rule condition
     *
     * @return Zend_Db_Expr
     * @throws CustomGento_ProductBadges_Exception_Transform
     */
    protected function transformConditionToSql(Mage_Rule_Model_Condition_Abstract $condition)
    {
        switch (true) {
            case $condition instanceof Mage_Rule_Model_Condition_Combine:
                $conditions = array_map(Closure::bind(function(Mage_Rule_Model_Condition_Abstract $condition) {
                    return $this->transformConditionToSql($condition);
                }, $this), $condition->getConditions());

                $operator = $condition->getData('aggregator') === 'all' ? 'AND' : 'OR';

                return new \Zend_Db_Expr('(' . implode(") {$operator} (", $conditions) . ')');
            case $condition instanceof Mage_Rule_Model_Condition_Product_Abstract:

                return $this->transformProductConditionToSql($condition);
            default:
                $conditionClass = get_class($condition);
                throw new CustomGento_ProductBadges_Exception_Transform("Invalid '{$conditionClass}' condition.");
        }
    }

    /**
     * Transform product rule condition to sql
     *
     * @param Mage_Rule_Model_Condition_Product_Abstract $condition Rule condition
     *
     * @return Zend_Db_Expr
     * @throws CustomGento_ProductBadges_Exception_Transform
     */
    protected function transformProductConditionToSql(Mage_Rule_Model_Condition_Product_Abstract $condition)
    {
        $attribute = $condition->getAttributeObject();
        $transformer = null;

        switch (true) {
            case 'category_ids' === $attribute->getAttributeCode():
                $transformer = 'category';
                break;
            case 'type_id' == $attribute->getAttributeCode():
                $transformer = 'global';
                break;
            case CustomGento_ProductBadges_Model_Rule_Condition_Product_StockStatus::ATTRIBUTE_NAME === $attribute->getAttributeCode():
                $transformer = 'stockstatus';
                break;
//            case CustomGento_ProductBadges_Model_Rule_Condition_Vitafy_Discount::ATTRIBUTE_NAME == $attribute->getAttributeCode():
//                $transformer = 'discountAmount';
//                break;
            case !$attribute->isScopeGlobal():
                $transformer = 'store';
                break;
            default:
                $transformer = 'global';
                break;
        }

        /** @var CustomGento_ProductBadges_Model_Condition_Transformer_Interface $transformer */
        $transformer = Mage::getSingleton('customgento_productbadges/condition_transformer_' . $transformer);

        if (!$transformer instanceof CustomGento_ProductBadges_Model_Condition_Transformer_Interface) {
            throw new CustomGento_ProductBadges_Exception_Transform("Couldn't transform condition!");
        }

        return $transformer->transform($condition);
    }

    /**
     * Get database adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    protected function getDbAdapter()
    {
        return  Mage::getSingleton('core/resource')->getConnection('core_read');
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

}