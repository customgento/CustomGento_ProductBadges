<?php
class CustomGento_ProductBadges_Block_Renderer_Type_Abstract
    extends Mage_Core_Block_Abstract
{

    /**
     * @param CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig
     * @return string
     */
    protected function _customStyling(CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig)
    {
        /** @note: I would like to create separate class for this function because
         *  this function is very specific for badges that are dynamic e.g. have dynamic text and shape
         */

        $customisations = [];

        $textColor = $badgeConfig->getData('badge_text_color');

        if ($textColor) {
            $customisations['color'] = '#' . $textColor;
        }

        $bgColor = $badgeConfig->getData('badge_background_color');

        if ($bgColor) {
            $customisations['background'] = '#' . $bgColor;
        }

        $fontFamily = $badgeConfig->getData('badge_font_family');

        if ($fontFamily) {
            $customisations['font-family'] = $fontFamily;
        }

        $fontSize = $badgeConfig->getData('badge_font_size');

        if ($fontSize) {
            $customisations['font-size'] = $fontSize . 'px';
        }

        if (!empty($customisations)) {
            $styleString = '';
            foreach ($customisations as $cssProperty => $value) {
                $styleString .= $cssProperty . ':' . $value . ' !important;';
            }

            return 'style="' . $this->escapeHtml($styleString) . '"';
        }

        return '';
    }

}