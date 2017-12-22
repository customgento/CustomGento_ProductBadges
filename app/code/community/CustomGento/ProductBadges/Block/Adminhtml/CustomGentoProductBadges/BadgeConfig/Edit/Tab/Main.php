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

        $generalFieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('customgento_productbadges')->__('General Information'))
        );

        if ($model->getId()) {
            $generalFieldset->addField('badge_config_id', 'hidden', array(
                'name' => 'badge_config_id',
            ));
        }

        $generalFieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('customgento_productbadges')->__('Badge Name'),
            'title' => Mage::helper('customgento_productbadges')->__('Badge Name'),
            'required' => true,
        ));

        $generalFieldset->addField('internal_code', 'text', array(
            'name' => 'internal_code',
            'label' => Mage::helper('customgento_productbadges')->__('Internal Code'),
            'title' => Mage::helper('customgento_productbadges')->__('Internal Code'),
            'required' => true,
        ));

        $generalFieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('customgento_productbadges')->__('Description'),
            'title' => Mage::helper('customgento_productbadges')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $generalFieldset->addField('is_active', 'select', array(
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
        $generalFieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('customgento_productbadges')->__('From Date'),
            'title'  => Mage::helper('customgento_productbadges')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        $generalFieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('customgento_productbadges')->__('To Date'),
            'title'  => Mage::helper('customgento_productbadges')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));

        $visualisationFieldset = $form->addFieldset('visualisation_fieldset',
            array('legend' => Mage::helper('customgento_productbadges')->__('Visualisation Settings'))
        );

        // We are having dependency between render_type and (badge_image and badge_text)
        $renderType = $visualisationFieldset->addField('render_type', 'select', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Render Type'),
            'title'     => Mage::helper('customgento_productbadges')->__('Render Type'),
            'name'      => 'render_type',
            'required'  => true,
            //@todo: probably move the options logic to a model
            'options'   => Mage::helper('customgento_productbadges/renderTypeConfig')->getRenderTypesForAdminForm()
        ));

        $badgeImage = $visualisationFieldset->addField('badge_image', 'image', array(
            'label' => Mage::helper('customgento_productbadges')->__('Badge Image'),
            'name' => 'badge_image',
            'note' => '(*.jpg, *.png, *.gif)',
            'required'  => true
        ));

        $badgeText = $visualisationFieldset->addField('badge_text', 'text', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Badge Text'),
            'title'     => Mage::helper('customgento_productbadges')->__('Badge Text'),
            'name'      => 'badge_text',
            'required'  => true
        ));

        // Prepare position chooser
        $positionChooser = Mage::app()
            ->getLayout()
            ->createBlock('customgento_productbadges/adminhtml_customGentoProductBadges_badgeConfig_edit_form_positionChooser');

        $renderContainer = $visualisationFieldset->addField('render_container', 'note', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Render Container'),
            'title'     => Mage::helper('customgento_productbadges')->__('Render Container'),
            'name'      => 'render_container',
            'required'  => true
        ));

        $positionChooser->prepareElementHtml($renderContainer, $model->getData('render_container'));

        $form->setValues($model->getData());

        $this->setForm($form);

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($renderType->getHtmlId(), $renderType->getName())
                ->addFieldMap($badgeImage->getHtmlId(), $badgeImage->getName())
                ->addFieldMap($badgeText->getHtmlId(), $badgeText->getName())
                ->addFieldDependence(
                    $badgeText->getName(),
                    $renderType->getName(),
                    array('circle', 'rectangle')
                )
                ->addFieldDependence(
                    $badgeImage->getName(),
                    $renderType->getName(),
                    'image'
                )
        );

        Mage::dispatchEvent('customgento_productbadges_badge_config_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }

}
