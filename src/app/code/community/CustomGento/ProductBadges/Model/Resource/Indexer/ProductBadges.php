<?php

class CustomGento_ProductBadges_Model_Resource_Indexer_ProductBadges
    extends Mage_Index_Model_Resource_Abstract
{
    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_resources = Mage::getSingleton('core/resource');
        $this->_init('customgento_productbadges/badge_config', 'badge_config_id');
    }

    /**
     * @param int $storeId
     *
     * @return CustomGento_ProductBadges_Model_Scanner_ProductBadges
     */
    protected function _spawnProductBadges($storeId)
    {
        /** @var CustomGento_ProductBadges_Model_Scanner_ProductBadges $productBadgesScanner */
        $productBadgesScanner = Mage::getModel('customgento_productbadges/scanner_productBadges');

        return $productBadgesScanner->init($storeId);
    }

    protected $_badgesIndexTableNamePrefix = 'customgento_productbadges_badges_index_';

    /**
     * Flat tables which were prepared
     *
     * @var array
     */
    protected $_preparedFlatTables = array();

    /**
     * Exists flat tables cache
     *
     * @var array
     */
    protected $_existsFlatTables = array();

    /**
     * @param int $storeId
     *
     * @return string
     */
    protected function getFlatTableName($storeId)
    {
        return $this->_getReadAdapter()
            ->getTableName($this->_badgesIndexTableNamePrefix . $storeId);
    }

    public function reindexAll()
    {
        Mage::dispatchEvent(
            'customgento_productbadges_reindexall_before',
            array('indexer' => $this)
        );

        $this->rebuild();

        Mage::dispatchEvent(
            'customgento_productbadges_reindexall_after',
            array('indexer' => $this)
        );
    }

    /**
     * Rebuild Catalog Product Flat Data
     *
     * @param Mage_Core_Model_Store $store
     * @param array                 $productIds
     *
     * @return CustomGento_ProductBadges_Model_Resource_Indexer_ProductBadges
     */
    public function rebuild($store = null, $productIds = array())
    {
        if (!$this->_getHelper()->isEnabled()) {
            return $this;
        }

        if ($store === null) {
            $notActiveStores = array();

            /** @var Mage_Core_Model_Store $store */
            foreach (Mage::app()->getStores() as $store) {
                if ($store->getIsActive()) {
                    $this->rebuild($store, $productIds);
                } else {
                    $notActiveStores[] = $store;
                }
            }

            // Cleaning index tables for non active stores
            foreach ($notActiveStores as $store) {
                $this->dropIndexTableByStoreId($store->getId());
            }

            return $this;
        }

        $storeId = (int)$store->getId();

        $productBadges = $this->_spawnProductBadges($storeId);

        $this->prepareFlatTable($storeId, $productBadges);

        while ($productBadges->possibleToFetchMoreBadges()) {
            $badgesData = $productBadges->fetchBadges($productIds);

            $preparedForInsertData = array();

            if (!isset($badgesData['found_badges'])) {
                continue;
            }

            foreach ($badgesData['found_badges'] as $badgeCode => $foundProductIds) {
                foreach ($foundProductIds as $productId) {
                    // Here we pre-fill default values
                    if (!isset($preparedForInsertData[$productId])) {
                        $preparedForInsertData[$productId] = array();

                        $preparedForInsertData[$productId] = array_fill_keys(
                            $productBadges->getProductBadgeCodes(),
                            0
                        );

                        $preparedForInsertData[$productId]['product_id'] = $productId;
                    }

                    $preparedForInsertData[$productId][$badgeCode] = 1;
                }
            }

            if (!empty($preparedForInsertData)) {
                // Update / Insert new Badges
                $insertedRows = $this->_getWriteAdapter()->insertOnDuplicate(
                    $this->getFlatTableName($storeId),
                    $preparedForInsertData
                );
            }

            //Delete badges that should not be presented anymore
            $deletedRows = $this->_deleteOutdatedBadges($storeId, $badgesData, $preparedForInsertData);
        }

        return $this;
    }

    protected function _deleteOutdatedBadges($storeId, $badgesData, $preparedForInsertData)
    {
        $condition = array();

        $condition[] = $this->_getWriteAdapter()
            ->quoteInto('product_id >= ?', $badgesData['product_id_scanned_from']);

        $condition[] = $this->_getWriteAdapter()
            ->quoteInto('product_id <= ?', $badgesData['product_id_scanned_to']);

        // Covering edge case when all badges got outdated in product range
        if (!empty($preparedForInsertData)) {
            $condition[] = $this->_getWriteAdapter()
                ->quoteInto('product_id NOT IN(?)', array_keys($preparedForInsertData));
        }

        $affectedDeletedRowsRows = $this->_getWriteAdapter()
            ->delete(
                $this->getFlatTableName($storeId),
                implode(' AND ', $condition)
            );

        return $affectedDeletedRowsRows;
    }


    /**
     * Retrieve catalog product flat columns array in DDL format
     *
     * @param CustomGento_ProductBadges_Model_Scanner_ProductBadges $productBadges
     *
     * @return array
     */
    protected function getFlatColumns(CustomGento_ProductBadges_Model_Scanner_ProductBadges $productBadges)
    {
        $columns = array();

        $columns['product_id'] = array(
            'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length'   => null,
            'unsigned' => true,
            'nullable' => false,
            'default'  => false,
            'primary'  => true,
            'comment'  => 'Product Id'
        );

        $badgeCodes = $productBadges->getProductBadgeCodes();

        foreach ($badgeCodes as $code) {
            $columns[$code] = array(
                'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'length'   => null,
                'unsigned' => true,
                'nullable' => false,
                'default'  => false,
                'comment'  => $code
            );
        }

        return $columns;
    }

    /**
     * Check is flat table for store exists
     *
     * @param int $storeId
     *
     * @return bool
     */
    protected function _isFlatTableExists($storeId)
    {
        if (!isset($this->_existsFlatTables[$storeId])) {
            $tableName     = $this->getFlatTableName($storeId);
            $isTableExists = $this->_getWriteAdapter()->isTableExists($tableName);

            $this->_existsFlatTables[$storeId] = $isTableExists;
        }

        return $this->_existsFlatTables[$storeId];
    }

    /**
     * Retrieve UNIQUE HASH for a Table foreign key
     *
     * @param string $priTableName  the target table name
     * @param string $priColumnName the target table column name
     * @param string $refTableName  the reference table name
     * @param string $refColumnName the reference table column name
     *
     * @return string
     */
    public function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        return Mage::getSingleton('core/resource')
            ->getFkName($priTableName, $priColumnName, $refTableName, $refColumnName);
    }

    /**
     * Prepare flat table for store
     *
     * @param int                                                   $storeId
     * @param CustomGento_ProductBadges_Model_Scanner_ProductBadges $productBadges
     *
     * @return CustomGento_ProductBadges_Model_Resource_Indexer_ProductBadges
     * @throws Zend_Db_Exception
     */
    public function prepareFlatTable($storeId, CustomGento_ProductBadges_Model_Scanner_ProductBadges $productBadges)
    {
        if (isset($this->_preparedFlatTables[$storeId])) {
            return $this;
        }

        $adapter   = $this->_getWriteAdapter();
        $tableName = $this->getFlatTableName($storeId);

        // Extract columns we need to have in flat table
        $columns = $this->getFlatColumns($productBadges);

        // Foreign keys
        $foreignEntityKey = $this->getFkName($tableName, 'product_id', 'catalog/product', 'entity_id');

        // Create table or modify existing one
        if (!$this->_isFlatTableExists($storeId)) {
            /** @var $table Varien_Db_Ddl_Table */
            $table = $adapter->newTable($tableName);
            foreach ($columns as $fieldName => $fieldProp) {
                $table->addColumn(
                    $fieldName,
                    $fieldProp['type'],
                    isset($fieldProp['length']) ? $fieldProp['length'] : null,
                    array(
                        'nullable' => isset($fieldProp['nullable']) ? (bool)$fieldProp['nullable'] : false,
                        'unsigned' => isset($fieldProp['unsigned']) ? (bool)$fieldProp['unsigned'] : false,
                        'default'  => isset($fieldProp['default']) ? $fieldProp['default'] : false,
                        'primary'  => isset($fieldProp['primary']) ? $fieldProp['primary'] : false,
                    ),
                    isset($fieldProp['comment']) ? $fieldProp['comment'] : $fieldName
                );
            }

            $table->addForeignKey(
                $foreignEntityKey,
                'product_id',
                $this->getTable('catalog/product'),
                'entity_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE,
                Varien_Db_Ddl_Table::ACTION_CASCADE
            );

            $table->setComment("Product Badges Flat (Store {$storeId})");

            $adapter->createTable($table);

            $this->_existsFlatTables[$storeId] = true;
        } else {
            // Modify existing table
            $adapter->resetDdlCache($tableName);

            // Sort columns into added/altered/dropped lists
            $describe    = $adapter->describeTable($tableName);
            $addColumns  = array_diff_key($columns, $describe);
            $dropColumns = array_diff_key($describe, $columns);

            // Drop columns
            foreach (array_keys($dropColumns) as $columnName) {
                $adapter->dropColumn($tableName, $columnName);
            }

            // Add columns
            foreach ($addColumns as $columnName => $columnProp) {
                $columnProp = array_change_key_case($columnProp, CASE_UPPER);
                if (!isset($columnProp['COMMENT'])) {
                    $columnProp['COMMENT'] = ucwords(str_replace('_', ' ', $columnName));
                }

                $adapter->addColumn($tableName, $columnName, $columnProp);
            }
        }

        $this->_preparedFlatTables[$storeId] = true;

        return $this;
    }

    /**
     * Scan all index table for a badge and if badge exists write 0 for all products
     *
     * @param string $code
     *
     * @return $this
     */
    public function badgeDisablingReindex($code)
    {
        /** @var Mage_Core_Model_Store $store */
        foreach (Mage::app()->getStores() as $store) {
            if ($store->getIsActive()) {
                $tableName = $this->getFlatTableName($store->getId());
                if ($this->_getWriteAdapter()->isTableExists($tableName)) {
                    $describe = $this->_getWriteAdapter()->describeTable($tableName);
                    if (array_key_exists($code, $describe)) {
                        // Write 0 for and make the badge not active, soft disable
                        $this->_getWriteAdapter()->update($tableName, array($code => 0));
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param int $storeId
     */
    public function dropIndexTableByStoreId($storeId)
    {
        $tableName = $this->getFlatTableName($storeId);
        /**
         * This does "DROP TABLE IF EXISTS" so we don't have
         * to be worried if the table does not exist
         */
        $this->_getWriteAdapter()->dropTable($tableName);
    }

    /**
     *
     * @return CustomGento_ProductBadges_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('customgento_productbadges');
    }
}
