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
 * The Installer class
 */
class Shopware_Plugins_Frontend_SofortPayment_Components_Services_Setup
{
    /** @var Shopware_Components_Plugin_Bootstrap */
    private $_bootstrap = null;
    /**
	 * @var type
	 */
	private $_configHelper = null;

    /**
     * Creates an Installer object
     *
     * @param Shopware_Components_Plugin_Bootstrap $bootstrap Instance of the plugins bootstrap class
     */
    public function __construct(Shopware_Components_Plugin_Bootstrap $bootstrap)
    {
        $this->_bootstrap = $bootstrap;
		$this->_configHelper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_ConfigHelper();
    }

    /**
     * Calls all installation methods
     */
    public function install()
    {
        $this->_createTables();
        $this->_createConfig();
        $this->_createPayments();
        $this->_createEvents();
        $this->_createRuleSet();
        $languages = $this->_addTranslationSnippets();
        $this->_createConfigTranslations();
        $this->_translatePayments($languages);
        $this->_applyAdminViewModifications();
    }

    /**
     * Creates a ruleset for iDeal
     */
    private function _createRuleSet()
    {
        $payment = $this->_bootstrap->Payments()->findOneBy(array('name' => 'sofortideal'));
		$ruleset = new Shopware\Models\Payment\RuleSet;
		$ruleset->setPayment($payment);
		$ruleset->setRule1('LANDISNOT');
		$ruleset->setValue1('NL');
		$ruleset->setRule2('');
		$ruleset->setValue2(0);
		Shopware()->Models()->persist($ruleset);
    }

    /**
     * Creates all tables needed by the module
     */
    private function _createTables()
    {
        $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
        $helper->database()->createLoggingTable();
        $helper->database()->createBasketTable();
        Shopware()->Models()->addAttribute(
        's_user_attributes', 'sofort', 'ideal_bank', 'varchar(255)');
        Shopware()->Models()->generateAttributeModels(array('s_user_attributes'));
    }

