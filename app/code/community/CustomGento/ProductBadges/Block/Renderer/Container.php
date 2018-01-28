<?php
class CustomGento_ProductBadges_Block_Renderer_Container
    extends Mage_Core_Block_Abstract
{

    /** @var array */
    private $_containers = array();

    /** @var CustomGento_ProductBadges_Model_Config_RenderContainer $_containerConfigModel */
    private $_containerConfigModel;

    protected function _construct()
    {
        $this->_containerConfigModel = Mage::getModel('customgento_productbadges/config_renderContainer');
    }

    /**
     * @param string $badgeInternalCode
     * @param CustomGento_ProductBadges_Block_Renderer_Type_Interface $badge
     */
    public function attachBadgeToContainer($badgeInternalCode, CustomGento_ProductBadges_Block_Renderer_Type_Interface $badge)
    {
        $containerName = $this->_containerConfigModel->getContainerOfProductBadge($badgeInternalCode);
        $this->_containers[$containerName][$badgeInternalCode] = $badge;
    }

    /**
     * @param $productId
     * @return string
     */
    public function generateContainersHtml($productId)
    {
        $containersHtml = '';

        foreach ($this->_containers as $containerName => $badges) {
            $containerCssClass = $this->_containerConfigModel
                ->getRenderContainersConfigByContainerName($containerName)
                ->getCssClass();

            $containerHtml = '<div class="product-badge-container ' . $containerCssClass . '">###BADGES_HTML_PLACEHOLDER###</div>';
            $badgesHtml = '';

            /** @var $badge CustomGento_ProductBadges_Block_Renderer_Type_Interface */
            foreach ($badges as $badgeInternalId => $badge) {
                $badgesHtml .= $badge->getBadgeHtml($badgeInternalId);
            }

            $containerHtml = str_replace('###BADGES_HTML_PLACEHOLDER###', $badgesHtml, $containerHtml);
            $containersHtml .= $containerHtml;
        }

        return $containersHtml;
    }

    /**
     * Usually used after rendering badges HTML
     * because we want to start with clear state for next product badges
     */
    public function clearState()
    {
        /** NOTE: May be we just unset all value instead of = array(); */
        $this->_containers = array();
    }

}