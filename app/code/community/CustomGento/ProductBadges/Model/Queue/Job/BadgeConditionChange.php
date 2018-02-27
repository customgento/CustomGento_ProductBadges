<?php
class CustomGento_ProductBadges_Model_Queue_Job_BadgeConditionChange
    extends CustomGento_ProductBadges_Model_Queue_Job_Abstract
{

    /**
     * @param Varien_Object $badgeConfig
     * @return array
     */
    public function getPreparedDataForJobAction(Varien_Object $badgeConfig)
    {
        return array('badge_config_id' => $badgeConfig->getId());
    }

    /**
     * @param array $data
     */
    public function processJobAction(array $data)
    {
        return;
    }

}