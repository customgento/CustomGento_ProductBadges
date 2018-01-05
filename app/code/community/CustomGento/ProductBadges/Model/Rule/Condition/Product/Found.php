<?php
class CustomGento_ProductBadges_Model_Rule_Condition_Product_Found
    extends CustomGento_ProductBadges_Model_Rule_Condition_Product_Combine
{
    /**
     * Init the product found conditions and set the custom type
     */
    public function _construct()
    {
        parent::_construct();
        $this->setType('customgento_productbadges/rule_condition_product_found');
    }

    /**
     * Set the allowed value options for the select field.
     *
     * @see Mage_Rule_Model_Condition_Combine::loadValueOptions()
     * @return CustomGento_ProductBadges_Model_Rule_Condition_Product_Found
     */
    public function loadValueOptions()
    {
        $this->setValueOption(
            array(
                1 => Mage::helper('customgento_productbadges')->__('FOUND'),
                //0 => Mage::helper('dynamiccategory')->__('NOT FOUND'),
            )
        );

        return $this;
    }

    /**
     * Returns the html code for the condition field
     *
     * @see Mage_Rule_Model_Condition_Combine::asHtml()
     *
     * @return string HTML
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml();
        $html .= Mage::helper('customgento_productbadges')->__(
            'If an product is %s in the catalog with %s of these conditions true:',
            $this->getValueElement()->getHtml(),
            $this->getAggregatorElement()->getHtml()
        );

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }
}
