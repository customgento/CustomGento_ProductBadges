<?php

class CustomGento_ProductBadges_Model_Admin_BadgeConfig_Grid_Filter_Store
    extends Varien_Object
{

    /**
     * Website collection
     * websiteId => Mage_Core_Model_Website
     *
     * @var array
     */
    protected $_websiteCollection = array();

    /**
     * Group collection
     * groupId => Mage_Core_Model_Store_Group
     *
     * @var array
     */
    protected $_groupCollection = array();

    /**
     * Store collection
     * storeId => Mage_Core_Model_Store
     *
     * @var array
     */
    protected $_storeCollection;

    /**
     * Init model
     * Load Website, Group and Store collections
     *
     * @return CustomGento_ProductBadges_Model_Admin_BadgeConfig_Grid_Filter_Store
     */
    public function __construct()
    {
        $this->_loadWebsiteCollection();
        $this->_loadGroupCollection();
        $this->_loadStoreCollection();

        return $this;
    }

    /**
     * Load/Reload Website collection
     *
     * @return array
     */
    protected function _loadWebsiteCollection()
    {
        $this->_websiteCollection = Mage::app()->getWebsites();
        return $this;
    }

    /**
     * Load/Reload Group collection
     *
     * @return array
     */
    protected function _loadGroupCollection()
    {
        $this->_groupCollection = array();
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $this->_groupCollection[$group->getId()] = $group;
            }
        }
        return $this;
    }

    /**
     * Load/Reload Store collection
     *
     * @return array
     */
    protected function _loadStoreCollection()
    {
        $this->_storeCollection = Mage::app()->getStores();
        return $this;
    }

    /**
     * Retrieve formatted store values for grid filter and cell
     *
     * @return array
     */
    public function getStoreOptionHash()
    {
        $options = array();

        $options[Mage_Core_Model_App::ADMIN_STORE_ID] = Mage::helper('customgento_productbadges')->__('All Store Views');

        foreach ($this->_websiteCollection as $website) {
            foreach ($this->_groupCollection as $group) {
                if ($website->getId() != $group->getWebsiteId()) {
                    continue;
                }

                foreach ($this->_storeCollection as $store) {
                    if ($group->getId() != $store->getGroupId()) {
                        continue;
                    }

                    $options[$store->getId()] = $website->getName() . ' > ' . $group->getName() . ' > ' .$store->getName();
                }
            }
        }

        return $options;
    }

}
