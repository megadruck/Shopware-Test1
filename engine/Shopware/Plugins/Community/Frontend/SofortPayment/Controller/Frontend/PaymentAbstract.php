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
     * Abstract Payment Controller class dealing with all shared processes of the plugins payment means
     */
    abstract class Shopware_Plugins_Frontend_SofortPayment_Controller_Frontend_PaymentAbstract
        extends Shopware_Controllers_Frontend_Payment
    {
        protected $_loggingSource = "abstract";
        protected $orderProcessHelper;

        public function __construct(\Enlight_Controller_Request_Request $request, \Enlight_Controller_Response_Response $response)
        {
            $this->orderProcessHelper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_OrderProcessHelper($this);
            parent::__construct($request, $response);
        }

                /**
         * First Action to be accessed during checkout
         */
        public function indexAction()
        {
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
            Shopware()->Session()->sofortUniqueId = null;
            Shopware()->Session()->sofortSendMail = false;

			if ( $this->getPaymentShortName() !== 'sofortideal' && $this->getPaymentShortName() !== 'sofortbanking' ) {
                $this->redirect( array( "action" => "error", "forceSecure" => 1 ) );
                return;
            }

            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            //Backup Basket
            $user      = $this->getUser();
            $sessionId = $user[ "additional" ][ "user" ][ "sessionID" ];
            $helper->database()->saveBasket( $sessionId );
            //Create Order
            $paymentUniqueId                     = $this->createPaymentUniqueId();
            Shopware()->Session()->sofortUniqueId = $paymentUniqueId;

			$paymentState = $helper->option()->getStateTranslation($this->getPaymentShortName(), "temporary");

			$this->orderProcessHelper->createOrderBeforeRedirect($paymentState, $paymentUniqueId);
			$logger = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
			$logger->logManually( $this->_loggingSource,
			"Saved order with ordernumber " . $this->getOrderNumber() . " and temporary transaction id " . $paymentUniqueId );

            //Restore Basket
            $helper->database()->restoreBasket( $sessionId );
            $this->redirect( array( "action" => "gateway", "forceSecure" => 1 ) );
        }

        /**
         * Handles success in the transactions
         */
        public function successAction()
        {
            //Clear Basket
            $user      = $this->getUser();
            $sessionId = $user[ "additional" ][ "user" ][ "sessionID" ];
            $helper    = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $helper->database()->clearBasket( $sessionId );
            $this->redirect(
                array(
                     "controller"  => "checkout",
                     "action"      => "finish",
                     "forceSecure" => 1,
                     "sUniqueID"   => Shopware()->Session()->sofortUniqueId
                )
            );
        }

        /**
         * Handles errors in the transactions
         */
        public function errorAction()
        {
            $request    = $this->Request()->getParams();
            $translator = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Translator();
            $logger     = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
            $helper     = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            if ( $request[ 'error_codes' ] == "6001" ) {
                Shopware()->Session()->pigmbhErrorMessage = $translator->getSnippetByNumber( "1202",
                    "Die Zeit zur Durchführung der Zahlung ist aus Sicherheitsgründen abgelaufen. " .
                    "Es wurde keine Transaktion durchgeführt. Bitte führen Sie die Zahlung erneut aus." );
            } else {
                Shopware()->Session()->pigmbhErrorMessage = $translator->getSnippetByNumber( "1201",
                    "Die gewählte Zahlart ist leider nicht möglich, oder wurde auf Kundenwunsch abgebrochen." .
                    " Bitte wählen Sie eine andere Zahlart." );
            }
            if ( isset( $request[ 'sofortError' ] ) ) {
                $logger->logManually( $this->_loggingSource, "An error occurred: " . var_export( $request, true ) );
            }
            //Use temporary transaction id if there is no transactionId
            if ( empty( $request[ 'transactionId' ] ) ) {
                $transactionId = Shopware()->Session()->sofortUniqueId;
            } else {
                $transactionId = $request[ 'transactionId' ];
            }
            //Condemn order if appropriate
			$logger->logManually( $this->_loggingSource, "Changing Orderstate to mark it as failed" );
			if($this->getOrderNumber()){
				$order = Shopware()->Modules()->Order();
				$helper->database()->changeTransactionId($this->getOrderNumber(), $transactionId);
				$orderId = $helper->database()->getOrderByTransactionId( $transactionId );
				$order->setPaymentStatus(
					$orderId,
					$this->convertLibState( "payment_canceled", "unknown" ),
					false
				);
			}
			
			$this->restoreArtikleStock($orderId);
			$this->redirect(
                array(
                     "controller"   => "account",
                     "action"       => "payment",
                     "sTarget"      => "checkout",
                     "errorMessage" => 1,
                     "forceSecure"  => 1
                )
            );
        }

        /**
         * Uses the argumented lib state and reason codes to determine the desired
         * shopware state in accordance with the config
         *
         * @param String $state
         * @param String $reason
         *
         * @return int Status
         */
        public abstract function convertLibState( $state, $reason );

        /**
         * Deals with timeouts during transactions
         *
         * @todo set correct translationname
         */
        public function timeoutAction()
        {
            $request                                  = $this->Request()->getParams();
            $helper                                   = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $translator                               = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Translator();
            $logger                                   = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
            Shopware()->Session()->pigmbhErrorMessage = $translator->getSnippetByNumber( "1202",
                "Die Zeit zur Durchführung der Zahlung ist aus Sicherheitsgründen abgelaufen. " .
                "Es wurde keine Transaktion durchgeführt. Bitte führen Sie die Zahlung erneut aus." );
            //Condemn order if appropriate
			$logger->logManually( $this->_loggingSource, "Changing Orderstate to mark it as failed" );
			$order = Shopware()->Modules()->Order();
			$helper->database()->changeTransactionId($this->getOrderNumber(), $request[ 'transactionId' ]);
			$orderId = $helper->database()->getOrderByTransactionId( $request[ 'transactionId' ] );
			$order->setPaymentStatus(
				$orderId,
				$this->convertLibState( "payment_canceled", "unknown" ), false
			);
			$this->restoreArtikleStock($orderId);
            $this->redirect(
                array(
                    "controller"   => "account",
                    "action"       => "payment",
                    "sTarget"      => "checkout",
                    "errorMessage" => 1,
                    "forceSecure"  => 1
                )
            );
        }

        /**
         * Returns a boolean if the order should be saved before redirecting the customer
         *
         * @return boolean
         */
        protected function createOrderbevoreRedirect(){
            return (bool)Shopware()->Plugins()->Frontend()->SofortPayment()->Config()->get('option_general_create_transactions');
        }

	/**
	 * Saves and/or update the order and send an email
	 *
	 * @param integer $status
	 * @param string $reason
	 * @param string $transactionId
	 */
	protected function finalizeOrder($status, $reason, $transactionId){
	    $this->orderProcessHelper->createOrderAfterRedirect($status, $reason, $transactionId);
	}
	
	/**
	 * Adjusts the stock for all basketitems for the given orderId
	 */
	private function restoreArtikleStock($orderId){
		if($this->orderProcessHelper->getCreateOrder()){
			$artikleStockHelper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_ArticleStockHelper();
			$artikleStockHelper->restoreArticleStock($orderId);
		}
	}
			
	
	
}