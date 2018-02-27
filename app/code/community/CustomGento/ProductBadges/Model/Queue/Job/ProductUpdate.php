<?php
class CustomGento_ProductBadges_Model_Queue_Job_ProductUpdate
    extends CustomGento_ProductBadges_Model_Queue_Job_Abstract
{

    /**
     * @param Varien_Object $product
     * @return array
     */
    public function getPreparedDataForJobAction(Varien_Object $product)
    {
        return array('product_id' => $product->getId());
    }

    /**
     * @param array $data
     */
    public function processJobAction(array $data)
    {
        return;
    }

}