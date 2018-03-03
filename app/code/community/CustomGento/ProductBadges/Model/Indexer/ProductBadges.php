<?php

class CustomGento_ProductBadges_Model_Indexer_ProductBadges
{

    protected $_chunkSize = 500;

    /** @var array */
    private $_chunks = array();

    /** @var int */
    private $_currentChunkNumber = 0;

    /** @var int */
    private $_chunksCount = 0;

    /** @var array */
    private $_badgeCodes = array();

    /** @var CustomGento_ProductBadges_Model_Resource_BadgeConfig_Collection */
    private $_badgeConfigsCollection;

    public function __construct($storeId)
    {
        $this->_chunks      = $this->_getProductIdChunks($this->_chunkSize);
        $this->_chunksCount = count($this->_chunks);

        /** @var CustomGento_ProductBadges_Model_Resource_BadgeConfig_Collection $badgeConfigsCollection */
        $badgeConfigsCollection = Mage::getModel('customgento_productbadges/badgeConfig')->getCollection();

        $this->_badgeConfigsCollection = $badgeConfigsCollection
            ->addFiltersNeededForIndexer($storeId);
        $this->_init('customgento_productbadges/indexer_productBadges');
    }

    public function fetchBadges($productIds = array())
    {
        $productMappingBadges = array();
        if (!empty($productIds)) {
            sort($productIds);
            $productIdRanges['from']   = $productIds[0];
            $productIdRanges['to']     = $productIds[count($productIds) - 1];

            //As we just want to update the given product ids we do not want to split the product collection into chunks
            //anymore. Normally this function is called for every chunk,so we set the current chunk number to the max value
            //to avoid more function calls.
            $this->_currentChunkNumber = $this->_chunksCount;
        } else {
            $productIdRanges = $this->_getProductIdRanges($this->_currentChunkNumber);
            $this->_currentChunkNumber++;
        }

        /** @var CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig */
        foreach ($this->_badgeConfigsCollection as $badgeConfig) {
            $badgeCode = trim($badgeConfig->getInternalCode());

            $productMappingBadges['found_badges'][$badgeCode] = $badgeConfig
                ->getMatchingProductIds($productIdRanges['from'], $productIdRanges['to']);
        }

        $productMappingBadges['product_id_scanned_from'] = $productIdRanges['from'];
        $productMappingBadges['product_id_scanned_to']   = $productIdRanges['to'];

        return $productMappingBadges;
    }

    /**
     * @return array
     */
    public function getProductBadgeCodes()
    {
        if (count($this->_badgeCodes) === 0) {
            /** @var CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig */
            foreach ($this->_badgeConfigsCollection as $badgeConfig) {
                //@todo get real badge code, it should be unique
                $this->_badgeCodes[] = trim($badgeConfig->getInternalCode());
            }
        }

        return $this->_badgeCodes;
    }

    public function possibleToFetchMoreBadges()
    {
        return $this->_chunksCount > $this->_currentChunkNumber;
    }


    private function _getProductIdChunks($chunkSize)
    {
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $allProductIds     = $productCollection->getAllIds();

        return array_chunk($allProductIds, $chunkSize);
    }

    private function _getProductIdRanges($chunkNumber)
    {
        return array(
            'from' => reset($this->_chunks[$chunkNumber]),
            'to'   => end($this->_chunks[$chunkNumber])
        );
    }

}