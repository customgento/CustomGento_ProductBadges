<?php
class CustomGento_ProductBadges_Helper_Cache
    extends Mage_Core_Helper_Abstract
{

    const CUSTOMGENTO_PRODUCT_BADGES_CACHE_TAG = 'CUSTOMGENTO_PRODUCT_BADGES';

    /**
     * @param string $badgeCode
     *
     * @return CustomGento_ProductBadges_Helper_Cache
     */
    public function clearCacheForBadge($badgeCode)
    {
        Mage::app()->cleanCache(array(self::CUSTOMGENTO_PRODUCT_BADGES_CACHE_TAG . '_' . $badgeCode));

        return $this;
    }

    /**
     * @param array $badgeCodes
     * @return array
     */
    public function getProductBadgesTags($badgeCodes)
    {
        $tags = array(self::CUSTOMGENTO_PRODUCT_BADGES_CACHE_TAG);

        foreach ($badgeCodes as $code) {
            $tags[] = self::CUSTOMGENTO_PRODUCT_BADGES_CACHE_TAG . '_' . $code;
        }

        return $tags;
    }

    /**
     * @param int $productId
     *
     * @return array
     */
    public function getProductBadgesCacheKey($productId)
    {
        return Mage_Core_Block_Abstract::CACHE_KEY_PREFIX . implode('_', array(
            'CUSTOMGENTO_PRODUCT_BADGES_PRODUCT',
            Mage::app()->getStore()->getId(),
            $productId
        ));
    }

    /**
     * @param $productId
     *
     * @return string
     */
    public function getProductBadgesCache($productId)
    {
        return Mage::app()->loadCache($this->getProductBadgesCacheKey($productId));
    }

    /**
     * @return CustomGento_ProductBadges_Helper_Cache
     */
    public function clearAllBadgeCache()
    {
        Mage::app()->cleanCache(array(self::CUSTOMGENTO_PRODUCT_BADGES_CACHE_TAG));
        return $this;
    }

}