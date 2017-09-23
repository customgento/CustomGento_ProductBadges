<?php
class CustomGento_ProductBadges_Helper_RenderTypeConfig
    extends Mage_Core_Helper_Abstract
{

    const CUSTOMGENTO_PRODUCTBADGES_RENDER_TYPE_CONFIG_XML_PATH = 'global/customgento_productbadges/renderer_types';

    /** @var array|bool */
    protected $_renderTypesConfigurations = false;

    public function __construct()
    {
        $config = Mage::getConfig()
            ->getNode(self::CUSTOMGENTO_PRODUCTBADGES_RENDER_TYPE_CONFIG_XML_PATH);

        if(!empty($config)) {
            $this->_renderTypesConfigurations = $config->asCanonicalArray();
        }
    }

    /**
     * @return bool
     */
    public function hasRenderTypes()
    {
        return (false !== $this->_renderTypesConfigurations) ? true : false;
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
        return 'circle';
    }

}