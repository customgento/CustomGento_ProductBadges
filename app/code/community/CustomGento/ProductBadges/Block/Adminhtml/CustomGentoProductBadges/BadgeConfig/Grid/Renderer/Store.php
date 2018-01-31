<?php
class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Grid_Renderer_Store
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * Render a grid cell as options
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            $value = explode(',', $value);

            $res = array();
            foreach ($value as $item) {
                if (isset($options[$item])) {
                    $res[] = $this->escapeHtml($options[$item]);
                }
                elseif ($showMissingOptionValues) {
                    $res[] = $this->escapeHtml($item);
                }
            }

            return implode('<br />', $res);
        }

        return '';
    }
}
