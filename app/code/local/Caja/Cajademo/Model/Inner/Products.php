<?php

class Caja_Cajademo_Model_Inner_Products extends Mage_Catalog_Model_Product_Api
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method items overload
     */
    public function items($filters = null, $store = null)
    {

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addStoreFilter($this->_getStoreId($store))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('status');

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }

        $result = array();
        $cInventory = Mage::getModel('cataloginventory/stock_item');
        foreach ($collection as $product) {
            // @TODO  optimize that segment, $stock usage load's a server
            $stock = $cInventory->loadByProduct($product->getEntityId());
            $result[] = array(
                'entity_id'             => $product->getEntityId(),
                'skuCode'               => str_replace( array(" "), "", $product->getSku()),
                'description'           => str_replace( array("'", "\"", " ", "-"), "_", $product->getName()),
                "totalQuantity"         => (int)$stock->getQty(),
                "reservedQuantity"      => (int)$stock->getMinQty(),
                'minQuantityAllowed'    => (int)$stock->getMinSaleQty(),
                'isAvailable'           => ($product->getStatus() == 1) ? (bool)true : (bool)false
            );

            unset($product, $stock);
        }
        return $result;
    }

    public function updateItems($items)
    {
        // @TODO  need to reindex Magento indexes
        $cInventory = Mage::getModel('cataloginventory/stock_item');
        $testIds = array(337, 373, 374, 391, 393, 412, 258, 259, 260, 494, 495, 427, 520, 521, 522, 523, 524, 525, 526, 437, 387, 388, 448);
        foreach ($items['skus'] as $item) {

            if (in_array($item['entity_id'], $testIds)) {
                //$time_start = microtime(true);
                $stockItem = $cInventory->loadByProduct($item['entity_id']);

                if ($stockItem->getId() > 0) {
                    $stockItem->setQty($item['totalQuantity']);
                    $stockItem->setMinQty($item['reservedQuantity']);
                    $stockItem->setUseConfigMinQty(0);

                    if ($item['totalQuantity'] > $item['reservedQuantity']) {
                        $stockItem->setIsInStock(1);
                    } else {
                        $stockItem->setIsInStock(0);
                    }

                    $stockItem->save();
                }

                //$time_end = microtime(true);
                //$time = $time_end - $time_start;
            }

            unset($stockItem);
        }
        return true;
    }
}