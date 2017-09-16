<?php

abstract class CustomGento_ProductBadges_Model_Condition_Transformer_Abstract
    implements CustomGento_ProductBadges_Model_Condition_Transformer_Interface
{
    /**
     * Is operator for scalar types?
     *
     * @param string $operator Operator
     *
     * @return bool
     */
    public function isScalarOperator($operator)
    {
        return !in_array($operator, ['!{}', '{}', '()', '!()']);
    }

    /**
     * Get operator prefix
     *
     * @param string $operator Operator
     *
     * @return string
     */
    public function getOperatorPrefix($operator)
    {
        return in_array($operator, ['!=', '!{}', '!()']) ? 'NOT' : '';
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

}