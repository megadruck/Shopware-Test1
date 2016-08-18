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
     * This class handles all CSV Operations
     */
    class Shopware_Plugins_Frontend_SofortPayment_Components_Services_Csv
    {
        /**
         * @var string
         */
        private $_path = null;

        /**
         * @var array
         */
        private $_translations = null;

        /**
        * @var Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger
        */
        private $_logger = null;

        /**
         * Creates the instance for this class
         */
        public function __construct()
        {
            $this->_logger = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
        }

                /**
         * Load all CSV Files and Translations
         * If locale Property is set, only that locale is loaded
         *
         * @return bool Indicator of success
         */
        public function loadCsvFiles()
        {
            try {
                if ( ( $files = scandir( $this->getPath() ) ) ) {
                    foreach ( $files as $file ) {
                        if ( is_file( $this->getPath() . $file ) ) {
                            $this->loadCsvFileByName( $file );
                        }
                    }
                }
            } catch ( Exception $exc ) {
                $this->_logger->logManually(__CLASS__,$exc->getTraceAsString());
            }

            return false;
        }

        /**
         * Loads a single csv file by the given name
         *
         * @param string $filename Name of the file
         */
        public function loadCsvFileByName( $filename )
        {
            $translations = array();
            if ( ( $file = fopen( $this->getPath() . $filename, "r" ) ) !== false ) {
                while ( ( $data = fgetcsv( $file, 0, ';', '"' ) ) !== false ) {
                    if ( !( $data[ 0 ] == "Nr." && $data[ 1 ] == "String" ) ) {
                        $translations[ $data[ 0 ] ] = $data[ 1 ];
                    }
                }
            }
            $this->_translations[ basename( $filename, '.csv' ) ] = $translations;
        }

        /**
         * Returns the Path to the CSV Files
         *
         * @return string
         */
        public function getPath()
        {
            return $this->_path;
        }

        /**
         * Sets the Path to the CSV Files
         *
         * @param string $path
         */
        public function setPath( $path )
        {
            $this->_path = $path;
        }

        /**
         * Returns the translations
         *
         * @return array
         */
        public function getTranslations()
        {
            return $this->_translations;
        }
    }

