<?php

/**
 * Shopware 4
 * Copyright © shopware AG
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */
/**
 * @category Shopware
 * @package Shopware\Plugins\SwagCookiePermission
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */

if (!function_exists('geoip_country_code_by_addr')) {
    require __DIR__ . '/GeoIp/src/geoip.inc';
}

//Include geoip functions when geoip extension is not installed
if(!function_exists('geoip_database_info')) {
    require_once __DIR__ . '/GeoIp/src/geoip.functions.php';
}

class Shopware_Plugins_Frontend_SwagCookiePermission_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    const ASK_LATER_MODE = 1;

    const REMOVE_COOKIES_MODE = 2;

    const EVERYONE = 1;

    const DETECT_BY_IP = 2;

    const DETECT_BY_BROWSER_LANGUAGE = 3;

    const DETECT_BY_SHOP_LANGUAGE = 4;

    /**
     * Returns the plugin label which is displayed in the plugin information and
     * in the Plugin Manager.
     * @return string
     */
    public function getLabel()
    {
        return 'Cookie permission';
    }

    /**
     * Returns the plugin version.
     * @return string
     * @throws Exception
     */
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);

        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    /**
     * Standard plugin install method to register all required components
     * and subscribes the PostDispatch event of the frontend checkout.
     * @throws \Exception
     * @return bool
     */
    public function install()
    {
        $this->createConfigForm();
        $this->createTranslations();
        $this->createTranslationsFrontend();
        $this->createEvents();

        return true;
    }

    /**
     * Subscribes the PostDispatch event of the frontend controller.
     */
    protected function createEvents()
    {
        $this->subscribeEvent('Enlight_Controller_Action_PostDispatch_Frontend', 'onPostDispatchFrontend');
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Widgets_SwagCookiePermission',
            'getWidgetController'
        );
    }

    /**
     * Creates the configuration form for the plugin
     *
     * @return void
     */
    protected function createConfigForm()
    {
        $form = $this->Form();
        //Mode
        $form->setElement(
            'select',
            'swagCookieMode',
            array(
                'label' => 'Mode:',
                'store' => array(
                    array($this->getAskLaterMode(), 'Später fragen (Ask later)'),
                    array($this->getRemoveCookiesMode(), 'Cookies entfernen (Remove cookies)')
                ),
                'required' => true
            )
        );

        //Enable for
        $form->setElement(
            'select',
            'swagCookieAppliesTo',
            array(
                'label' => 'Enable for:',
                'store' => array(
                    array($this->getEveryone(), 'Jeder (Everyone)'),
                    array($this->getDetectByIp(), 'GEO-IP ermitteln (GEO-IP detection)'),
                    array($this->getDetectByBrowserLanguage(), 'Browser Sprache ermitteln (Browser language detection)'),
                    array($this->getDetectByShopLanguage(), 'Shop Sprache ermitteln (Shop language detection)')
                ),
                'required' => true,
                'multiSelect' => true
            )
        );

        //Languages
        $languagesArray = array();
        $localesRepository = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Locale');
        $locales = $localesRepository->findAll();
        $insertedLanguages = array();
        foreach ($locales as $locale) {
            $languageAbbr = substr($locale->getLocale(), 0, 2);
            if (!in_array($languageAbbr, $insertedLanguages)) {
                $languagesArray[] = array($languageAbbr, $locale->getLanguage());
                $insertedLanguages[] = $languageAbbr;
            }
        }

        $form->setElement(
            'select',
            'swagCookieLanguage',
            array(
                'label' => 'Language:',
                'store' => $languagesArray,
                'required' => false,
                'multiSelect' => true
            )
        );

        //Countries
        $countriesArray = array();
        $countriesRepository = Shopware()->Models()->getRepository('\Shopware\Models\Country\Country');
        $countries = $countriesRepository->findAll();
        foreach ($countries as $country) {
            $countriesArray[] = array($country->getIso(), $country->getName());
        }

        $form->setElement(
            'select',
            'swagCookieCountry',
            array(
                'label' => 'Country:',
                'store' => $countriesArray,
                'required' => false,
                'multiSelect' => true
            )
        );

        //Forward to URL
        $form->setElement(
            'text',
            'swagCookieForwardTo',
            array(
                'label' => 'Forward to URL:',
                'required' => false
            )
        );

        //Cookie bar color
        $form->setElement(
            'color',
            'swagCookieColor',
            array(
                'label' => 'Color:',
                'value' => null
            )
        );
    }

    /**
     * Returns config value
     * of ask later mode
     *
     * @return int
     */
    public static function getAskLaterMode()
    {
        return self::ASK_LATER_MODE;
    }

    /**
     * Returns config value
     * of remove cookies mode
     *
     * @return int
     */
    public static function getRemoveCookiesMode()
    {
        return self::REMOVE_COOKIES_MODE;
    }

    /**
     * Config value of the restrict everyone
     *
     * @return int
     */
    public static function getEveryone()
    {
        return self::EVERYONE;
    }

    /**
     * Config value of the detect by ip
     *
     * @return int
     */
    public static function getDetectByIp()
    {
        return self::DETECT_BY_IP;
    }

    /**
     * Config value of the detect by browser language
     *
     * @return int
     */
    public static function getDetectByBrowserLanguage()
    {
        return self::DETECT_BY_BROWSER_LANGUAGE;
    }

    /**
     * Config value of the detect by shop language
     *
     * @return int
     */
    public static function getDetectByShopLanguage()
    {
        return self::DETECT_BY_SHOP_LANGUAGE;
    }

    /**
     * Creates the translations for the
     * configuration form
     *
     * @return void
     */
    public function createTranslations()
    {
        $form = $this->Form();
        $shopRepository = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Locale');

        //contains all translations
        $translations = array(
            'de_DE' => array(
                'swagCookieMode' => array(
                    'label' => 'Modus:',
                ),
                'swagCookieAppliesTo' => array(
                    'label' => 'Aktivieren für:',
                ),
                'swagCookieLanguage' => array(
                    'label' => 'Sprache:',
                ),
                'swagCookieForwardTo' => array(
                   'label' => 'Weiterleiten zu URL:',
                ),
                'swagCookieCountry' => array(
                    'label' => 'Land:',
                ),
                'swagCookieColor' => array(
                    'label' => 'Farbe:',
                )
            )
        );

        // In 4.2.2 we introduced a helper function for this, so we can skip the custom logic
        if ($this->assertMinimumVersion('4.2.2')) {
            $this->addFormTranslations($translations);
        } else {
            $this->translateForm($translations, $form);
        }
    }

    /**
     * translates the plugin configuration form with the given translations
     *
     * @param $translations
     * @param \Shopware\Models\Config\Form $form
     */
    private function translateForm($translations, $form)
    {
        // Translations
        $shopRepository = Shopware()->Models()->getRepository('\Shopware\Models\Shop\Locale');

        //iterate the languages
        foreach($translations as $locale => $snippets) {
            $localeModel = $shopRepository->findOneBy(array('locale' => $locale));

            //not found? continue with next language
            if($localeModel === null) {
                continue;
            }

            if($snippets['plugin_form']) {
                // Translation for form description
                $formTranslation = null;
                /* @var \Shopware\Models\Config\FormTranslation $translation */
                foreach($form->getTranslations() as $translation) {
                    if($translation->getLocale()->getLocale() == $locale) {
                        $formTranslation = $translation;
                    }
                }

                // If none found create a new one
                if(!$formTranslation) {
                    $formTranslation = new \Shopware\Models\Config\FormTranslation();
                    $formTranslation->setLocale($localeModel);
                    //add the translation to the form
                    $form->addTranslation($formTranslation);
                }

                if($snippets['plugin_form']['label']) {
                    $formTranslation->setLabel($snippets['plugin_form']['label']);
                }

                if($snippets['plugin_form']['description']) {
                    $formTranslation->setDescription($snippets['plugin_form']['description']);
                }

                unset($snippets['plugin_form']);
            }

            //iterate all snippets of the current language
            foreach($snippets as $element => $snippet) {
                $translationModel = null;
                //get the form element by name
                $elementModel = $form->getElement($element);

                //not found? continue with next snippet
                if($elementModel === null) {
                    continue;
                }

                // Try to load existing translation
                foreach($elementModel->getTranslations() as $translation) {
                    if($translation->getLocale()->getLocale() == $locale) {
                        $translationModel = $translation;
                        break;
                    }
                }

                // If none found create a new one
                if(!$translationModel) {
                    $translationModel = new \Shopware\Models\Config\ElementTranslation();
                    $translationModel->setLocale($localeModel);
                    //add the translation to the form element
                    $elementModel->addTranslation($translationModel);
                }

                if($snippet['label']) {
                    $translationModel->setLabel($snippet['label']);
                }

                if($snippet['description']) {
                    $translationModel->setDescription($snippet['description']);
                }
            }
        }
    }

    /**
     * Creates the german translations
     * for the frontend template
     *
     * @return void
     */
    public function createTranslationsFrontend()
    {
        // Getting localeId for german language
        $sql = "SELECT id FROM s_core_locales WHERE locale = 'de_DE'";
        $localeId = Shopware()->Db()->fetchOne($sql);

        $sql = "
            INSERT IGNORE INTO `s_core_snippets` (`namespace`, `shopID`, `localeID`, `name`, `value`, `created`, `updated`)
            VALUES
            (:namespace, :shopId, :localeId, 'cookieBarMessage', 'Diese Seite erfordert Cookies. Sind Sie mit der Verwendung von Cookies einverstanden?', :now, :now),
            (:namespace, :shopId, :localeId, 'CookieBarNoBtn', 'Nein', :now, :now),
            (:namespace, :shopId, :localeId, 'CookieBarYesBtn', 'Ja', :now, :now)
        ";

        Shopware()->Db()->query($sql, array(
                'namespace' => 'engine/Shopware/Plugins/Local/Frontend/SwagCookiePermission/Views/frontend/plugins/swag_cookie_permission/index',
                'shopId' => 1,
                'localeId' => $localeId,
                'now' => date('Y-m-d H:i:s')
            )
        );
    }

    /**
     * Returns the path to the backend controller.
     *
     * @return string
     */
    public function getWidgetController(Enlight_Event_EventArgs $arguments)
    {
        return $this->Path() . '/Controllers/Widgets/SwagCookiePermission.php';
    }

    /**
     * Event listener function which called over the Enlight_Controller_Action_PostDispatch_Frontend event.
     *
     * @param Enlight_Event_EventArgs $arguments
     */
    public function onPostDispatchFrontend(Enlight_Event_EventArgs $arguments)
    {
        $controller = $arguments->getSubject();
        $view = $controller->View();

        $cookieMode = $this->Config()->get('swagCookieMode');

        $view->addTemplateDir($this->Path() . 'Views/');
        $view->extendsTemplate('frontend/plugins/swag_cookie_permission/index.tpl');

        if ($controller->Request()->getCookie('allowCookie') != 1) {
            $result = $this->isAffectedUser();
            if ($result === true && $this->Config()->get('swagCookieMode') == $this->getRemoveCookiesMode()) {
                //delete cookies
                header_remove('Set-Cookie');
                $cookies = $controller->Request()->getCookie();
                foreach ($cookies as $key => $value) {
                    $controller->Response()->setCookie($key, null, 0);
                }
            }
        }

        $config = array(
            'cookieMode' => $cookieMode,
            'appliesTo' => $this->Config()->get('swagCookieAppliesTo'),
            'forwardTo' => $this->Config()->get('swagCookieForwardTo'),
            'backgroundColor' => $this->Config()->get('swagCookieColor'),
            'isRemoveCookies' => ($cookieMode == $this->getRemoveCookiesMode()) ? true : false,
        );

        $view->swagCookieBarConfig = $config;
    }

    /**
     * Helper function to check if the user should be warned.
     *
     * @return boolean
     */
    public function isAffectedUser()
    {
        $userAgent = Shopware()->Front()->Request()->getServer('HTTP_USER_AGENT');
        // Return false if the user is a bot
        $crawler = $this->detectBot($userAgent);
        if ($crawler) {
            return false;
        }

        $request = Shopware()->Front()->Request();
        //there may be more than one restriction type
        $appliesToArray = $this->Config()->get('swagCookieAppliesTo');
        foreach ($appliesToArray as $appliesTo) {
            if ($appliesTo == $this->getEveryone()) {
                return true;
            } elseif ($appliesTo == $this->getDetectByBrowserLanguage()) {
                $affectedLanguagesArray = $this->Config()->get('swagCookieLanguage');
                foreach ($affectedLanguagesArray as $language) {
                    if ($language == substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) {
                        return true;
                    }
                }
            } elseif ($appliesTo == $this->getDetectByIp()) {
                $userIp = $request->getClientIp();
                $ipLocation = $this->getCountry($userIp);
                $affectedCountriesArray = $this->Config()->get('swagCookieCountry');
                foreach ($affectedCountriesArray as $country) {
                    if ($country == $ipLocation) {
                        return true;
                    }
                }
            } elseif ($appliesTo == $this->getDetectByShopLanguage()) {
                $affectedLanguagesArray = $this->Config()->get('swagCookieLanguage');
                $shopLocale = Shopware()->Shop()->getLocale();
                foreach ($affectedLanguagesArray as $language) {
                    if ($language == substr($shopLocale->getLocale(), 0, 2)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Detects if the connected socket is a search engine bot or a regular user
     * @param $USER_AGENT
     * @return bool
     */
    function detectBot($USER_AGENT) {
        $crawlers_agents = strtolower(Shopware()->Config()->get("botBlacklist")); //To lower to avoid camel-case issues!
        $crawlers = explode(";", $crawlers_agents); //Split to array by char ';'
        if(is_array($crawlers)) {
        foreach($crawlers as $crawler) {
            if (strpos(strtolower($USER_AGENT), trim($crawler)) !== false) {
                return true;
            }
        }
    }
        return false;
    }
    /**
     * Helper function to get country
     * by GEO-IP detection API
     *
     * @param string $ip
     * @return string
     */
    protected function getCountry($ip)
    {
        $gi = geoip_open(__DIR__ . '/GeoIp/db/GeoIP.dat', GEOIP_STANDARD);
        $response = geoip_country_code_by_addr($gi, $ip);
        geoip_close($gi);

        return $response;
    }

}
