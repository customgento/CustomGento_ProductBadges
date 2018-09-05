<?php

class CustomGento_ProductBadges_Helper_Config
    extends Mage_Core_Helper_Abstract
{
    const CUSTOMGENTO_PRODUCTBADGES_RULES_GLOBAL_CONFIG_XML_PATH = 'global/customgento_productbadges/rules';

    /** @var array|bool */
    protected $_rulesConfigurations = false;

    public function __construct()
    {
        $config = Mage::getConfig()
            ->getNode(self::CUSTOMGENTO_PRODUCTBADGES_RULES_GLOBAL_CONFIG_XML_PATH);

        if (!empty($config)) {
            $configsArray = $config->asCanonicalArray();

            foreach ($configsArray as $data) {
                /** @var CustomGento_ProductBadges_Model_Rule_Config $ruleConfigModel */
                $ruleConfigModel = Mage::getModel('customgento_productbadges/rule_config');

                $ruleConfigModel->init(
                    $data['condition_class'],
                    $data['transformer_class'],
                    $data['internal_code'],
                    $data['label']
                );

                $this->_rulesConfigurations[$ruleConfigModel->getInternalCode()] = $ruleConfigModel;
            }
        }
    }

    /**
     * @return bool
     */
    public function hasRulesConfigurations()
    {
        return (false !== $this->_rulesConfigurations);
    }

    /**
     * Gets array of rule configuration objects
     *
     * @return array
     */
    public function getRulesConfigurations()
    {
        return $this->_rulesConfigurations;
    }
}