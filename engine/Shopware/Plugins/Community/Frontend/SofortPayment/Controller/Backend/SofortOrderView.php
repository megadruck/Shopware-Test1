<?php
/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU LESSER GENERAL PUBLIC LICENSE, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU LESSER GENERAL PUBLIC LICENSE with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    Shopware_Plugins
 * @subpackage Sofort
 * @author     PayIntelligent
 */

/**
 * Controller for the Backend view
 */
class Shopware_Controllers_Backend_SofortOrderView extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * timeout for an order to be cancelled
     *
     * @var int
     */
    private $timeStamp = 4500;

    /**
     * index action is called if no other action is triggered
     */
    public function indexAction()
    {
        $this->View()->loadTemplate("backend/sofort_order_view/app.js");
        $this->View()->assign("title", "Sofort AG Order");
    }

    /**
     * This Action loads the loggingdata from the datebase into the backendview
     */
    public function loadStoreAction()
    {
        $limit = $this->Request()->getParam('limit', 20);
        $offset = $this->Request()->getParam('start', 0);
        $sort = $this->Request()->getParam('sort', null);

		$data = $this->getAllOrders($limit, $offset, $sort);
        $this->View()->assign(
            array(
                "data" => $data,
                "total" => count($data),
                "success" => $data != false
            )
        );
    }

	/**
	 * Returns all orders or a specific set of orders
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param string $sort
	 * @return Zend_Db_Select
	 */
	private function getAllOrders($limit = null, $offset = null, $sort = null){
		$select = Shopware()->Db()->select()
            ->from('s_order', array(
                'id',
                'number' => 'ordernumber',
                'invoiceAmountNet' => 'invoice_amount_net',
                'invoiceShippingNet' => 'invoice_shipping_net',
                'cleared',
                'transactionId' => 'transactionID',
                'currency',
                'currencyFactor',
                'invoiceAmount' => 'invoice_amount',
                'invoiceShipping' => 'invoice_shipping',
                'orderTime' => 'ordertime'
            ))
            ->join('s_core_paymentmeans', '`s_order`.`paymentID` = `s_core_paymentmeans`.`id`', array(
                'paymentId' => 'name'
            ))
            ->joinLeft('s_premium_dispatch', '`s_order`.`dispatchID` = `s_premium_dispatch`.`id`', array(
                'dispatchId' => 'name'
            ))
            ->join('s_core_shops', '`s_order`.`subshopID` = `s_core_shops`.`id`', array(
                'shopId' => 'name'
            ))
            ->join('s_user_billingaddress', '`s_order`.`userID` = `s_user_billingaddress`.`userID`', array(
                'customerId' => 'CONCAT(`firstname`," ",`lastname`)',
            ))
            ->join(array('orderState' =>'s_core_states'), '`s_order`.`status` = `orderState`.`id`', array(
                'status' => 'description',
            ))
            ->join(array('paymentState' =>'s_core_states'), '`s_order`.`cleared` = `paymentState`.`id`', array(
                'cleared' => 'description',
            ))
            ->where('`s_order`.`status` = -1')
            ->where('`s_order`.`ordertime` < DATE_SUB(NOW(),INTERVAL ? MINUTE)', $this->timeStamp / 60)
            ->where('`s_core_paymentmeans`.`name` IN("sofortbanking", "sofortideal")');
		if(isset($limit) && isset($offset)){
			$select->limit($limit, $offset);
		}
		if(isset($sort)){
			$select->order($sort);
		}
		return Shopware()->Db()->fetchAll($select);
	}



	/**
     * Action for restoring the stock for every article for the given order
     */
    public function restoreArticleStockAction()
    {
        $orderId = $this->Request()->getParam('orderId', null);
        $ordernumber = $this->Request()->getParam('orderNumber', null);
        $success = false;
        if (!is_null($ordernumber) && !is_null($orderId) && $ordernumber !== 0) {
            $success = $this->restoreArticleStock($orderId);
        }
        $this->View()->assign(
            array(
                "success" => $success
            )
        );
    }

    /**
     * Action for restoring the stock for every article and remove every order
     */
    public function clearAllOrdersAction()
    {
        $success = false;
		$data = $this->getAllOrders();
		$deleteableOrders = array();
		$userResponse = $this->Request()->getParam('userResponse', 'no');
		if($userResponse === 'yes' && !empty($data)){
			foreach($data as $order){
				if($this->restoreArticleStock($order['id'])){
					$deleteableOrders[] = $order['id'];
				}
			}
			$success = count($deleteableOrders) === Shopware()->Db()->delete('s_order', '`id` IN('.Shopware()->Db()->quote($deleteableOrders).')');
		}

        $this->View()->assign(
            array(
                "success" => $success
            )
        );
    }

    /**
     * Restores the Stock for every item for the given order
     *
     * @param int $orderId
     * @return boolean
     */
    private function restoreArticleStock($orderId)
    {
        $result = false;
        foreach($this->getOrderBasket($orderId) as $article){
            if(!is_array($article) || !isset($article['quantity']) || !isset($article['quantity'])){
                continue;
            }
            try {
                Shopware()->Db()->query("UPDATE `s_articles_details` SET `instock` = `instock`+? WHERE `ordernumber`=?", array(
                    $article['quantity'],
                    $article['articleordernumber']
                ));
                $result = true;
            } catch (Exception $exception) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Returns the BasketItems for the given order
     *
     * @param int $orderId
     * @return array
     */
    private function getOrderBasket($orderId){
        $select = Shopware()->Db()->select()
            ->from('s_order_details', array(
                'articleordernumber', 'quantity'
            ))
            ->where('`modus` = 0')
            ->where('`articleID` != 0')
            ->where('`orderID` = ?', $orderId);
        return Shopware()->Db()->fetchAll($select);
    }

}
