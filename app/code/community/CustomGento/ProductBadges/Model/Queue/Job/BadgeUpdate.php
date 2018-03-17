<?php
class CustomGento_ProductBadges_Model_Queue_Job_BadgeUpdate
    extends CustomGento_ProductBadges_Model_Queue_Job_Abstract
{

    /**
     * @param Varien_Object $badgeConfig
     * @return array
     */
    public function getPreparedDataForJobAction(Varien_Object $badgeConfig)
    {
        return array('new_data' => $badgeConfig->getData(), 'orig_data' => $badgeConfig->getOrigData());
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function processJobAction(array $data)
    {
        $renderFields = array(
            'render_type',
            'render_container',
            'badge_text',
            'badge_image',
            'badge_background_color',
            'badge_font_family',
            'badge_font_size'
        );

        $newData  = $data['new_data'];
        $origData = $data['orig_data'];

        $isDesignChanged = false;

        foreach ($renderFields as $field) {
            if ($newData[$field] != $origData[$field]) {
                $isDesignChanged = true;
                break;
            }
        }


        if ($this->_wasBadgeDisabled($newData, $origData)) {
            $this->_getProductBadgesIndexerResource()->badgeDisablingReindex($origData['internal_code']);
            $this->_getCacheHelper()->clearCacheForBadge($origData['internal_code']);
            return $this;
        }

        if ($this->_wasBadgeEnabled($newData, $origData)) {
            // We rebuild all badges because there are too many edge cases
            $this->_getProductBadgesIndexerResource()->rebuild();
            $this->_getCacheHelper()->clearAllBadgeCache();
            return $this;
        }

        if ($newData['conditions_serialized'] != $origData['conditions_serialized']) {
            // We rebuild all badges because there are too many edge cases
            $this->_getProductBadgesIndexerResource()->rebuild();
            $this->_getCacheHelper()->clearAllBadgeCache();
            return $this;
        }

        if ($isDesignChanged) {
            $this->_getCacheHelper()->clearCacheForBadge($origData['internal_code']);
        }

        return $this;
    }

    /**
     * Speculative function trying to determine what was the old state
     * and is the the badge state going to change to Disabled
     *
     * We check the 'is_active' flag but we also have to check from_date and to_date
     *
     * @param array $newData
     * @param array $oldData
     *
     * @return bool
     */
    protected function _wasBadgeEnabled(array $newData, array $oldData)
    {
        $isActiveInNewPeriod = Mage::app()
            ->getLocale()
            ->isStoreDateInInterval(Mage_Core_Model_App::ADMIN_STORE_ID, $newData['from_date'], $newData['to_date']);

        $wasActiveInOldPeriod = Mage::app()
            ->getLocale()
            ->isStoreDateInInterval(Mage_Core_Model_App::ADMIN_STORE_ID, $oldData['from_date'], $oldData['to_date']);

        if ($newData['is_active'] == '1' && $oldData['is_active'] != '1') {
            return $isActiveInNewPeriod;
        }

        if ($this->_wasBadgePeriodChanged($newData, $oldData)) {
            return (!$wasActiveInOldPeriod && $isActiveInNewPeriod);
        }

        return false;
    }

    /**
     * Speculative function trying to determine what was the old state
     * and is the the badge state going to change to Disabled
     *
     * We check the 'is_active' flag but we also have to check from_date and to_date
     *
     * @param array $newData
     * @param array $oldData
     *
     * @return bool
     */
    protected function _wasBadgeDisabled(array $newData, array $oldData)
    {
        $isActiveInNewPeriod = Mage::app()
            ->getLocale()
            ->isStoreDateInInterval(Mage_Core_Model_App::ADMIN_STORE_ID, $newData['from_date'], $newData['to_date']);

        $wasActiveInOldPeriod = Mage::app()
            ->getLocale()
            ->isStoreDateInInterval(Mage_Core_Model_App::ADMIN_STORE_ID, $oldData['from_date'], $oldData['to_date']);

        // Active flag was changed
        if ($newData['is_active'] == '0' && $oldData['is_active'] == '1') {
            return $wasActiveInOldPeriod;
        }

        if ($this->_wasBadgePeriodChanged($newData, $oldData)) {
            return ($wasActiveInOldPeriod && !$isActiveInNewPeriod);
        }

        return false;
    }

    private function _wasBadgePeriodChanged(array $newData, array $oldData)
    {
        return !($newData['from_date'] == $oldData['from_date'] && $newData['to_date'] == $oldData['to_date']);
    }

    /**
     * @return CustomGento_ProductBadges_Helper_Cache
     */
    protected function _getCacheHelper()
    {
        return Mage::helper('customgento_productbadges/cache');
    }

    /**
     * @return CustomGento_ProductBadges_Model_Resource_Indexer_ProductBadges
     */
    protected function _getProductBadgesIndexerResource()
    {
        return Mage::getResourceModel('customgento_productbadges/indexer_productBadges');
    }

}