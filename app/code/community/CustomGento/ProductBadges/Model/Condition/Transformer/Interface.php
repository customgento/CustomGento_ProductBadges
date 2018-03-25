<?php

interface CustomGento_ProductBadges_Model_Condition_Transformer_Interface
{
    /**
     * Transform rule condition to 'SQL WHERE' condition
     *
     * @param Mage_Rule_Model_Condition_Product_Abstract $condition Rule condition
     * @param int $storeId
     * @param int $fromId
     * @param int $toId
     *
     * @return \Zend_Db_Expr|string
     */
    public function transform(Mage_Rule_Model_Condition_Product_Abstract $condition, $storeId, $fromId, $toId);

}