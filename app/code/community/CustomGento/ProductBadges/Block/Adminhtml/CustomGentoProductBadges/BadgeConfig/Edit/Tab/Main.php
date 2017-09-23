<?php
class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('customgento_productbadges')->__('Badge Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('customgento_productbadges')->__('Badge Information');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_badge_config');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('current_badge_');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('customgento_productbadges')->__('General Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('badge_config_id', 'hidden', array(
                'name' => 'badge_config_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('customgento_productbadges')->__('Badge Name'),
            'title' => Mage::helper('customgento_productbadges')->__('Badge Name'),
            'required' => true,
        ));

        $fieldset->addField('internal_code', 'text', array(
            'name' => 'internal_code',
            'label' => Mage::helper('customgento_productbadges')->__('Internal Code'),
            'title' => Mage::helper('customgento_productbadges')->__('Internal Code'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('customgento_productbadges')->__('Description'),
            'title' => Mage::helper('customgento_productbadges')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Status'),
            'title'     => Mage::helper('customgento_productbadges')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('customgento_productbadges')->__('Active'),
                '0' => Mage::helper('customgento_productbadges')->__('Inactive'),
            ),
        ));

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('customgento_productbadges')->__('From Date'),
            'title'  => Mage::helper('customgento_productbadges')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('customgento_productbadges')->__('To Date'),
            'title'  => Mage::helper('customgento_productbadges')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));

        $fieldset->addField('render_type', 'select', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Render Type'),
            'title'     => Mage::helper('customgento_productbadges')->__('Render Type'),
            'name'      => 'render_type',
            'required'  => true,
            //@todo: probably move the options logic to a model
            'options'   => Mage::helper('customgento_productbadges/renderTypeConfig')->getRenderTypesForAdminForm()
        ));

        $fieldset->addField('render_container', 'select', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Render Container'),
            'title'     => Mage::helper('customgento_productbadges')->__('Render Container'),
            'name'      => 'render_container',
            'required'  => true,
            //@todo: probably move the options logic to a model
            'options'   => Mage::getModel('customgento_productbadges/config_renderContainer')->getRenderContainersForAdminForms()
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        Mage::dispatchEvent('customgento_productbadges_badge_config_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }

}
