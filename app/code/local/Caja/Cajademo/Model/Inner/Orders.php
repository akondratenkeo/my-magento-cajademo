<?php

class Caja_Cajademo_Model_Inner_Orders extends Mage_Sales_Model_Order_Api
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method items overload.
     */
    public function items($filters = null)
    {
        $orders = array();

        /** @var $orderCollection Mage_Sales_Model_Mysql4_Order_Collection */
        $orderCollection = Mage::getModel("sales/order")->getCollection();
        $orderCollection->addAttributeToSelect('*')
            ->addAddressFields();

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['order']);
        try {
            foreach ($filters as $field => $value) {
                $orderCollection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }

        $i = 0;
        foreach ($orderCollection as $order) {
            $oResult = $this->_getAttributes($order, 'order');
            $orders[$i] = array(
                "status" => "NEW",
                "externalId" => $oResult['order_id']
            );

            $products = array();
            foreach ($order->getAllVisibleItems() as $item) {
                if ($item->getGiftMessageId() > 0) {
                    $item->setGiftMessage(
                        Mage::getSingleton('giftmessage/message')->load($item->getGiftMessageId())->getMessage()
                    );
                }
                $pResult = $this->_getAttributes($item, 'order_item');
                $products[] = array(
                    "externalId" => $pResult['item_id'],
                    "skuCode" => $pResult['sku'],
                    "qty" => (int)$pResult['qty_ordered'],
                    "status" => "NEW"
                );
                unset($pResult, $item);
            }
            $orders[$i]['skus'] = array_values($products);
            $orders[$i]['zoneId'] = Mage::getStoreConfig('general/cajademo/zone_id');
            $orders[$i]['duedate'] = str_replace('.', '', (string)(microtime(true) + 86400));

            $i++;
        }
        return $orders;
    }

    public function updateOrders($items)
    {
        $orderModel = Mage::getModel('sales/order');

        foreach ($items as $item) {

            $order = $orderModel->load($item['externalId']);
            $order->setData('state', "complete");
            $order->setStatus("complete");
            $history = $order->addStatusHistoryComment('Order was set to Complete by our automation tool.', false);
            $history->setIsCustomerNotified(false);
            $order->save();

            unset($order, $history);
        }

        return true;
    }
}