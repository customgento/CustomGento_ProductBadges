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
     * @return CustomGento_ProductBadges_Model_Resource_BadgeConfig_Collection
     */
    public function addFiltersNeededForIndexer()
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

        return $this;
    }

}