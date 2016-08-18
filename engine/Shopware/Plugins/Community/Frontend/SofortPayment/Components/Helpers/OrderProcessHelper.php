<?php
/**
 * OrderProcessHelper
 */
class Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_OrderProcessHelper
{
    /**
     * If true an actual order will be placed before redirecting
     *
     * Note: if false the placeholder orderId can not be used!
     *       This option doesn't take any effect when "creating order before redirect" is set to false
     * @var boolean
     */
    private $createOrder = true;

    /**
     * @var boolean
     */
    private $createOrderbeforeRedirect;

    /**
     * @var Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger
     */
    private $logger;

    /**
     * @var Shopware_Controllers_Frontend_Payment
     */
    private $context;

    /**
     * Creates an instance for this class
     * @param Shopware_Controllers_Frontend_Payment $context
     */
    public function __construct(Shopware_Controllers_Frontend_Payment $context)
    {
        $this->createOrderbeforeRedirect = (bool)Shopware()->Plugins()->Frontend()->SofortPayment()->Config()->get('option_general_create_transactions');
        $this->logger = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
        $this->context = $context;
        $this->helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
    }

	/**
	 * Returns the createOrder
	 *
	 * @return boolean
	 */
	public function getCreateOrder() {
		return $this->createOrder;
	}

	    /**
     * Sends an email to the customer
     *
     * @return boolean
     */
    private function sendMail($orderVariables, $user)
    {
        if (empty($orderVariables)) {
            $this->logger->logManually(__CLASS__, 'OrderMail is empty. '.var_export($orderVariables, true));
            return false;
        }
        if (empty($user)) {
            $this->logger->logManually(__CLASS__, 'User is empty. '.var_export($user, true));
            return false;
        }
        Shopware()->Session()->sofortSendMail = true;
        $order = Shopware()->Modules()->Order();
        $order->sUserData = $user;
        $order->sNet = empty($user['additional']['charge_vat']);
        $order->sendMail($orderVariables);
    }

    /**
     * changes the orderstate for the actual order
     *
     * @param integer $stateId
     */
    private function setOrderState($transactionId, $stateId)
    {
        $ordernumber = $this->context->getOrderNumber();
        if(is_null($ordernumber)){
            $orderInfo = $this->getOrderInformation($transactionId);
            $ordernumber = $orderInfo['ordernumber'];
        }

        $orderModel = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->findOneByNumber($ordernumber);
        if(isset($orderModel)){
	    $orderModel->setOrderStatus(Shopware()->Models()->getRepository('Shopware\Models\Order\Status')->find($stateId));
	    Shopware()->Models()->flush($orderModel);
	}else{
	    $this->logger->logManually(__CLASS__, 'Could not found Order. Maybe the session has expired.');
	}
    }

    /**
     * Saves the order into the database
     *
     * @param string $transactionId
     * @param integer $state
     * @return boolean
     */
    private function saveOrder($transactionId, $state)
    {
        Shopware()->Session()->sofortUniqueId = $transactionId;
        $orderNumber = $this->context->saveOrder($transactionId, $transactionId, $state, true);
        $result = !empty($orderNumber);
        if ($result) {
            Shopware()->Session()->sofortOrderExsist = (bool)$result;
        }
        // just to be sure everythings written into database
        Shopware()->Models()->flush();
        $this->logger->logManually(__CLASS__, $result ? 'Order #'.$orderNumber.' saved successfully':'Failed to save order');
        return $result;
    }

    /**
     * Public method called before redirecting a customer
     *
     * @param integer $state
     * @param string $transactionId
     * @return boolean
     */
    public function createOrderBeforeRedirect($state, $transactionId)
    {
        Shopware()->Session()->sofortOrderExsist = false;
        $result = true;

	if($this->createOrder || (!$this->createOrder && $this->createOrderbeforeRedirect)) {
            //create an actual Order
            $result = $this->saveOrder($transactionId, $state);
            if ($result && !$this->createOrderbeforeRedirect) {
                //set state to abort
                $this->setOrderState($transactionId, -1);
            }
        }

        return $result;
    }

    /**
     * Public method called after the customer returns to the shop
     *
     * @param string $status
     * @param string $reason
     * @param string $transactionId
     */
    public function createOrderAfterRedirect($status, $reason, $transactionId)
    {
        // workaround for missing session after creating order
        $orderVariables = unserialize(Shopware()->Session()->offsetGet('sofortMailVariables'));
        $user = $this->context->getUser();
        $alreadySend = false;
        
        $result = false;
        if (!Shopware()->Session()->sofortOrderExsist) {
            Shopware()->Session()->sofortSendMail = true;
            //create Order
            $result = $this->saveOrder($transactionId, 0);
            $alreadySend = true;
        }else{
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $helper->database()->changeTransactionId( $this->context->getOrderNumber(), $transactionId );
            $this->setOrderState($transactionId, 0);
        }

        $state = $this->context->convertLibState($status, $reason);
        if ($state !== 0) {
            $row = $this->getOrderInformation($transactionId);
            $order = Shopware()->Modules()->Order();
            $order->setPaymentStatus($row['id'], $state, false);
            $this->logger->logManually(__CLASS__, 'Change PaymentStatus for Order #'.$row['ordernumber'].'('.$row['id'].') to '.$state);
        }
        
        if(!$alreadySend) { 
            //send Mail
            $this->sendMail($orderVariables, $user);
        }
    }

    /**
     * Returns all informations for an specific order
     * @param string $transactionId
     * @return array | false
     */
    private function getOrderInformation($transactionId){
        $this->logger->logManually(__CLASS__, 'Get informations for TransactionId:'. $transactionId);
        $select = Shopware()->Db()->select()
                ->from('s_order',array('id','ordernumber'))
                ->where('`transactionID` = ?', $transactionId);
        return Shopware()->Db()->fetchRow($select);
    }
}
