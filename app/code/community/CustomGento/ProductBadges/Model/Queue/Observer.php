<?php
class CustomGento_ProductBadges_Model_Queue_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerBadgeChange(Varien_Event_Observer $observer)
    {
        $queueRegister = new CustomGento_ProductBadges_Model_Queue_Register();
        $badgeConfig = $observer->getBadgeConfig();

        $job = new CustomGento_ProductBadges_Model_Queue_Job_BadgeConditionChange();

        $queueRegister->attemptToRegisterJob($job, $badgeConfig);
    }

}