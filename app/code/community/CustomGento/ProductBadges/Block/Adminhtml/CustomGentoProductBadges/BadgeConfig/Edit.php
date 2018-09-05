<?php

class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     */
    protected function _construct()
    {
        $this->_objectId   = 'badge_config_id';
        $this->_blockGroup = 'customgento_productbadges';
        $this->_controller = 'adminhtml_customGentoProductBadges_badgeConfig';

        parent::_construct();

        $this->_addButton(
            'save_and_continue_edit',
            array(
                'class'   => 'save',
                'label'   => Mage::helper('customgento_productbadges')->__('Save and Continue Edit'),
                'onclick' => 'editForm.submit($(\'edit_form\').action + \'back/edit/\')',
            ),
            10
        );
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $badgeConfig = Mage::registry('current_badge_config');
        if ($badgeConfig->getData('badge_config_id')) {
            return Mage::helper('customgento_productbadges')
                ->__("Edit Badge '%s'", $this->escapeHtml($badgeConfig->getName()));
        }

        return Mage::helper('customgento_productbadges')->__('New Badge');
    }
}
