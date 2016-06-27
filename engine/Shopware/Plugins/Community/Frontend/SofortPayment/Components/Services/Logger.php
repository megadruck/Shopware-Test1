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
     * Logger class.
     * This Class handles all logging methods
     */
    class Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger
    {
        /**
         * Log Method required by the lib.
         *
         * @param String $message Message to be logged
         * @param String $uri     File to be logged into
         * (needed by lib, not used since logging target will always be the database)
         */
        public function log( $message, $uri )
        {
            $this->_logIntoDb( "Sofort Library", $message );
        }

        /**
         * Create a log entry for the given arguments
         *
         * @param String $className Name of the class the entry is made by.
         * @param String $message   Message to be logged
         */
        public function logManually( $className, $message )
        {
            $this->_logIntoDb( $className, $message );
        }

        /**
         * Persists the log entrie in the database
         *
         * @param String $source String describing the source of this log entry.
         *                       Can be either a classname or the term "Sofort Library"
         * @param String $message
         */
        private function _logIntoDb( $source, $message )
        {
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            if ( $helper->option()->isLogging() ) {
                $moduleVersion = Shopware()->Plugins()->Frontend()->SofortPayment()->getVersion();
                $helper->database()->insertLogEntry( $moduleVersion, $source, $message );
            }
        }
    }
