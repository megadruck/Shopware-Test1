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
     * This Helper class provides easier access to the SofortLib
     */
    class Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_LibraryHelper
    {
        /**
         * Returns the path to the lib directory
         *
         * @return String
         */
        private function _getBasicLibPath()
        {
            return Shopware()->Plugins()->Frontend()->SofortPayment()->Path() . "Library/";
        }

        /**
         * Allows the creating of SofortLib_iDealClassic objects from a  shop complinat environment
         *
         * @param String $configKey
         * @param String $password
         *
         * @return \SofortLib_iDealClassic
         */
        public function getIdealClassic( $configKey, $password )
        {
            require_once $this->_getBasicLibPath() . "sofortLib.php";
            require_once $this->_getBasicLibPath() . "sofortLib_ideal_classic.php";

            return new SofortLib_iDealClassic( $configKey, $password );
        }

        /**
         * Allows the creating of SofortLib_Multipay objects from a  shop complinat environment
         *
         * @param String $configKey
         *
         * @return \SofortLib_Multipay
         */
        public function getMultipay( $configKey )
        {
            require_once $this->_getBasicLibPath() . "sofortLib.php";

            return new SofortLib_Multipay( $configKey );
        }

        /**
         * Allows the creating of SofortLib_Notification objects from a  shop complinat environment
         *
         * @return \SofortLib_Notification
         */
        public function getNotificationInstance()
        {
            require_once $this->_getBasicLibPath() . "sofortLib.php";

            return new SofortLib_Notification();
        }

        /**
         * Returns an instance of the SofortLib_TransactionData class
         *
         * @param String $configKey
         *
         * @return \SofortLib_TransactionData
         */
        public function getTransactionDataInstance( $configKey )
        {
            require_once $this->_getBasicLibPath() . "sofortLib.php";

            return new SofortLib_TransactionData( $configKey );
        }
    }

