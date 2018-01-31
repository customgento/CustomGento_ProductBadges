<?php

class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Edit_Tab_Main_StoreChooser
    extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('multiple');
        $this->setSize(10);
    }

    public function getName()
    {
        $name = parent::getName();
        if (strpos($name, '[]') === false) {
            $name.= '[]';
        }
        return $name;
    }

    public function getElementHtml()
    {
        $this->addClass('select multiselect');

        $checkedStoreChooserEnabler = '';
        $disabledStoreChooser = '';
        $disableAllStoreViews = 'disabled';
        if ($this->shouldAllStoreViewsBeChosen()) {
            $checkedStoreChooserEnabler = 'checked';
            $disabledStoreChooser = 'disabled="disabled"';
            $disableAllStoreViews = '';
        }

        $html = '<label><input class="badges_store_enabler" type="checkbox" id="' . $this->getHtmlId() . '_all_store_views" '.
            'data-controlled-default="' .$this->getHtmlId() . '_default" ' .
            ' ' . $checkedStoreChooserEnabler . ' ' .
            'data-controlled-chooser="' .$this->getHtmlId() . '" /> ' .
            Mage::helper('customgento_productbadges')->__('All Store Views') . '</label>';

        $html .= '<input type="hidden" value="' . Mage_Core_Model_App::ADMIN_STORE_ID . '" ' . $disableAllStoreViews .
            ' id="' .$this->getHtmlId() . '_default"' . ' name="' . $this->getName() . '" />';

        $html .= '<br />';

        $html .= '<select id="' . $this->getHtmlId() . '" ' . $disabledStoreChooser . ' name="' . $this->getName() . '" ' .
            $this->serialize($this->getHtmlAttributes()) . ' multiple="multiple">' . "\n";

        $value = $this->getValue();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                if (is_array($option['value'])) {
                    $html .= '<optgroup label="' . $option['label'] . '">' . "\n";
                    foreach ($option['value'] as $groupItem) {
                        $html .= $this->_optionToHtml($groupItem, $value);
                    }
                    $html .= '</optgroup>' . "\n";
                } else {
                    $html .= $this->_optionToHtml($option, $value);
                }
            }
        }

        $html .= '</select>' . "\n";
        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * @return bool
     */
    protected function shouldAllStoreViewsBeChosen()
    {
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (empty($values)
            || (1 === count($values) && $values[0] == Mage_Core_Model_App::ADMIN_STORE_ID) ) {
            return true;
        }

        return false;
    }

    protected function _optionToHtml($option, $selected)
    {
        $html = '<option value="'.$this->_escape($option['value']).'"';
        $html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
        $html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
        if (in_array((string)$option['value'], $selected)) {
            $html.= ' selected="selected"';
        }
        $html.= '>'.$this->_escape($option['label']). '</option>'."\n";
        return $html;
    }
}
