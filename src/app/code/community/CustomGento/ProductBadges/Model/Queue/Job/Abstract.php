<?php

abstract class CustomGento_ProductBadges_Model_Queue_Job_Abstract
{
    abstract public function processJobAction(array $data);

    abstract public function getPreparedDataForJobAction(Varien_Object $object);

    /**
     * @return string
     */
    abstract public function whoAmI();
}
