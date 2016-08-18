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
     * Paymentcontroller for the Sofortbanking Payment. Handles all actions occurring during the checkout process,
     * as well as the asynchronous notifications sent by the API.
     */
    class Shopware_Controllers_Frontend_PaymentSofortbanking
        extends Shopware_Plugins_Frontend_SofortPayment_Controller_Frontend_PaymentAbstract
    {
        protected $_loggingSource = "SofortBanking Controller";

        /**
         * This action handles the transition to the Sofort AG Payment Form
         * The sofortLib is used to generate the url to redirect the customer to.
         */
        public function gatewayAction()
        {
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
            $user   = $this->getUser();
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            //Prepare Parameters
            $configKey          = $helper->option()->getConfigKey( "sofortbanking" );
            $successUrl         = Shopware()->Front()->Router()->assemble( array(
                                                                                'action'        => 'return',
                                                                                'transactionId' => '-TRANSACTION-',
                                                                                'forceSecure'   => 1
                                                                           ) );
            $errorUrl           = Shopware()->Front()->Router()->assemble( array(
                                                                                'action'        => 'error',
                                                                                'forceSecure'   => 1,
                                                                                'transactionId' => '-TRANSACTION-',
                                                                           ) );
            $timeoutUrl         = Shopware()->Front()->Router()->assemble( array(
                                                                                'action'        => 'timeout',
                                                                                'transactionId' => '-TRANSACTION-',
                                                                                'forceSecure'   => 1
                                                                           ) );
            $notifyUrl          = Shopware()->Front()->Router()->assemble( array(
                                                                                'action'      => 'notify',
                                                                                'forceSecure' => 1
                                                                           ) );
            $reason             = $helper->option()->getReason( $this->getOrderNumber(), $user );
            $customerProtection = $helper->option()->isCustomerProtectionEnabled( "sofortbanking" );
			$timeout = ini_get('session.gc_maxlifetime') - 120;
			if($timeout <= 0){
				$timeout = ini_get('session.gc_maxlifetime');
			}
            //Use Lib to call API
            $sofort = $helper->library()->getMultipay( $configKey );
            $sofort->setLogger( new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger() );
            $sofort->setLogEnabled();
            $sofort->setSofortueberweisung();
            $sofort->setAmount( $this->getAmount(), $this->getCurrencyShortName() );
            $sofort->setReason( $reason[ 1 ], $reason[ 2 ] );
            $sofort->setSuccessUrl( $successUrl );
            $sofort->setAbortUrl( $errorUrl );
            $sofort->setTimeoutUrl( $timeoutUrl );
            $sofort->setNotificationUrl( $notifyUrl );
			$sofort->setTimeout($timeout);
            $sofort->setSofortueberweisungCustomerprotection( $customerProtection );
            $sofort->setVersion( "ShopWare4_" . Shopware()->Plugins()->Frontend()->SofortPayment()->getVersion() );
            $sofort->sendRequest();

            //Validate Data
            if ( $sofort->isError() ) {
                return $this->redirect( Shopware()->Front()->Router()->assemble(
                    array(
                        "action"      => "error",
                        "forceSecure" => 1
                    )
                ));
            }
            //Store TransactionId into session and order
            Shopware()->Session()->sofortTransactionId = $sofort->getTransactionId();
            if(!is_null($this->getOrderNumber())){
                $helper->database()->changeTransactionId( $this->getOrderNumber(), $sofort->getTransactionId() );
            }
            //Redirect customer if validation successfull
            $this->redirect( $sofort->getPaymentUrl(), array( "forceSecure" => 1 ) );
        }

        /**
         * This action acts as a link between the gateway and the success page.
         * In case of any validation you want to happen or data you want to be saved use this action
         */
        public function returnAction()
        {
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            //Change Orderstate
            $request         = $this->Request()->getParams();
            $transactionId   = $request[ 'transactionId' ];
            $configKey       = $helper->option()->getConfigKey( "sofortbanking" );
            $transactionData = $helper->library()->getTransactionDataInstance( $configKey );
            $transactionData->setTransaction( $transactionId );
            $transactionData->sendRequest();

            $this->finalizeOrder($transactionData->getStatus(), $transactionData->getReason(), $transactionId);

            //Redirect to the successAction
            $this->redirect( array(
                                  "action"      => "success",
                                  "forceSecure" => 1
                             ) );
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
        public function convertLibState( $state, $reason )
        {
            $status = '';
            switch ( $state ) {
                case "loss":
                    $status = "investigation_needed";
                    break;
                case "pending":
                    $status = "payment_confirmed";
                    break;
                case "received":
                    if ( $reason == 'consumer_protection' ) {
                        $status = "payment_confirmed";
                    }
                    if ( $reason == 'credited' ) {
                        $status = "payment_received";
                    }
                    break;
                case "refunded":
                    if ( $reason == 'compensation' ) {
                        $status = "refund_partial";
                    }
                    if ( $reason == 'refunded' ) {
                        $status = "refund_full";
                    }
                    break;
                case "untraceable":
                    if ( $reason == 'sofort_bank_account_needed' ) {
                        $status = "payment_confirmed";
                    }
                    break;
            }
            $haystack = array(
                "temporary",
                "payment_confirmed",
                "payment_received",
                "payment_canceled",
                "investigation_needed",
                "refund_partial",
                "refund_full"
            );
            if ( in_array( $state, $haystack ) ) {
                $status = $state;
            }
            $logger = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
            $logger->logManually( $this->_loggingSource, "Translated state $state to status $status." );
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();

            return $helper->option()->getStateTranslation( "sofortbanking", $status );
        }

        /**
         * This action handles the asynchronous callbacks
         * The sofortLib is used to obtain data about
         */
        public function notifyAction()
        {
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
            $helper    = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $configKey = $helper->option()->getConfigKey( "sofortbanking" );
            //Step 3: Handling Notifications about status changes
            $notification = $helper->library()->getNotificationInstance();
            $notification->setLogger( new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger() );
            $notification->setLogEnabled();
            $notification->getNotification();
            $transactionId = $notification->getTransactionId();
            //Step 4: Inquire changed Transactiondata
            $transactionData = new SofortLib_TransactionData( $configKey );
            $transactionData->setTransaction( $transactionId );
            $transactionData->sendRequest();
            //Step 5: Handling the Response to the Inquiry for changed Transaction Data
            $orderId = $helper->database()->getOrderByTransactionId( $transactionId );
            $state   = $this->convertLibState( $transactionData->getStatus(), $transactionData->getStatusReason() );
            $order   = Shopware()->Modules()->Order();
            if ( $state != 0 ) {
                $order->setPaymentStatus( $orderId, $state, false );
                $order->setOrderStatus($orderId, 0, false);
            }
        }
    }
