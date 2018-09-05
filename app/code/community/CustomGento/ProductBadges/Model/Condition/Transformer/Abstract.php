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
        /** @todo: Check all operators if we didn't miss any. */
        return !in_array($operator, array('!{}', '{}', '()', '!()', '[]', '![]'));
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
        /** @todo: Check all operators if we didn't miss any. */
        return in_array($operator, array('!=', '!{}', '!()', '![]')) ? 'NOT' : '';
    }

    /**
     * When we have many subconditions we have to decide
     * how we concatenate them with OR or AND
     *
     * @param string $operator Operator
     *
     * @return string
     */
    public function orAndCondition($operator)
    {
        /** @todo: Check all operators if we didn't miss any. */
        return in_array($operator, array('[]', '![]')) ? 'AND' : 'OR';
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
