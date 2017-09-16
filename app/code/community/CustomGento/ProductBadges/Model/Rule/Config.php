<?php
class CustomGento_ProductBadges_Model_Rule_Config
{

    /** @var string */
    private $_conditionClass;

    /** @var string */
    private $_transformerClass;

    /** @var string */
    private $_internalCode;

    /** @var string */
    private  $_label;

    /**
     * @param string $conditionClass
     * @param string $transformerClass
     * @param string $internalCode
     * @param string $label
     */
    public function init($conditionClass, $transformerClass, $internalCode, $label)
    {
        $this->_conditionClass   = $conditionClass;
        $this->_transformerClass = $transformerClass;
        $this->_internalCode     = $internalCode;
        $this->_label            = $label;
    }

    /**
     * @return string
     */
    public function getConditionClass()
    {
        return $this->_conditionClass;
    }

    /**
     * @return string
     */
    public function getTransformerClass()
    {
        return $this->_transformerClass;
    }

    /**
     * @return string
     */
    public function getInternalCode()
    {
        return $this->_internalCode;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

}