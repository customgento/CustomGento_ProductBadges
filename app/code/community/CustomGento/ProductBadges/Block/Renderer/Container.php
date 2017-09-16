<?php
class CustomGento_ProductBadges_Block_Renderer_Container
    extends Mage_Core_Block_Abstract
{

    private $_containersConfig = array(
        'top-left'     => array('class' => 'product-badge-container--top-left'),
        'top-right'    => array('class' => 'product-badge-container--top-right'),
        'bottom-left'  => array('class' => 'product-badge-container--bottom-left'),
        'bottom-right' => array('class' => 'product-badge-container--bottom-right')
    );

    /** @var array */
    private $_containers = array();

    /**
     * @param $containerName
     * @param $badgeInternalId
     * @param CustomGento_ProductBadges_Block_Renderer_Type_Interface $badge
     */
    public function attachBadgeToContainer($containerName, $badgeInternalId, CustomGento_ProductBadges_Block_Renderer_Type_Interface $badge)
    {
        $this->_containers[$containerName][$badgeInternalId] = $badge;
    }

    /**
     * @param $productId
     * @return string
     */
    public function generateContainersHtml($productId)
    {
        $containersHtml = '';

        foreach ($this->_containers as $containerName => $badges) {
            $containerHtml = '<div class="product-badge-container ' . $this->_containersConfig[$containerName]['class'] . '">###BADGES_HTML_PLACEHOLDER###</div>';
            $badgesHtml = '';

            /** @var $badge CustomGento_ProductBadges_Block_Renderer_Type_Interface */
            foreach ($badges as $badgeInternalId => $badge) {
                $badgesHtml .= $badge->getBadgeHtml($badgeInternalId, $productId);
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