<?php
class CustomGento_ProductBadges_Model_Cache
{

    const CUSTOMGENTO_PRODUCT_BADGES_CACHE_TAG = 'CUSTOMGENTO_PRODUCT_BADGES';

    /**
     * @param string $badgeCode
     *
     * @return CustomGento_ProductBadges_Model_Cache
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
     * @param int $storeId
     *
     * @return array
     */
    public function getProductBadgesCacheKey($productId, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        return Mage_Core_Block_Abstract::CACHE_KEY_PREFIX . implode('_', array(
            'CUSTOMGENTO_PRODUCT_BADGES_PRODUCT',
            $storeId,
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
     * @return CustomGento_ProductBadges_Model_Cache
     */
    public function clearAllBadgeCache()
    {
        Mage::app()->cleanCache(array(self::CUSTOMGENTO_PRODUCT_BADGES_CACHE_TAG));
        return $this;
    }

    /**
     * @param int $productId
     * @return CustomGento_ProductBadges_Model_Cache
     */
    public function clearProductBadgesCache($productId)
    {
        /** @var Mage_Core_Model_Store $store */
        foreach (Mage::app()->getStores() as $store) {
            if ($store->getIsActive()) {
                Mage::app()->getCache()
                            ->remove($this->getProductBadgesCacheKey($productId, $store->getId()));
            }
        }

        return $this;
    }

}