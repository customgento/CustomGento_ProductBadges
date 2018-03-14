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

        if ($isDesignChanged) {
            $this->_getCacheHelper()->clearCacheForBadge($origData['internal_code']);
        }

        //@todo: check if badge is still active
        /**
         * 1. Remove badge from all index tables
         * 2. Clear cache where the badge exists in a container
         */

        //@todo: check if badge is not valid in date range
        /**
         * 1. Remove badge from all index tables
         * 2. Clear cache where the badge exists in a container
         */

        //@todo: check if badge conditions changed && badge is active and active in date range
        if ($newData['conditions_serialized'] != $origData['conditions_serialized']) {
        /**
         * 1. Reindex badge
         * 2. Clear cache for affected product ids
         */
        }

        return;
    }

    /**
     * @return CustomGento_ProductBadges_Helper_Cache
     */
    protected function _getCacheHelper()
    {
        return Mage::helper('customgento_productbadges/cache');
    }

}