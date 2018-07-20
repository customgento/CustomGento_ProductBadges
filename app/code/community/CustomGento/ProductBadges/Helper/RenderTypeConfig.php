<?php

class CustomGento_ProductBadges_Helper_RenderTypeConfig
    extends Mage_Core_Helper_Abstract
{
    const CUSTOMGENTO_PRODUCTBADGES_RENDER_TYPE_CONFIG_XML_PATH = 'global/customgento_productbadges/renderer_types';

    /** @var array */
    protected $_renderTypesConfigurations = array();

    /** @var array|bool */
    protected $_renderTypesMapping = false;

    public function __construct()
    {
        $config = Mage::getConfig()
            ->getNode(self::CUSTOMGENTO_PRODUCTBADGES_RENDER_TYPE_CONFIG_XML_PATH);

        if (!empty($config)) {
            $this->_renderTypesConfigurations = (array)$config->asCanonicalArray();
        }
    }

    /**
     * @return bool
     */
    public function hasRenderTypes()
    {
        return !empty($this->_renderTypesConfigurations);
    }

    /**
     * Gets array of render types configurations
     *
     * @return array
     */
    public function getRenderTypes()
    {
        return $this->_renderTypesConfigurations;
    }

    /**
     * @return array
     */
    public function getRenderTypesForAdminForm()
    {
        $formLabel = array('' => '---');

        foreach ($this->_renderTypesConfigurations as $key => $data) {
            $formLabel[$key] = $data['admin_label'];
        }

        return $formLabel;
    }

    /**
     * @param string $badgeCode
     *
     * @return string
     */
    public function getBadgeRenderType($badgeCode)
    {
        $badgeConfigCollection = Mage::getModel('customgento_productbadges/badgeConfig')
            ->getCollection()
            ->addFieldToSelect('render_type');

        if (false === $this->_renderTypesMapping) {
            /** @var CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig */
            foreach ($badgeConfigCollection as $badgeConfig) {
                $this->_renderTypesMapping[$badgeConfig->getInternalCode()] = $badgeConfig->getRenderType();
            }
        }

        return isset($this->_renderTypesMapping[$badgeCode])
            // We fetch the configured badge render type
            ? $this->_renderTypesMapping[$badgeCode]
            // In case there is not render type configured we give the first know render type
            : reset($this->_renderTypesConfigurations);
    }
}
