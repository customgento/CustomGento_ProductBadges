<?php
class CustomGento_ProductBadges_Model_Config_RenderContainer
{

    const CUSTOMGENTO_PRODUCTBADGES_RENDER_CONTAINERS_GLOBAL_CONFIG_XML_PATH = 'global/customgento_productbadges/render_containers';

    /** @var array|bool */
    protected $_renderContainersConfigurations = false;

    /** @var array|bool */
    protected $_badgeRenderContainersMapping = false;

    public function __construct()
    {
        $config = Mage::getConfig()
            ->getNode(self::CUSTOMGENTO_PRODUCTBADGES_RENDER_CONTAINERS_GLOBAL_CONFIG_XML_PATH);

        if(!empty($config)) {
            $configsArray = $config->asCanonicalArray();

            foreach ($configsArray as $data) {
                /** @var CustomGento_ProductBadges_Model_Config_RenderContainer_Config $renderContainerConfigModel */
                $renderContainerConfigModel = Mage::getModel('customgento_productbadges/config_renderContainer_config');

                $renderContainerConfigModel->init(
                    $data['internal_code'],
                    $data['css_class']
                );

                $this->_renderContainersConfigurations[$renderContainerConfigModel->getInternalCode()] = $renderContainerConfigModel;
            }
        }
    }

    /**
     * @return bool
     */
    public function hasRenderContainersConfigurations()
    {
        return (false !== $this->_renderContainersConfigurations);
    }

    /**
     * Gets array of render container configuration objects
     *
     * @return array
     */
    public function getRenderContainersConfigurations()
    {
        return $this->_renderContainersConfigurations;
    }

    /**
     * @param string $containerName
     *
     * @return CustomGento_ProductBadges_Model_Config_RenderContainer_Config
     */
    public function getRenderContainersConfigByContainerName($containerName)
    {
        return $this->_renderContainersConfigurations[$containerName];
    }

    /**
     * @param string $badgeCode
     * @return string
     */
    public function getContainerOfProductBadge($badgeCode)
    {
        $badgeConfigCollection = Mage::getModel('customgento_productbadges/badgeConfig')
            ->getCollection()
            ->addFieldToSelect(array('render_container', 'internal_code'));

        if (false === $this->_badgeRenderContainersMapping) {
            /** @var CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig */
            foreach ($badgeConfigCollection as $badgeConfig) {
                $this->_badgeRenderContainersMapping[$badgeConfig->getInternalCode()] = $badgeConfig->getRenderContainer();
            }
        }

        return isset($this->_badgeRenderContainersMapping[$badgeCode])
            // We fetch the configured badge container
            ? $this->_badgeRenderContainersMapping[$badgeCode]
            // In case there is not container configured we give the first know container
            : reset($this->_renderContainersConfigurations);
    }

}