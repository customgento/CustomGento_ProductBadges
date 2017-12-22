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
<style>
.product-badges-chooser-line {
    clear: both;
}

.product-badges-chooser-position-box {
    float: left;
    width: 40px;
    height: 40px;
    border: 1px #CC0000 solid;
}

.product-badges-chooser-position-box-allowed {
    cursor: pointer;
    background: #fff;
}

.product-badges-chooser-position-box-active {
    background: #eb5e00 !important;
}

.product-badges-chooser-position-box-disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>
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
<script type="text/javascript">
(function (d) {
    var badgePositions = d.getElementsByClassName("product-badges-chooser-position-box-allowed");

    var renderContainerPositionInput = d.getElementById("render_container_position_input");

    for (var i = 0; i < badgePositions.length; i++) {
        var positionValue = badgePositions[i].getAttribute("data-position");

        // Mark the active position box
        if (positionValue === renderContainerPositionInput.value) {
            badgePositions[i].classList.add("product-badges-chooser-position-box-active");
        }

        badgePositions[i].addEventListener(
            "click",
            function() {
                //Remove active class from all boxes
                var badgeActivePositions = d.getElementsByClassName("product-badges-chooser-position-box-allowed");

                for (var i = 0; i < badgeActivePositions.length; i++) {
                    badgeActivePositions[i].classList.remove("product-badges-chooser-position-box-active");
                }

                // Add active class
                this.classList.add("product-badges-chooser-position-box-active");

                // Set the value to hidden input
                renderContainerPositionInput.value = this.getAttribute("data-position");
            },
            false
        )
    }
}(document));
</script>
';

        return $hiddenInput . $scriptAndRender;
    }

}