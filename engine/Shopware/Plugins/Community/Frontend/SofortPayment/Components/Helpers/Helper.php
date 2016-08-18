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
     * This class is a summary object for all other helpers
     */
    class Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper
    {
        private $_databaseHelper = null;

        private $_eventHelper = null;

        private $_libraryHelper = null;

        private $_optionHelper = null;

        /**
         * Returns the Database Helper.
         * If called multiple times from the same object instance, the same object will be returned
         *
         * @return Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_DatabaseHelper
         */
        public function database()
        {
            if ( isset( $this->_databaseHelper ) ) {
                return $this->_databaseHelper;
            }
            $this->_databaseHelper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_DatabaseHelper();

            return $this->_databaseHelper;
        }

        /**
         * Returns the Event Helper.
         * If called multiple times from the same object instance, the same object will be returned
         *
         * @return Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_EventHelper
         */
        public function event()
        {
            if ( isset( $this->_eventHelper ) ) {
                return $this->_eventHelper;
            }
            $this->_eventHelper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_EventHelper();

            return $this->_eventHelper;
        }

        /**
         * Returns the Library Helper.
         * If called multiple times from the same object instance, the same object will be returned
         *
         * @return Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_LibraryHelper
         */
        public function library()
        {
            if ( isset( $this->_libraryHelper ) ) {
                return $this->_libraryHelper;
            }
            $this->_libraryHelper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_LibraryHelper();

            return $this->_libraryHelper;
        }

        /**
         * Returns the Option Helper.
         * If called multiple times from the same object instance, the same object will be returned
         *
         * @return Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_OptionHelper
         */
        public function option()
        {
            if ( isset( $this->_optionHelper ) ) {
                return $this->_optionHelper;
            }
            $this->_optionHelper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_OptionHelper();

            return $this->_optionHelper;
        }
    }