    /**
     * Creates the Admin Config for the plugin
     *
     * @throws Exception
     */
    private function _createConfig()
    {
        $sql = "DELETE FROM s_core_config_element_translations
               WHERE element_id IN (SELECT s_core_config_elements.id FROM s_core_config_elements
               WHERE s_core_config_elements.form_id = (SELECT s_core_config_forms.id FROM s_core_config_forms
               WHERE s_core_config_forms.plugin_id = ?));
               DELETE FROM s_core_config_elements
               WHERE form_id = (SELECT id FROM s_core_config_forms WHERE plugin_id = ?);";
		Shopware()->Db()->query($sql, array($this->_bootstrap->getId(), $this->_bootstrap->getId()));

		$savedData = $this->_configHelper->loadData();

		$form = $this->_bootstrap->Form();
        $temporaryStates = array(
            array(12, "Komplett bezahlt"),
            array(17, "Offen"),
            array(20, "Wiedergutschrift"),
            array(21, "Überprüfung notwendig"),
            array(35, "Vorgang wurde abgebrochen.")
        );
        $states = array(
            array(0, " *keine Statusänderung* "),
            array(12, "Komplett bezahlt"),
            array(17, "Offen"),
            array(20, "Wiedergutschrift"),
            array(21, "Überprüfung notwendig"),
            array(35, "Vorgang wurde abgebrochen.")
        );
		//Grundeinstellungen
        $form->setElement('button', 'option_lable_basic_settings',
            array(
                 'label' => 'Plugin Mainconfiguration',
            ));
        $form->setElement('text', 'option_general_reason_one',
            array(
                 'label'       => 'Verwendungszweck 1:',
                 'description' => 'Legt den Text fest, der als Verwendungszweck bei der Überweisung angegeben' .
                                  'wird (max. 27 Zeichen - Sonderzeichen werden ersetzt/gelöscht).',
                 'required'    => false,
                 'value'       => (string)$savedData['option_general_reason_one'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('text', 'option_general_reason_two',
            array(
                 'label'       => 'Verwendungszweck 2:',
                 'description' => 'Legt den Text fest, der als Verwendungszweck bei der Überweisung angegeben' .
                                  'wird (max. 27 Zeichen - Sonderzeichen werden ersetzt/gelöscht).',
                 'required'    => false,
                 'value'       => (string)$savedData['option_general_reason_two'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('boolean', 'option_general_logging',
            array(
                 'label'       => 'Logging aktivieren ',
                 'description' => 'Aktiviert die Protokollierung der Aktionen des Moduls.' .
                                  ' Sollte nur zur Fehlersuche aktiviert werden.',
                 'required'    => false,
                 'value'       => (bool)$savedData['option_general_logging'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('boolean', 'option_general_create_transactions',
            array(
                 'label'       => 'Bestellung vor Weiterleitung erstellen',
                 'description' => 'Legt fest, ob die Bestellung bereits mit Weiterleitung des Käufers auf das Zahlformular von SOFORT Überweisung angelegt wird, oder erst nachdem die SOFORT Überweisung erfolgreich durchgeführt wurde.',
                 'required'    => false,
                 'value'       => (bool)$savedData['option_general_create_transactions'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        //Einstellungen: SofortBanking
        $form->setElement('button', 'option_lable_settings_sofortbanking',
            array(
                 'label' => 'Configuration: SOFORT Banking',
            ));
        $form->setElement('select', 'option_sofortbanking_frontend_display',
            array(
                 'label'       => 'Banner oder Text bei der Auswahl der Zahlarten:',
                 'description' => 'Legt fest, ob in der Auswahl der Zahlarten ein Logo und beschreibender Text' .
                                  ' oder nur ein Banner angezeigt wird.',
                 'store'       => array(
                     array(1, 'Banner'),
                     array(2, 'Logo & Text')
                 ),
                 'value'       => (int)$savedData['option_sofortbanking_frontend_display'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('text', 'option_sofortbanking_key',
            array(
                 'label'       => 'Konfigurationsschlüssel:',
                 'description' => 'Von SOFORT zugewiesener Konfigurationsschlüssel - dient u.a. als Passwort',
                 'required'    => true,
                 'value'       => (string)$savedData['option_sofortbanking_key'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('boolean', 'option_sofortbanking_customer_protection',
            array(
                 'label'       => 'Käuferschutztext/-banner anzeigen',
                 'description' => 'Legt fest, der Infotext bzw. der Käuferschutz Banner in der Auswahl' .
                                  ' der Zahlarten angezeigt wird. Die Anzeige darf nur durchgeführt werden,' .
                                  ' wenn im Projekt bei SOFORT der Käuferschutz aktiviert ist.',
                 'required'    => false,
                 'value'       => (bool)$savedData['option_sofortbanking_customer_protection'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('boolean', 'option_sofortbanking_recommended_payment',
            array(
                 'label'       => 'Empfohlene Zahlart',
                 'description' => 'Diese Zahlart als "Empfohlene Zahlart" bei der Auswahl der Zahlarten markieren.',
                 'required'    => false,
                 'value'       => (bool)$savedData['option_sofortbanking_recommended_payment'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('button', 'option_lable_settings_sofortbanking_states',
            array(
                 'label' => 'Select a status for every Sofort paymentstatus change:',
            ));
        $form->setElement('select', 'option_sofortbanking_state_temporary',
            array(
                 'label'       => 'Temporär:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die noch nicht bezahlt wurden.',
                 'store'       => $temporaryStates,
                 'value'       => (int)$savedData['option_sofortbanking_state_temporary'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_sofortbanking_state_payment_confirmed',
            array(
                 'label'       => 'Bestätigte Zahlung:',
                 'description' => 'Definieren Sie den Status für Bestellungen,' .
                                  ' die mit SOFORT Überweisungen erfolgreich abgeschlossen wurden.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_sofortbanking_state_payment_confirmed'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_sofortbanking_state_payment_received',
            array(
                 'label'       => 'Geldeingang:',
                 'description' => 'Definieren Sie den Status für Bestellungen,' .
                                  ' bei denen das Geld auf dem Konto eingegangen ist. Nur mit SOFORT Bank Konto verfügbar.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_sofortbanking_state_payment_received'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_sofortbanking_state_payment_canceled',
            array(
                 'label'       => 'Abgebrochene Zahlung:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die abgebrochen wurden.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_sofortbanking_state_payment_canceled'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_sofortbanking_state_investigation_needed',
            array(
                 'label'       => 'Bestellung prüfen:',
                 'description' =>
                     'Definieren Sie den Status für Bestellungen, die einer Überprüfung der Zahlung bedürfen.' .
                     ' Nur mit SOFORT Bank Konto verfügbar.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_sofortbanking_state_investigation_needed'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_sofortbanking_state_refund_partial',
            array(
                 'label'       => 'Teilerstattung:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die teilweise erstattet werden.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_sofortbanking_state_refund_partial'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_sofortbanking_state_refund_full',
            array(
                 'label'       => 'Erstattung:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die vollständig erstattet werden.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_sofortbanking_state_refund_full'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        //Einstellungen: iDeal
        $form->setElement('button', 'option_lable_settings_ideal',
            array(
                 'label' => 'Configuration: iDeal',
            ));
        $form->setElement('select', 'option_ideal_frontend_display',
            array(
                 'label'       => 'Banner oder Text bei der Auswahl der Zahlarten:',
                 'description' => 'Legt fest, ob in der Auswahl der Zahlarten ein Logo und' .
                                  ' beschreibender Text oder nur ein Banner angezeigt wird.',
                 'store'       => array(
                     array(1, 'Banner'),
                     array(2, 'Logo & Text')
                 ),
                 'value'       => (int)$savedData['option_ideal_frontend_display'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('text', 'option_ideal_key',
            array(
                 'label'       => 'Konfigurationsschlüssel:',
                 'description' => 'Von SOFORT zugewiesener Konfigurationsschlüssel.' .
                                  ' Kann dem Webinterface unter Meine Projekte->[Ideal Projektname]' .
                                  ' als Konfiurationsschlüssel entnommen werden.',
                 'required'    => true,
                 'value'       => (string)$savedData['option_ideal_key'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('text', 'option_ideal_project_password',
            array(
                 'label'       => 'Projektpasswort',
                 'description' => '',
                 'required'    => true,
                 'value'       => (string)$savedData['option_ideal_project_password'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('text', 'option_ideal_notification_password',
            array(
                 'label'       => 'Benachrichtigungspasswort',
                 'description' => '',
                 'required'    => true,
                 'value'       => (string)$savedData['option_ideal_notification_password'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('boolean', 'option_ideal_recommended_payment',
            array(
                 'label'       => 'Empfohlene Zahlart',
                 'description' => 'Diese Zahlart als "Empfohlene Zahlart" bei der Auswahl der Zahlarten markieren.',
                 'required'    => false,
                 'value'       => (bool)$savedData['option_ideal_recommended_payment'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('button', 'option_lable_settings_ideal_states',
            array(
                 'label' => 'Select a status for every iDeal paymentstatus change:',
            ));
        $form->setElement('select', 'option_ideal_state_temporary',
            array(
                 'label'       => 'Temporär:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die noch nicht bezahlt wurden.',
                 'store'       => $temporaryStates,
                 'value'       => (int)$savedData['option_ideal_state_temporary'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_ideal_state_payment_pending',
            array(
                 'label'       => 'Schwebende Zahlung:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die fertig bezahlt wurden,' .
                                  ' wobei iDEAL die Zahlung noch nicht als garantiert bestätigt hat.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_ideal_state_payment_pending'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_ideal_state_payment_received',
            array(
                 'label'       => 'Bestätigte Zahlung:',
                 'description' => 'Definieren Sie den Status für Bestellungen,' .
                                  ' die fertig bezahlt und durch iDEAL als garantiert bestätigt wurden.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_ideal_state_payment_received'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_ideal_state_payment_canceled',
            array(
                 'label'       => 'Abgebrochene Zahlung:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die abgebrochen wurden.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_ideal_state_payment_canceled'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_ideal_state_storno',
            array(
                 'label'       => 'Stornierte Zahlung:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die durch iDEAL abgebrochen wurden.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_ideal_state_storno'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_ideal_state_refund_partial',
            array(
                 'label'       => 'Teilerstattung:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die teilweise erstattet werden.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_ideal_state_refund_partial'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
        $form->setElement('select', 'option_ideal_state_refund_full',
            array(
                 'label'       => 'Erstattung:',
                 'description' => 'Definieren Sie den Status für Bestellungen, die vollständig erstattet werden.',
                 'store'       => $states,
                 'value'       => (int)$savedData['option_ideal_state_refund_full'],
                'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
            ));
    }

    /**
     * Creates the payment means
     *
     * @throws Exception
     */
    private function _createPayments()
    {
        $this->_bootstrap->createPayment(
                         array(
                              'name'                  => 'sofortbanking',
                              'description'           => 'SOFORT Überweisung / SOFORT Banking',
                              'action'                => 'payment_sofortbanking',
                              'active'                => 0,
                              'template'                => 'sofortbanking.tpl',
                              'additionalDescription' => ''
                         )
        );
        $this->_bootstrap->createPayment(
                         array(
                              'name'                  => 'sofortideal',
                              'description'           => 'iDEAL',
                              'action'                => 'payment_ideal',
                              'active'                => 0,
                             'template'                => 'sofortideal.tpl',
                             'additionalDescription' => ''
                         )
        );
    }

    /**
     * Creates the Events for the plugin
     *
     * @throws Exception
     */
    private function _createEvents()
    {
        $this->_bootstrap->subscribeEvent('Enlight_Controller_Action_PreDispatch_Frontend_Account', 'onCheckoutConfirm');
        $this->_bootstrap->subscribeEvent('Shopware_Modules_Order_SendMail_Send', 'disableStatusMails');
        $this->_bootstrap->subscribeEvent('Shopware_Controllers_Frontend_Checkout::saveShippingPaymentAction::before', 'onSavePayment');
        $this->_bootstrap->subscribeEvent('Shopware_Controllers_Frontend_Account::savePaymentAction::before', 'onSavePayment');
        $this->_bootstrap->subscribeEvent('Theme_Compiler_Collect_Plugin_Javascript', 'addJsFiles');
        $this->_bootstrap->subscribeEvent('Enlight_Controller_Action_PostDispatch', 'onPostDispatch');
        $this->_bootstrap->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Frontend_PaymentSofortbanking', 'registerSofortbankingController');
        $this->_bootstrap->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Frontend_PaymentIdeal', 'registerIdealController');
        $this->_bootstrap->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Backend_SofortLogView', 'registerAdminViewLogController');
        $this->_bootstrap->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Backend_SofortOrderView','registerAdminViewOrderController');
    }

    /**
     * Adds the translation snippets for the frontend and admin view to the shopware tables
     *
     * @throws Exception
     * @return array Languages returns an array of all languages currently supported
     */
    private function _addTranslationSnippets()
    {
        $languages = array();
        $csv = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Csv();
        $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();

        //Add snippets from the csv files
        $csv->setPath($this->_bootstrap->Path() . "snippets/");
        $csv->loadCsvFiles();
        $translations = $csv->getTranslations();

        //Add Backend Snippets that are not included in the csv files
        $translations['de_DE']['sofort_admin_view_lable_date'] = 'Datum';
        $translations['de_DE']['sofort_admin_view_lable_version'] = 'Version';
        $translations['de_DE']['sofort_admin_view_lable_source'] = 'Ursprung';
        $translations['de_DE']['sofort_admin_view_lable_entry'] = 'Eintrag';
        $translations['de_DE']['sofort_admin_view_lable_action'] = 'Aktionen';
        $translations['en_GB']['sofort_admin_view_lable_date'] = 'Date';
        $translations['en_GB']['sofort_admin_view_lable_version'] = 'Version';
        $translations['en_GB']['sofort_admin_view_lable_source'] = 'Source';
        $translations['en_GB']['sofort_admin_view_lable_entry'] = 'Entry';
        $translations['en_GB']['sofort_admin_view_lable_action'] = 'Actions';

        //Save snippets
        try {
            foreach ($translations as $locale => $snippets) {
                $languages[] = $locale;

                $parameter['localeId'] = $helper->database()->getLocaleId($locale);
                $parameter['shopIds'] = $helper->database()->getShopIds($locale);
                array_walk($snippets, array(Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_DatabaseHelper,
                                            'addLocaleSnippet'), $parameter);
            }
        } catch (Exception $exception) {
            $this->uninstall();
            throw new Exception("Can not insert translation-snippets." . $exception->getMessage());
        }

        return $languages;
    }

    /**
     * Calls all uninstalling methods
     */
    public function uninstall()
    {
        $this->_configHelper->saveData();
		$this->_cleanDb();
    }

    /**
     * Removes all Tables and DB entries made by the plugin
     */
    private function _cleanDb()
    {
        $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
        $sofortidealId = $helper->database()->getPaymentKey("sofortideal");
        $sofortbankingId = $helper->database()->getPaymentKey("sofortbanking");
        $helper->database()->removeLogTable();
        $helper->database()->removeBasketTable();
        $helper->database()->removeSnippets();
        $helper->database()->removePaymentRules($sofortidealId);
        $helper->database()->removePaymentRules($sofortbankingId);
        try {
            Shopware()->Models()->removeAttribute(
            's_user_attributes', 'sofort', 'ideal_bank'
            );
            Shopware()->Models()->generateAttributeModels(array(
                                                               's_user_attributes'
                                                          ));
        } catch (Exception $exception) {
            //Workaround since shopware attributes may cause crashes in 413
        }
    }

    /**
     * Creates the Admin Translations for the plugin
     *
     * @throws Exception
     */
    private function _createConfigTranslations()
    {
        $mapping = array(
            'option_lable_basic_settings'                     => array(
                'label' => '4004'
            ),
            'option_general_reason_one'                       => array(
                'label'       => '1623',
                'description' => '1624'
            ),
            'option_general_reason_two'                       => array(
                'label'       => '1625',
                'description' => '1624'
            ),
            'option_general_create_transactions'              => array(
                'label'       => '4001',
                'description' => '4002'
            ),
            'option_general_logging'                          => array(
                'label'       => '1630',
                'description' => '1631'
            ),
            'option_general_remove_failed_transactions'       => array(
                'label'       => '1654',
                'description' => '1655'
            ),
            'option_lable_settings_sofortbanking'             => array(
                'label' => '4005'
            ),
            'option_sofortbanking_frontend_display'           => array(
                'label'       => '1613',
                'description' => '1614',
                'store'       => array(
                    array(1, '1615'),
                    array(2, '1616')
                )
            ),
            'option_sofortbanking_key'                        => array(
                'label'       => '1606',
                'description' => '1607'
            ),
            'option_sofortbanking_customer_protection'        => array(
                'label'       => '1617',
                'description' => '1618'
            ),
            'option_sofortbanking_recommended_payment'        => array(
                'label'       => '1619',
                'description' => '1620'
            ),
            'option_lable_settings_sofortbanking_states'      => array(
                'label' => '1632'
            ),
            'option_sofortbanking_state_temporary'            => array(
                'label'       => '1633',
                'description' => '1634',
                'store'       => array(
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_sofortbanking_state_payment_confirmed'    => array(
                'label'       => '1635',
                'description' => '1636',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_sofortbanking_state_payment_received'     => array(
                'label'       => '1637',
                'description' => '1638',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_sofortbanking_state_payment_canceled'     => array(
                'label'       => '1639',
                'description' => '1640',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_sofortbanking_state_investigation_needed' => array(
                'label'       => '1641',
                'description' => '1642',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_sofortbanking_state_refund_partial'       => array(
                'label'       => '1643',
                'description' => '1644',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_sofortbanking_state_refund_full'          => array(
                'label'       => '1645',
                'description' => '1646',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_lable_settings_ideal'                     => array(
                'label' => '4006'
            ),
            'option_ideal_frontend_display'                   => array(
                'label'       => '1613',
                'description' => '1614',
                'store'       => array(
                    array(1, '1615'),
                    array(2, '1616')
                )
            ),
            'option_ideal_key'                                => array(
                'label'       => '1606',
                'description' => '1607'
            ),
            'option_ideal_project_password'                   => array(
                'label'       => '2605',
                'description' => '2605'
            ),
            'option_ideal_notification_password'              => array(
                'label'       => '2607',
                'description' => '2608'
            ),
            'option_ideal_recommended_payment'                => array(
                'label'       => '1619',
                'description' => '1620'
            ),
            'option_lable_settings_ideal_states'              => array(
                'label' => '2632'
            ),
            'option_ideal_state_temporary'                    => array(
                'label'       => '2633',
                'description' => '2634',
                'store'       => array(
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_ideal_state_payment_pending'              => array(
                'label'       => '2635',
                'description' => '2636',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_ideal_state_payment_received'             => array(
                'label'       => '2637',
                'description' => '2638',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_ideal_state_payment_canceled'             => array(
                'label'       => '2639',
                'description' => '2640',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_ideal_state_storno'                       => array(
                'label'       => '2641',
                'description' => '2642',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            ),
            'option_ideal_state_refund_partial'               => array(
                'label'       => '2643',
                'description' => '2644',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )),
            'option_ideal_state_refund_full'                  => array(
                'label'       => '2645',
                'description' => '2646',
                'store'       => array(
                    array(0, "1653"),
                    array(12, "Komplett bezahlt"),
                    array(17, "Offen"),
                    array(20, "Wiedergutschrift"),
                    array(21, "Überprüfung notwendig"),
                    array(35, "Vorgang wurde abgebrochen.")
                )
            )
        );
        //Build Translation array
        $translations = array();
        $translator = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Translator();

        //Build German Translations
        $translations['de_DE'] = array();
        $translator->setLanguage('de_DE');
        foreach ($mapping as $option => $values) {
            $translations['de_DE'][$option] = array();
            if (isset($values['label'])) {
                $translations['de_DE'][$option]['label'] = $translator->getSnippetByNumber($values['label']);
            }
            if (isset($values['text'])) {
                $translations['de_DE'][$option]['text'] = $translator->getSnippetByNumber($values['text']);
            }
            if (isset($values['description'])) {
                $translations['de_DE'][$option]['description'] = $translator->getSnippetByNumber($values['description']);
            }
            if (isset($values['store'])) {
                foreach ($values['store'] as $entries) {
                    if ($entries[1] !== "1653") {
                        $translations['de_DE'][$option]['store'][$entries[0]] = $entries[1];
                    } else {
                        $translations['de_DE'][$option]['store'][$entries[0]] = $translator->getSnippetByNumber($entries[1]);
                    }
                }
            }
        }

        //Build English Translations
        $translations['en_GB'] = array();
        $translator->setLanguage('en_GB');
        foreach ($mapping as $option => $values) {
            $translations['en_GB'][$option] = array();
            if (isset($values['label'])) {
                $translations['en_GB'][$option]['label'] = $translator->getSnippetByNumber($values['label']);
            }
            if (isset($values['text'])) {
                $translations['en_GB'][$option]['text'] = $translator->getSnippetByNumber($values['text']);
            }
            if (isset($values['description'])) {
                $translations['en_GB'][$option]['description'] = $translator->getSnippetByNumber($values['description']);
            }
            if (isset($values['store'])) {
                foreach ($values['store'] as $entries) {
                    if ($entries[1] !== "1653") {
                        $translations['en_GB'][$option]['store'][$entries[0]] = $entries[1];
                    } else {
                        $translations['en_GB'][$option]['store'][$entries[0]] = $translator->getSnippetByNumber($entries[1]);
                    }
                }
            }
        }

        $form = $this->_bootstrap->Form();
        $shopRepository = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Locale');
        foreach ($translations as $locale => $snippets) {
            $localeModel = $shopRepository->findOneBy(array('locale' => $locale));
            foreach ($snippets as $element => $snippet) {
                if ($localeModel === null) {
                    continue;
                }
                $elementModel = $form->getElement($element);
                if ($elementModel === null) {
                    continue;
                }
                $translationModel = new \Shopware\Models\Config\ElementTranslation();
                if (isset($snippet['label'])) {
                    $translationModel->setLabel($snippet['label']);
                }
                if (isset($snippet['description'])) {
                    $translationModel->setDescription($snippet['description']);
                }
                /** @var $localeModel \Shopware\Models\Shop\Locale */
                $translationModel->setLocale($localeModel);
                $elementModel->addTranslation($translationModel);
            }
        }
    }

    /**
     * Translates the payment description
     */
    private function _translatePayments($languages)
    {
        $translator = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Translator();
        $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
        $suKey = $helper->database()->getPaymentKey("sofortbanking");
        $idealKey = $helper->database()->getPaymentKey("sofortideal");
        //Add Translation
        foreach ($languages as $language) {
            $translator->setLanguage($language);
            $suDescription = $translator->getSnippetByNumber("1001");
            $idealDescription = $translator->getSnippetByNumber("2001");
            $translator->addPaymentTranslation($suKey, $suDescription);
            $translator->addPaymentTranslation($idealKey, $idealDescription);
        }
    }

    /**
     * Creates all Admin View Modifications
     *
     * @throws Exception
     */
    private function _applyAdminViewModifications()
    {
        try {
            $parent = $this->_bootstrap->Menu()->findOneBy('label', 'logfile');
            $this->_bootstrap->createMenuItem(array(
                                                   'label'      => 'Sofort AG Log',
                                                   'class'      => 'sprite-cards-stack',
                                                   'active'     => 1,
                                                   'controller' => 'SofortLogView',
                                                   'action'     => 'index',
                                                   'parent'     => $parent
                                              ));

            $parent = $this->_bootstrap->Menu()->findOneBy('label', 'Zahlungen');
            $this->_bootstrap->createMenuItem(array(
                                                   'label'      => 'Sofort AG Orders',
                                                   'class'      => 'sprite-cards-stack',
                                                   'active'     => 1,
                                                   'controller' => 'SofortOrderView',
                                                   'action'     => 'index',
                                                   'parent'     => $parent
                                              ));
        } catch (Exception $exception) {
            throw new Exception("can not create menuentry." . $exception->getMessage());
        }
    }

    /**
     * Calls all soft update methods methods
     * Soft update methods do not require reinstalling of the plugin to affect it
     */
    public function softUpdate()
    {
        $languages = $this->_addTranslationSnippets();
        $this->_translatePayments($languages);
    }
}