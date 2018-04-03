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

        if (!Mage::app()->isSingleStoreMode()) {
            $generalFieldset->addType(
                'badge_store_chooser',
                'CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Edit_Tab_Main_StoreChooser'
            );

            $generalFieldset->addField('store_ids', 'badge_store_chooser', array(
                'label'     => Mage::helper('customgento_productbadges')->__('Visible In'),
                'required'  => true,
                'name'      => 'store_ids[]',
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                'value'     => $model->getData('store_ids'),
                'after_element_html' => Mage::getBlockSingleton('adminhtml/store_switcher')->getHintHtml()
            ));
        } else {
            $generalFieldset->addField('store_ids', 'hidden', array(
                'name'      => 'store_ids[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setData('store_ids', Mage::app()->getStore(true)->getId());
        }

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
            'options'   => Mage::helper('customgento_productbadges/renderTypeConfig')->getRenderTypesForAdminForm(),
            'class'     => 'trigger-badge-preview'
        ));

        $preview = $visualisationFieldset->addField('badge_preview_dummy', 'note', array(
            'label'              => Mage::helper('customgento_productbadges')->__('Preview'),
            'title'              => Mage::helper('customgento_productbadges')->__('Preview'),
            'name'               => 'badge_preview_dummy',
            'after_element_html' => $this->getPreviewContainer()
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
            'required'  => true,
            'class'     => 'trigger-badge-preview'
        ));

        $editImg = '<img src="' . $this->getSkinUrl('images/fam_rainbow.gif') . '" class="product-badge-color-picker-hint">';


        $badgeTextColor = $visualisationFieldset->addField('badge_text_color', 'text', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Text Color'),
            'title'     => Mage::helper('customgento_productbadges')->__('Text Color'),
            'name'      => 'badge_text_color',
            'class'     => 'color {required:false} trigger-badge-preview'
        ));

        $badgeTextColor->setData('after_element_html', $editImg);

        $badgeBackgroundColor = $visualisationFieldset->addField('badge_background_color', 'text', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Background Color'),
            'title'     => Mage::helper('customgento_productbadges')->__('Background Color'),
            'name'      => 'badge_background_color',
            'class'     => 'color {required:false} trigger-badge-preview'
        ));

        $badgeBackgroundColor->setData('after_element_html', $editImg);

        $badgeFontFamily = $visualisationFieldset->addField('badge_font_family', 'text', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Font Family'),
            'title'     => Mage::helper('customgento_productbadges')->__('Font Family'),
            'name'      => 'badge_font_family',
            'class'     => 'trigger-badge-preview'
        ));

        $badgeFontSize = $visualisationFieldset->addField('badge_font_size', 'text', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Font Size'),
            'title'     => Mage::helper('customgento_productbadges')->__('Font Size'),
            'name'      => 'badge_font_size',
            'class'     => 'trigger-badge-preview'
        ));

        $renderContainer = $visualisationFieldset->addField('render_container', 'note', array(
            'label'     => Mage::helper('customgento_productbadges')->__('Render Container'),
            'title'     => Mage::helper('customgento_productbadges')->__('Render Container'),
            'name'      => 'render_container',
            'required'  => true
        ));

        /** @var Mage_Adminhtml_Block_Template $positionChooser */
        $positionChooser = $this->getLayout()
            ->createBlock('adminhtml/template')
            ->setData('name', $renderContainer->getName())
            ->setData('value', $model->getData('render_container'))
            ->setTemplate('customgento/productbadges/badgeconfig/edit/positionchooser.phtml');

        $renderContainer->setData('after_element_html', $positionChooser->toHtml());

        $form->setValues($model->getData());

        $this->setForm($form);

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($renderType->getHtmlId(), $renderType->getName())
                ->addFieldMap($badgeImage->getHtmlId(), $badgeImage->getName())
                ->addFieldMap($badgeText->getHtmlId(), $badgeText->getName())
                ->addFieldMap($badgeTextColor->getHtmlId(), $badgeTextColor->getName())
                ->addFieldMap($badgeBackgroundColor->getHtmlId(), $badgeBackgroundColor->getName())
                ->addFieldMap($badgeFontFamily->getHtmlId(), $badgeFontFamily->getName())
                ->addFieldMap($badgeFontSize->getHtmlId(), $badgeFontSize->getName())
                ->addFieldMap($preview->getHtmlId(), $preview->getName())
                ->addFieldDependence(
                    $badgeText->getName(),
                    $renderType->getName(),
                    array('circle', 'rectangle')
                )
                ->addFieldDependence(
                    $badgeTextColor->getName(),
                    $renderType->getName(),
                    array('circle', 'rectangle')
                )
                ->addFieldDependence(
                    $badgeBackgroundColor->getName(),
                    $renderType->getName(),
                    array('circle', 'rectangle')
                )
                ->addFieldDependence(
                    $badgeFontFamily->getName(),
                    $renderType->getName(),
                    array('circle', 'rectangle')
                )
                ->addFieldDependence(
                    $badgeFontSize->getName(),
                    $renderType->getName(),
                    array('circle', 'rectangle')
                )
                ->addFieldDependence(
                    $preview->getName(),
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

    /**
     * @return string
     */
    protected function getPreviewContainer()
    {
        $dataAttributes = array(
            'data-update-url'   => $this->quoteEscape($this->getUrl('*/*/badgePreview', array('isAjax' => true))),
            'data-loader-image' => $this->getSkinUrl('images/ajax-loader-tr.gif')
        );

        $attributesString = '';

        foreach ($dataAttributes as $attribute => $value) {
            $attributesString .= $attribute . '="' . $value . '" ';
        }

        return '<div id="badge_preview_holder_field" '. $attributesString .'></div>';
    }

}
