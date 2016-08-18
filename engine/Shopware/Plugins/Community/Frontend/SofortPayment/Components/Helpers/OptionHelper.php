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
     * This Helper class provides getter for all module specific config options
     */
    class Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_OptionHelper
    {
        /**
         * Returns the Configkey for the given payment payment method
         *
         * @param string $method
         *
         * @return string
         */
        public function getConfigKey( $method )
        {
            return $this->_getConfigValue( $method . "_key" );
        }

        /**
         * Returns the ideal password
         *
         * @return string password
         */
        public function getPassword()
        {
            return $this->_getConfigValue( "ideal_project_password" );
        }

        /**
         * Returns the ideal notification password
         *
         * @return String Notification Password
         */
        public function getNotificationPassword()
        {
            return $this->_getConfigValue( "ideal_notification_password" );
        }

        /**
         * Returns the value of the option with the given shortname
         *
         * @param string $name
         *
         * @throws Exception
         * @return string
         */
        private function _getConfigValue( $name )
        {
            if ( trim( Shopware()->Plugins()->Frontend()->SofortPayment()->Config()->get( "option_" . $name ) )
                 === null
            ) {
                $logger = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
                $logger->logManually( "Option Access", "Trying to get an invalid option."
                                                       . "option_" . $name . " does not exist." );
                throw new Exception( "Trying to get an invalid option." );
            } else {
                return trim( Shopware()->Plugins()->Frontend()->SofortPayment()->Config()->get( "option_" . $name ) );
            }
        }

        /**
         * Returns an array of both reason fields
         *
         * @param String $orderId
         * @param array  $user
         *
         * @return array reason
         */
        public function getReason( $orderId, $user )
        {
            $reason          = array();
            $reason[ 1 ]     = (string)$this->_getConfigValue( "general_reason_one" );
            $reason[ 2 ]     = (string)$this->_getConfigValue( "general_reason_two" );
            $orderDate       = date( "Y-M-D" );
            $customerId      = $user[ 'billingaddress' ][ 'customernumber' ];
            $customerName    = ( $user[ 'billingaddress' ][ 'firstname' ] . " "
                                 . $user[ 'billingaddress' ][ 'lastname' ] );
            $customerCompany = $user[ 'billingaddress' ][ 'company' ];
            $customerEmail   = $user[ 'additional' ][ 'user' ][ 'email' ];
            foreach ( $reason as $key => $value ) {
                //Transaction
                $reason[ $key ] = preg_replace( '/{{transaction_id}}/', "-TRANSACTION-", $value );
                //Order_number
                $reason[ $key ] = preg_replace( '/{{order_id}}/', $orderId, $reason[ $key ] );
                //Order_date
                $reason[ $key ] = preg_replace( '/{{order_date}}/', $orderDate, $reason[ $key ] );
                //customer_id
                $reason[ $key ] = preg_replace( '/{{customer_id}}/', $customerId, $reason[ $key ] );
                //customer_name
                $reason[ $key ] = preg_replace( '/{{customer_name}}/', $customerName, $reason[ $key ] );
                //customer_company
                $reason[ $key ] = preg_replace( '/{{customer_company}}/', $customerCompany, $reason[ $key ] );
                //customer_email
                $reason[ $key ] = preg_replace( '/{{customer_email}}/', $customerEmail, $reason[ $key ] );
            }
            if(empty($reason[1])){
                $reason[1] = 'SOFORT AG';
            }

            return $reason;
        }

        /**
         * Returns the state of the logging option
         *
         * @return boolean
         */
        public function isLogging()
        {
            return (boolean)$this->_getConfigValue( "general_logging" );
        }

        /**
         * Returns the state of the "remove_failed_transactions" option
         *
         * @return boolean
         */
        public function isRemovingFailedTransactions()
        {
            return (boolean)$this->_getConfigValue( "general_remove_failed_transactions" );
        }

        /**
         * Returns the current Frontend Display Type of the chosen payment method.
         * Possible outcomes are:
         *      1. banner
         *      2. icon and text
         *
         * @param String $method Name of the Payment Method
         *
         * @return String
         */
        public function getFrontendDisplayType( $method )
        {
            return (string)$this->_getConfigValue( $method . "_frontend_display" );
        }

        /**
         * Returns the state of customer protection for the given payment method
         *
         * @return boolean
         */
        public function isCustomerProtectionEnabled()
        {
            return (boolean)$this->_getConfigValue( "sofortbanking_customer_protection" );
        }

        /**
         * Returns the state of the recommended flag for the given payment method
         *
         * @param String $method Name of the Payment Method
         *
         * @return boolean
         */
        public function isRecommendedPayment( $method )
        {
            return (boolean)$this->_getConfigValue( $method . "_recommended_payment" );
        }

        /**
         * Returns the statenumber of the given state for the given method
         *
         * @param String $method Name of the Payment Method
         * @param String $state
         *
         * @throws Exception
         * @return int Status
         */
        public function getStateTranslation( $method, $state )
        {
            $logger = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
            if ( $method === "sofortideal" ) {
                $method = 'ideal';
            }
            $haystack = '';
            if ( $method === "ideal" ) {
                $haystack = array(
                    "temporary",
                    "payment_pending",
                    "payment_received",
                    "payment_canceled",
                    "storno",
                    "refund_partial",
                    "refund_full"
                );
            }
            if ( $method === "sofortbanking" ) {
                $haystack = array(
                    "temporary",
                    "payment_confirmed",
                    "payment_received",
                    "payment_canceled",
                    "investigation_needed",
                    "refund_partial",
                    "refund_full"
                );
            }
            if ( in_array( $state, $haystack ) ) {
                $logger->logManually( "Optionhelper, State Translation", "Paymentmean: $method, " .
                                                                         "State changing to $state, returning " .
                                                                         $this->_getConfigValue( $method . "_state_"
                                                                                                 . $state ) );

                return (int)$this->_getConfigValue( $method . "_state_" . $state );
            }
            $logger->logManually( "Optionhelper, State Translation", "Invalid order state: " .
                                                                     var_export( $method ) . ", "
                                                                     . var_export( $state ) );
            throw new Exception( "Invalid order state" );
        }
    }

