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
     * This Helper class contains all methods called during non-controller events.
     * This is meant to avoid unwanted length of the bootstrap class.
     */
    class Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_EventHelper
    {
        /**
         * Displays the errormessages during checkout.
         */
        public function displayErrors( $view )
        {
            if(isset(Shopware()->Session()->pigmbhErrorMessage)) {
                $view->sErrorMessages = array(Shopware()->Session()->pigmbhErrorMessage);
                $logger = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Logger();
                $logger->logManually( "Errorhandling:", "Sessionmessage: " . Shopware()->Session()->pigmbhErrorMessage );
                Shopware()->Session()->pigmbhErrorMessage = null;
            }
        }

        /**
         * Displays either banner or logo with text for a payment description.
         *
         * @param Enlight_Event_EventArgs                          $arguments
         * @param Shopware_Plugins_Frontend_SofortPayment_Bootstrap $bootstrap
         */
        public function designPaymentMeanSelection( Enlight_Event_EventArgs $arguments,
                                                    Shopware_Plugins_Frontend_SofortPayment_Bootstrap $bootstrap )
        {
            $view              = $arguments->getSubject()->View();
            $helper            = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $translator        = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Translator();
            $languageShortName = $translator->getLanguageShortName();

            //Assign Sofortbanking template variables
            $view->sofortSofortbankingIsCustomerProtectionEnabled  = $helper->option()->isCustomerProtectionEnabled();
            $view->sofortSofortBankingLinkBannerLogo               =
                "https://images.sofort.com/" . $languageShortName . "/su/landing.php";
            $view->sofortSofortBankingLinkBannerCustomerProtection =
                $languageShortName === "de" ? "https://www.handelsbank.com/de/deutsche-handelsbank-kaeuferschutz/deutsche-handelsbank-kaeuferschutz.html" : "https://www.handelsbank.com/en/buyer-protection/information-for-buyers.html";

            $view->sofortSofortbankingIsRecommended                = $helper->option()
                                                                     ->isRecommendedPayment( "sofortbanking" );
            $view->sofortSofortbankingIsShowingBanner              = $helper->option()
                                                                     ->getFrontendDisplayType( "sofortbanking" ) == 1;
            $view->sofortSofortbankingIsShowingLogo                = $helper->option()
                                                                     ->getFrontendDisplayType( "sofortbanking" ) == 2;
            $view->sofortSofortbankingLogo                         = $translator->getSofortbankingLogo();
            $view->sofortSofortbankingBanner                       = $translator->getSofortbankingBanner( $customerProtection = false );
            $view->sofortSofortbankingBannerCp                     = $translator->getSofortbankingBanner( $customerProtection = true );
            $view->sofortSofortbankingRecommendedText              = $translator->getSnippetByNumber( "1003", "(empfohlene Zahlart)" );
            $view->sofortSofortbankingAlt                          = $translator->getSnippetByNumber( "1001", "SOFORT Überweisung" );
            $view->sofortSofortbankingLogoText                     = $translator->getSnippetByNumber( "1004",
                "* Zahlungssystem mit TÜV-geprüftem Datenschutz <br />" .
                "* Keine Registrierung notwendig <br />" .
                "* Ware/Dienstleistung wird bei Verfügbarkeit SOFORT versendet <br />" .
                "* Bitte halten Sie Ihre Online-Banking-Daten (PIN/TAN) bereit"
            );
            $view->sofortSofortbankingLogoTextCp                   = $translator->getSnippetByNumber( "1005",
                "* Bei Bezahlung mit SOFORT Überweisung genießen Sie Käuferschutz!" .
                " <a href='https://www.handelsbank.com/de/deutsche-handelsbank-kaeuferschutz/deutsche-handelsbank-kaeuferschutz.html'>" .
                "Mehr Informationen</a><br />"
            );
            //Assign Ideal template variables
            $configKey                        = $helper->option()->getConfigKey( "ideal" );
            $password                         = $helper->option()->getPassword();
            $sofort                           = $helper->library()->getIdealClassic( $configKey, $password );
            $view->sofortIdealAlt             = $translator->getSnippetByNumber( "2001", "iDEAL" );
            $view->sofortIdealRecommendedText = $translator->getSnippetByNumber( "1003", "(empfohlene Zahlart)" );
            $view->sofortIdealWelcomeMessage  = $translator->getSnippetByNumber( "2002", "Bitte wählen Sie Ihre Bank:" );
            $view->sofortIdealBanks           = $sofort->getRelatedBanks(); //get all iDEAL-Banks
            $view->sofortIdealIsRecommended   = $helper->option()->isRecommendedPayment( "ideal" );
            $view->sofortIdealIsShowingBanner = $helper->option()->getFrontendDisplayType( "ideal" ) == 1;
            $view->sofortIdealIsShowingLogo   = $helper->option()->getFrontendDisplayType( "ideal" ) == 2;
            $view->sofortIdealBanner          = $translator->getIdealBanner();
            $view->sofortIdealLogo            = $translator->getIdealLogo();
            $view->sofortIdealLink            =
                "https://images.sofort.com/" . $languageShortName . "/ideal/landing.php";
        }

        /**
         * Saves the Bank Id for Ideal payments
         *
         * @param Enlight_Event_EventArgs $arguments
         */
        public function saveBankId($bankname, $userId)
        {
            $model = Shopware()->Models()->getRepository( 'Shopware\Models\Customer\Customer' )->findOneById( $userId );
            $model->getAttribute()->setSofortIdealBank( $bankname );
            Shopware()->Session()->sofortIdealBankCode = $bankname;
            $manager = Shopware()->Models();
            $manager->persist( $model );
            $manager->flush();
        }
    }
