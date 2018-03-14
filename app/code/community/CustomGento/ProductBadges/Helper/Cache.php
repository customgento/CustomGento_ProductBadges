<?php
class CustomGento_ProductBadges_Helper_Cache
    extends Mage_Core_Helper_Abstract
{


    /**
     * @param string $badgeCode
     */
    public function clearCacheForBadge($badgeCode)
    {
        Mage::app()->cleanCache(array('CUSTOMGENTO_PRODUCT_BADGES_TAG_' . $badgeCode));
    }

    /**
     * @param array $badgeCodes
     * @return array
     */
    public function getProductBadgesTags($badgeCodes)
    {
        $tags = array();

        foreach ($badgeCodes as $code) {
            $tags[] = 'CUSTOMGENTO_PRODUCT_BADGES_TAG_' . $code;
        }

        return $tags;
    }

    /**
     * @param $productId
     *
     * @return array
     */
    public function getProductBadgesCacheKey($productId)
    {
        return array(
            'CUSTOMGENTO_PRODUCT_BADGES_PRODUCT',
            Mage::app()->getStore()->getId(),
            $productId
        );
    }

}