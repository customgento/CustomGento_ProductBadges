<?php

class CustomGento_ProductBadges_Model_Config_RenderContainer_Config
{
    /** @var string */
    protected $_internalCode;

    /** @var string */
    protected $_cssClass;

    /**
     * @param string $internalCode
     * @param string $cssClass
     */
    public function init($internalCode, $cssClass)
    {
        $this->_internalCode = $internalCode;
        $this->_cssClass     = $cssClass;
    }

    /**
     * @return string
     */
    public function getCssClass()
    {
        return $this->_cssClass;
    }

    /**
     * @return string
     */
    public function getInternalCode()
    {
        return $this->_internalCode;
    }
}
