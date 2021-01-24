<?php

class CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid
     * Set sort settings
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_badges_badge_config_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Set collection
     *
     * @return CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection CustomGento_ProductBadges_Model_Resource_BadgeConfig_Collection */
        $collection = Mage::getModel('customgento_productbadges/badgeConfig')
            ->getResourceCollection();

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Add grid columns
     *
     * @return CustomGento_ProductBadges_Block_Adminhtml_CustomGentoProductBadges_BadgeConfig_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'badge_config_id',
            array(
                'header' => Mage::helper('customgento_productbadges')->__('ID'),
                'align'  => 'right',
                'width'  => '50px',
                'index'  => 'badge_config_id',
            )
        );

        $this->addColumn(
            'name', array(
                'header' => Mage::helper('customgento_productbadges')->__('Badge Name'),
                'align'  => 'left',
                'index'  => 'name',
            )
        );

        $renderer = 'customgento_productbadges/adminhtml_customGentoProductBadges_badgeConfig_grid_column_preview';
        $this->addColumn(
            'preview', array(
                'header'   => Mage::helper('customgento_productbadges')->__('Preview'),
                'index'    => 'preview',
                'sortable' => false,
                'filter'   => false,
                'renderer' => $renderer
            )
        );

        $this->addColumn(
            'from_date', array(
                'header'  => Mage::helper('customgento_productbadges')->__('Date Start'),
                'align'   => 'left',
                'width'   => '120px',
                'type'    => 'date',
                'default' => '--',
                'index'   => 'from_date',
            )
        );

        $this->addColumn(
            'to_date', array(
                'header'  => Mage::helper('customgento_productbadges')->__('Date End'),
                'align'   => 'left',
                'width'   => '120px',
                'type'    => 'date',
                'default' => '--',
                'index'   => 'to_date',
            )
        );

        $this->addColumn(
            'is_active', array(
                'header'  => Mage::helper('customgento_productbadges')->__('Status'),
                'align'   => 'left',
                'width'   => '80px',
                'index'   => 'is_active',
                'type'    => 'options',
                'options' => array(
                    1 => 'Active',
                    0 => 'Inactive',
                ),
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $renderer    = 'customgento_productbadges/adminhtml_customGentoProductBadges_badgeConfig_grid_renderer_store';
            $storeFilter = Mage::getSingleton('customgento_productbadges/admin_badgeConfig_grid_filter_store');
            $this->addColumn(
                'store_ids', array(
                    'header'                    => Mage::helper('catalog')->__('Store'),
                    'align'                     => 'left',
                    'index'                     => 'store_ids',
                    'type'                      => 'options',
                    'renderer'                  => $renderer,
                    'sortable'                  => false,
                    'options'                   => $storeFilter->getStoreOptionHash(),
                    'width'                     => 250,
                    'filter_condition_callback' => array($this, '_storeFilterCallBack')
                )
            );
        }

        parent::_prepareColumns();

        return $this;
    }

    /**
     * @param CustomGento_ProductBadges_Model_Resource_BadgeConfig_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column                         $column
     *
     * @return $this
     */
    protected function _storeFilterCallBack(
        CustomGento_ProductBadges_Model_Resource_BadgeConfig_Collection $collection,
        Mage_Adminhtml_Block_Widget_Grid_Column $column
    ) {
        $value = $column->getFilter()->getValue();

        if ($value == null) {
            return $this;
        }

        $collection->getSelect()->where("store_ids REGEXP ?", "(^|,){$value}(,|$)");

        return $this;
    }

    /**
     * Retrieve row click URL
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('badge_config_id' => $row->getData('badge_config_id')));
    }
}
