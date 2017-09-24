<?php
class CustomGento_ProductBadges_Model_Config_BadgeText
{

    private $_badgeStaticTextMapping = false;

    /**
     * @param string $badgeInternalCode
     *
     * @return string
     */
    public function getBadgeText($badgeInternalCode)
    {
        $badgeConfigCollection = Mage::getModel('customgento_productbadges/badgeConfig')
            ->getCollection()
            ->addFieldToSelect(array('badge_text', 'internal_code'));

        if (false === $this->_badgeStaticTextMapping) {
            /** @var CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig */
            foreach ($badgeConfigCollection as $badgeConfig) {
                $this->_badgeStaticTextMapping[$badgeConfig->getInternalCode()] = $badgeConfig->getBadgeText();
            }
        }

        return isset($this->_badgeStaticTextMapping[$badgeInternalCode])
            // We fetch the configured badge static text
            ? Mage::helper('core')->escapeHtml($this->_badgeStaticTextMapping[$badgeInternalCode]) :
            // In case there is no static text configured we give empty string
            // @todo: I will keep this until we define workflow for missing static text
            '[No Text]';
    }

}