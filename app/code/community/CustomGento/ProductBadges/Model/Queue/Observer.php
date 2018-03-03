<?php
class CustomGento_ProductBadges_Model_Queue_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerBadgeChange(Varien_Event_Observer $observer)
    {
        $queueRegisterJob = new CustomGento_ProductBadges_Model_Queue_RegisterJob();
        $badgeConfig = $observer->getBadgeConfig();

        $job = new CustomGento_ProductBadges_Model_Queue_Job_BadgeConditionChange();

        $queueRegisterJob->attemptToRegisterJob($job, $badgeConfig);
    }

}