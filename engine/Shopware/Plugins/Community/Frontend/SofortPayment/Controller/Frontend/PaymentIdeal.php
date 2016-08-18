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
     * Paymentcontroller for the iDEAL Payment. Handles all actions occurring during the checkout process,
     * as well as the asynchronous notifications sent by the API.
     */
    class Shopware_Controllers_Frontend_PaymentIdeal
        extends Shopware_Plugins_Frontend_SofortPayment_Controller_Frontend_PaymentAbstract
    {
        protected $_loggingSource = "iDeal Controller";

        /**
         * This action handles the transition to the Sofort AG Payment Form
         * The sofortLib is used to generate the url to redirect the customer to.
         * For reference to the Sofort Documentation consider this action to execute step 1 and 2
         */
        public function gatewayAction()
        {
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
            $user   = $this->getUser();
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $logger = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
            //Prepare Parameters
            $configKey      = $helper->option()->getConfigKey( "ideal" );
            $password       = $helper->option()->getPassword();
            $customerId     = $user[ 'additional' ][ 'user' ][ 'id' ];
            $model          = Shopware()->Models()->getRepository( 'Shopware\Models\Customer\Customer' )
                              ->findOneById( $customerId );

            $senderBankCode = $model->getAttribute()->getSofortIdealBank();
            if ( $senderBankCode === null ) {
                $senderBankCode = Shopware()->Session()->sofortIdealBankCode;
                $logger->logManually( $this->_loggingSource, "Error during DB access. Using sender Bank code: " . $senderBankCode );
            } else {
                $logger->logManually( $this->_loggingSource, "Using sender Bank code: " . $senderBankCode );
            }
            $reason          = $helper->option()->getReason( $this->getOrderNumber(), $user );
            $senderCountryId = $user[ 'additional' ][ 'country' ][ 'countryiso' ];
            //Prepare API Call
            $sofort = $helper->library()->getIdealClassic( $configKey, $password );
            $sofort->getRelatedBanks();
            $sofort->setAmount( $this->getAmount(), $this->getCurrencyShortName() );
            $sofort->setSenderBankCode( $senderBankCode );
            $sofort->setReason( $reason[ '1' ], $reason[ '2' ] );
            $sofort->setSenderCountryId( $senderCountryId );
            $sofort->setSuccessUrl(
                Shopware()->Front()->Router()->assemble(
                    array(
                         'action'        => 'return',
                         'forceSecure'   => 1,
                         'transactionId' => '-TRANSACTION-',
                         'status'        => '-STATUS-',
                         'status_reason' => '-STATUS_REASON-'
                    )
                ) );
            $sofort->setAbortUrl(
                Shopware()->Front()->Router()->assemble(
                    array(
                         'action'        => 'error',
                         'forceSecure'   => 1,
                         'transactionId' => '-TRANSACTION-',
                         'sofortError'    => '-ERROR_CODES-'
                    )
                ) );
            $sofort->setNotificationUrl(
                Shopware()->Front()->Router()->assemble(
                    array(
                         'action'                => 'notify',
                         'forceSecure'           => 1,
                         'transactionId'         => '-TRANSACTION-',
                         'modificationTimestamp' => '-STATUS_MODIFIED-'
                    )
                ) );

            //Call API to get PaymentUrl
            $url = $sofort->getPaymentUrl();
            //Validate Data
            if ( $sofort->isError() ) {
                return $this->redirect(
                    Shopware()->Front()->Router()->assemble(
                    array(
                         "action"      => "error",
                         "forceSecure" => 1
                    )
                ));
            }
            //Redirect customer if validation successfull
            $this->redirect( $url, array( "forceSecure" => 1 ) );
        }

        /**
         * This action acts as a link between the gateway and the success page.
         * In case of any validation you want to happen or data you want to be saved use this action
         */
        public function returnAction()
        {
            $request = $this->Request()->getParams();
            //Gather Data
            $status            = $request[ 'status' ];
            $reason            = $request[ 'status_reason' ];
            $transactionId     = $request[ 'transactionId' ];

			$this->finalizeOrder($status, $reason, $transactionId);

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
            $logger = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
            $status = '';
            switch ( $state ) {
                case "loss":
                    $logger->logManually( "Debug", "State $state,Reason $reason." );
                    $status = "payment_canceled";
                    break;
                case "pending":
                    $logger->logManually( "Debug", "State $state,Reason $reason." );
                    $status = "payment_pending";
                    break;
                case "received":
                    $logger->logManually( "Debug", "State $state,Reason $reason." );
                    $status = "payment_received";
                    break;
                case "refunded":
                    $logger->logManually( "Debug", "State $state,Reason $reason." );
                    $status = "refund_full";
                    if ( $reason == "compensation" ) {
                        $status = "refund_partial";
                    }
                    break;
            }
            $haystack = array(
                "temporary",
                "payment_pending",
                "payment_received",
                "payment_canceled",
                "storno",
                "refund_partial",
                "refund_full"
            );
            if ( in_array( $state, $haystack ) ) {
                $status = $state;
            }
            $logger->logManually( $this->_loggingSource, "Translated state $state to status $status." );
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();

            return $helper->option()->getStateTranslation( "ideal", $status );
        }

        /**
         * This action handles the asynchronous callbacks
         * The sofortLib is used to obtain data about
         */
        public function notifyAction()
        {
            $request = $this->Request()->getParams();
            $helper  = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $logger  = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
            $notificationPassword = $helper->option()->getNotificationPassword();
            //Validate Notification
            if ( !$this->_validateResponse( $request, $notificationPassword ) ) {
                $logger->logManually( "Ideal Notification",
                    "Validationhash missmatch in Notification for Transaction " . $request[ 'transaction' ] );

                return;
            }
            $logger->logManually( "Ideal Notification",
                "Notification for Transaction " . $request[ 'transaction' ] . " received" );
            //Gather Data
            $transactionId = $request[ 'transaction' ];
            $status        = $request[ 'status' ];
            $reason        = $request[ 'status_reason' ];
            $orderId       = $helper->database()->getOrderByTransactionId( $transactionId );
            $ordernumber   = $helper->database()->getOrdernumberByTransactionId( $transactionId );
            $state         = $this->convertLibState( $status, $reason );
            //Change order state
            if ( $state != 0 ) {
                $order = Shopware()->Modules()->Order();
                $order->setPaymentStatus( $orderId, $state, false );
                $order->setOrderStatus($orderId, 0, false);
                $logger->logManually( "Ideal Notification", "Changing state of order $ordernumber to $state." );
            }
        }

        /**
         * Validates the response to make sure you are dealing with a valid notification
         *
         * @param array  $request
         * @param string $notification_password
         *
         * @return boolean indicator of success
         */
        private function _validateResponse( $request, $notification_password )
        {
            $hashString = $request[ 'transactionId' ] . "|" .
                          $request[ 'user_id' ] . "|" .
                          $request[ 'project_id' ] . "|" .
                          $request[ 'sender_holder' ] . "|" .
                          $request[ 'sender_account_number' ] . "|" .
                          $request[ 'sender_bank_name' ] . "|" .
                          $request[ 'sender_bank_bic' ] . "|" .
                          $request[ 'sender_iban' ] . "|" .
                          $request[ 'sender_country_id' ] . "|" .
                          $request[ 'recipient_holder' ] . "|" .
                          $request[ 'recipient_account_number' ] . "|" .
                          $request[ 'recipient_bank_code' ] . "|" .
                          $request[ 'recipient_bank_name' ] . "|" .
                          $request[ 'recipient_bank_bic' ] . "|" .
                          $request[ 'recipient_iban' ] . "|" .
                          $request[ 'recipient_country_id' ] . "|" .
                          $request[ 'amount' ] . "|" .
                          $request[ 'currency_id' ] . "|" .
                          $request[ 'reason_1' ] . "|" .
                          $request[ 'reason_2' ] . "|" .
                          $request[ 'user_variable_0' ] . "|" .
                          $request[ 'user_variable_1' ] . "|" .
                          $request[ 'user_variable_2' ] . "|" .
                          $request[ 'user_variable_3' ] . "|" .
                          $request[ 'user_variable_4' ] . "|" .
                          $request[ 'user_variable_5' ] . "|" .
                          $request[ 'created' ] . "|" .
                          $request[ 'status' ] . "|" .
                          $request[ 'status_modified' ] . "|" .
                          $notification_password;
            $hash       = sha1( $hashString );

            return $hash === $request[ 'hash' ];
        }

        /**
         * Action to save the iDEAL bankcode
         */
        public function saveBankAction(){
            Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
            $request    = $this->Request()->getParams();
            $eventHelper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_EventHelper();
            $eventHelper->saveBankId($request["sofort_ideal_bank_select"], Shopware()->Session()->offsetGet('sUserId'));
            echo "OK";
        }
    }
