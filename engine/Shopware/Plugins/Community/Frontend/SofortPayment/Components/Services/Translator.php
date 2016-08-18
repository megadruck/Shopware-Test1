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
     * Translator class
     */
    class Shopware_Plugins_Frontend_SofortPayment_Components_Services_Translator
    {
        private $_language;

        private $_languagesAvailable = array( "de", "en", "nl", "pl", "fr", "it", "es" );

        private $_fallbackLanguage = "de";

        /**
         * Creates an instance of this class.
         */
        public function __construct()
        {
            try {
                $this->_language = Shopware()->Shop()->getLocale()->getLocale();
            } catch ( Exception $exception ) {
                $this->_language = "de_DE";
                //No Logging or error handling since this might occur if the translator is used during
                //plugin installation
            }

        }

        /**
         * Returns the language tag that is currently used by the translator.
		 *
		 * @return String
         */
        public function getLanguageShortName()
        {
            $lang = $this->_getLanguageTagFromShortname( $this->_language );

            return in_array( $lang, $this->_languagesAvailable ) ? $lang : "de";
        }

        /**
         * Returns the language tag from the shortname.
         * e.x. en_gb -> en, de_DE -> de
         *
         * @param String $languageShortname
         *
         * @return String
         */
        private function _getLanguageTagFromShortname( $languageShortname )
        {
            return substr( $languageShortname, 0, 2 );
        }

        /**
         * Sets the Language for the translator to handle
         *
         * @param String $language
         */
        public function setLanguage( $language )
        {
            $this->_language = $language;
        }

        /**
         * Returns the Sofortbanking Logo depending on the current language
         *
         * @return String
         */
        public function getSofortbankingLogo()
        {
            return $this->_getImageBaseUrl( $this->_language ) . "su/logo_155x50.png";
        }

        /**
         * Returns the base url of all images used in the module
         *
         * @param String $language (f.ex de_DE)
         *
         * @return String Base Url
         */
        private function _getImageBaseUrl( $language )
        {
            $languageShortname = $this->_getLanguageTagFromShortname( $language );
            if ( !( in_array( $languageShortname, $this->_languagesAvailable ) ) ) {
                $languageShortname = $this->_fallbackLanguage;
            }

            return "https://images.sofort.com/" . $languageShortname . "/";
        }

        /**
         * Returns the Sofortbanking Banner depending on the current language
         *
         * @param boolean $customerProtection
         *
         * @return String
         */
        public function getSofortbankingBanner( $customerProtection )
        {
            if ( $customerProtection ) {
                return $this->_getImageBaseUrl( $this->_language ) . "su/banner_400x100_ks.png";
            }

            return $this->_getImageBaseUrl( $this->_language ) . "su/banner_300x100.png";
        }

        /**
         * Returns the Ideal Logo depending on the current language
         *
         * @return String
         */
        public function getIdealLogo()
        {
            return $this->_getImageBaseUrl( $this->_language ) . "/ideal/logo_155x50.png";
        }

        /**
         * Returns the Ideal Banner depending on the current language
         *
         * @return String
         */
        public function getIdealBanner()
        {
            return $this->_getImageBaseUrl( $this->_language ) . "ideal/banner_300x100.png";
        }

        /**
         * Returns the snippet mapped to the number from the specification
         *
         * @param String $number
         * @param String $defaulttext
         *
         * @return String
         */
        public function getSnippetByNumber( $number, $defaulttext = null )
        {
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();

            return $helper->database()->getTranslationSnippet( $number, $defaulttext, $this->_language );
        }

        /**
         * Adds a translation set for the payment with the given key
         *
         * @param String $key                   Key of the payment (numeric)
         * @param String $description
         * @param String $additionalDescription (optional)
         */
        public function addPaymentTranslation( $key, $description, $additionalDescription = "" )
        {
            $translationObject               = new Shopware_Components_Translation();
            $helper                          = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $data                            = array();
            $data[ 'description' ]           = $description;
            $data[ 'additionalDescription' ] = $additionalDescription;
            $languages                        = $helper->database()->getShopIds( $this->_language );

            foreach($languages as $language){
                $translationObject->write( $language['id'], "config_payment", $key, $data, 1 );
            }

        }
    }
