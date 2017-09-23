<?php
class CustomGento_ProductBadges_Model_Indexer_ProductBadgesScanner
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
        $this->_chunks = $this->_getProductIdChunks($this->_chunkSize);
        $this->_chunksCount = count($this->_chunks);

        $this->_badgeConfigsCollection = Mage::getModel('customgento_productbadges/badgeConfig')
            ->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->filterByStoreId($storeId);
    }

    public function fetchBadges()
    {
        $productMappingBadges = array();

        $productIdRanges = $this->_getProductIdRanges($this->_currentChunkNumber);

        /** @var CustomGento_ProductBadges_Model_BadgeConfig $badgeConfig */
        foreach ($this->_badgeConfigsCollection as $badgeConfig) {
            $badgeCode = trim($badgeConfig->getInternalCode());

            $productMappingBadges['found_badges'][$badgeCode] = $badgeConfig
                ->getMatchingProductIds($productIdRanges['from'], $productIdRanges['to']);
        }

        $this->_currentChunkNumber++;

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
        $allProductIds = $productCollection->getAllIds();

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