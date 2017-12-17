<?php
class CustomGento_ProductBadges_Model_Config_RenderTypeData
{

    private $_badgesMapping = false;

    /**
     * @param string $badgeInternalCode
     *
     * @return CustomGento_ProductBadges_Model_BadgeConfig|false
     */
    public function getBadgeConfig($badgeInternalCode)
    {
        $badgeConfigCollection = Mage::getModel('customgento_productbadges/badgeConfig')
            ->getCollection()
            ->addFieldToSelect(array('badge_text', 'internal_code', 'badge_image'));

        if (false === $this->_badgesMapping) {
            /** @var CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig */
            foreach ($badgeConfigCollection as $badgeConfig) {
                $this->_badgesMapping[$badgeConfig->getInternalCode()] = $badgeConfig;
            }
        }

        return isset($this->_badgesMapping[$badgeInternalCode]) ?
            $this->_badgesMapping[$badgeInternalCode] :
            false;
    }

}