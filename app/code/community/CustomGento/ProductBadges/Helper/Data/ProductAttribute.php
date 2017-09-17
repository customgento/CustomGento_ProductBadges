<?php
class CustomGento_ProductBadges_Helper_Data_ProductAttribute
    extends Mage_Core_Helper_Abstract
{

    /**
     * @param int $productId
     * @param string $attributeCode
     * @param null $storeId
     * @return mixed
     *
     * Note: Copied from http://ceckoslab.com/magento/load-single-product-attribute-vs-load-entire-product/
     */
    public function fetchProductAttributeBy_ProductId_AttributeCode_StoreId(
        $productId,
        $attributeCode,
        $storeId = null
    )
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($attributeCode);

        if (false === $attribute) {
            return '';
        }

        $attributeValue = Mage::getModel('catalog/product')
            ->getResource()
            ->getAttributeRawValue($productId, $attributeCode, $storeId);

        if ($attribute->usesSource()) {
            $attributeValue = $attribute->getSource()->getOptionText($attributeValue);
        }

        return $attributeValue;
    }

}