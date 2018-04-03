<?php
class CustomGento_ProductBadges_Model_Indexer
    extends Mage_Index_Model_Indexer_Abstract
{

    protected $_matchedEntities
        = array(
            Mage_Catalog_Model_Product::ENTITY => array(
                Mage_Index_Model_Event::TYPE_SAVE,
                Mage_Index_Model_Event::TYPE_MASS_ACTION
            ),
        );

    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
    }

    public function getName()
    {
        return 'CustomGento Product Badges';
    }

    public function getDescription()
    {
        return 'Index product badges';
    }

    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getEntity() == Mage_Catalog_Model_Product::ENTITY
            && $event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
            $productId = $event->getDataObject()->getId();
            $this->getResource()->rebuild(null, array($productId));
        } else if ($event->getEntity() == Mage_Catalog_Model_Product::ENTITY
            && $event->getType() == Mage_Index_Model_Event::TYPE_MASS_ACTION) {
            $productIds = $event->getDataObject()->getProductIds();
            $this->getResource()->rebuild(null, $productIds);
        }
    }

}