<?php
class CustomGento_ProductBadges_Model_Resource_BadgeConfig_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Set resource model and determine field mapping
     */
    protected function _construct()
    {
        $this->_init('customgento_productbadges/badgeConfig');
    }

    /**
     * @param int $storeId
     *
     * @return CustomGento_ProductBadges_Model_Resource_BadgeConfig_Collection
     */
    public function addFiltersNeededForIndexer($storeId)
    {
        $now = Mage::getModel('core/date')->date('Y-m-d');
        $this->addFieldToFilter('from_date', array(
            array('null' => true),
            array('lteq' => $now)
        ));
        $this->addFieldToFilter('to_date', array(
            array('null' => true),
            array('gteq' => $now)
        ));
        $this->addFieldToFilter('is_active', 1);


        $this->getSelect()->where($this->_getStoreMatchingExpression($storeId));

        return $this;
    }

    /**
     * @param int $storeId
     * @return Zend_Db_Expr
     */
    protected function _getStoreMatchingExpression($storeId)
    {
        $storeExpression = array();

        $defaultStoreViewId = Mage_Core_Model_App::ADMIN_STORE_ID;

        $storeExpression[] = $this->getResource()->getReadConnection()
            ->quoteInto("store_ids REGEXP ?", "(^|,){$defaultStoreViewId}(,|$)");

        $storeExpression[] = $this->getResource()->getReadConnection()
            ->quoteInto("store_ids REGEXP ?", "(^|,){$storeId}(,|$)");


        return new Zend_Db_Expr('(' . implode(') OR (', $storeExpression) . ')');
    }

}