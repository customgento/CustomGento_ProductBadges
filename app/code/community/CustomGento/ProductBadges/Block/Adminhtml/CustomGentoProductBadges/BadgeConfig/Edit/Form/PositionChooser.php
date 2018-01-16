<?php
class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Edit_Form_PositionChooser
    extends Mage_Adminhtml_Block_Template
{

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @param string $value
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element, $value)
    {
        $element->setData('after_element_html', $this->_getChooserHtml($element, $value));
        return $element;
    }

    protected function _getChooserHtml(Varien_Data_Form_Element_Abstract $element, $value)
    {
        $hiddenInput = '<input type="hidden" name="' . $element->getName() . '" id="render_container_position_input" class="required-entry" value=' . $value . ' />';

        $scriptAndRender = '

<div class="product-badges-chooser-line">
    <div data-position="top-left" class="product-badges-chooser-position-box product-badges-chooser-position-box-allowed"></div>
    <div class="product-badges-chooser-position-box product-badges-chooser-position-box-disabled"></div>
    <div data-position="top-right" class="product-badges-chooser-position-box product-badges-chooser-position-box-allowed"></div>
</div>
<div class="product-badges-chooser-line">
    <div class="product-badges-chooser-position-box product-badges-chooser-position-box-disabled"></div>
    <div class="product-badges-chooser-position-box product-badges-chooser-position-box-disabled"></div>
    <div class="product-badges-chooser-position-box product-badges-chooser-position-box-disabled"></div>
</div>
<div class="product-badges-chooser-line">
    <div data-position="bottom-left" class="product-badges-chooser-position-box product-badges-chooser-position-box-allowed"></div>
    <div class="product-badges-chooser-position-box product-badges-chooser-position-box-disabled"></div>
    <div data-position="bottom-right" class="product-badges-chooser-position-box product-badges-chooser-position-box-allowed"></div>
</div>

';

        return $hiddenInput . $scriptAndRender;
    }

}