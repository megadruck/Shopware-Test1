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
class Shopware_Plugins_Frontend_SofortPayment_Bootstrap
    extends Shopware_Components_Plugin_Bootstrap
{

	private $label = array(
		'de_DE' => 'SOFORT AG Shopware Module',
		'en_GB' => 'SOFORT AG Shopware Modules',
		'es_ES' => 'SOFORT AG Shopware Module',
		'fr_FR' => 'SOFORT Shopware Module',
		'it_IT' => 'SOFORT AG modulo Shopware',
		'nl_NL' => 'SOFORT shopware module',
		'pl_PL' => 'Moduł SOFORT AG Shopware'
	);

	/**
	 * Returns the correct Label for the given language
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return array_key_exists(Shopware()->Instance()->locale()->toString(), $this->label)?$this->label[Shopware()->Instance()->locale()->toString()]:$this->label['en_GB'];
	}

	/**
     * Get Info for the Pluginmanager
     *
     * @return array
     */
    public function getInfo()
    {

        return array(
            'version'     => $this->getVersion(),
            'author'      => "PayIntelligent GmbH",
            'source'      => $this->getSource(),
            'support'     => "http://www.sofort.com/",
            'link'        => "http://www.payintelligent.de",
            'copyright'   => "Copyright (c) 2013, SOFORT AG",
            'label'       => $this->getLabel(),
            'description' => ''
        );
    }

    /**
     * Returns the plugin version
     *
     * @return String
     */
    public function getVersion()
    {
        return "2.3.1";
    }

    /**
     * Shopware method automatically getting called for plugin Installation
     *
     * @return boolean Indicator of success
     */
    public function install()
    {
        $result = false;
        if (parent::install()) {
            $installer = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Setup($this);
            $installer->install();
            $result = true;
        }

        return $result;
    }

    /**
     * Updates the plugin
     *
     * @param string $oldVersion
     *
     * @return bool
     */
    public function update($oldVersion)
    {
        try {
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            switch ($oldVersion) {
                case "2.0.0":
                case "2.0.1":
                    $helper->database()->updateBasketTable();
                case "2.0.2":
                case "2.0.3":
                case "2.0.4":
                    $helper->database()->updatePaymentDescription();
				case "2.0.5":
				case "2.1.0":
				case "2.2.0":
				default:
					$this->uninstall();
					$this->install();
            }
        } catch (Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * Shopware method automatically getting called for plugin Uninstallation
     *
     * @return boolean Indicator of success
     */
    public function uninstall()
    {
        $result = false;
        if (parent::uninstall()) {
            $installer = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Setup($this);
            $installer->uninstall();
            $result = true;
        }

        return array('success' => $result, 'invalidateCache' => array('backend'));
    }

    /**
     * Shopware method automatically getting called for plugin Activation
     *
     * @return boolean Indicator of success
     */
    public function enable()
    {
        if (parent::enable()) {
            try {
                $installer = new Shopware_Plugins_Frontend_SofortPayment_Components_Services_Setup($this);
                $installer->softUpdate();

                return true;
            } catch (Exception $exception) {
                return false;
            }
        }

        return false;
    }

    /**
     * Eventhandler for checkout confirmation.
     * Displays any error messages stored in the session
     *
     * @param Enlight_Event_EventArgs $arguments
     */
    public function onCheckoutConfirm(Enlight_Event_EventArgs $arguments)
    {
        $params = $arguments->getRequest()->getParams();

        if ($arguments->getRequest()->getControllerName() !== 'account' ||
            $arguments->getRequest()->getActionName() !== 'payment' || $params["errorMessage"] != 1
        ) {
            return;
        } else {
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $view = $arguments->getSubject()->View();
            $helper->event()->displayErrors($view);
        }
    }

    /**
     * Handles payment logos and payment title in Frontend
     *
     * @param Enlight_Event_EventArgs $arguments
     */
    public function onPostDispatch(Enlight_Event_EventArgs $arguments)
    {
        $request = $arguments->getSubject()->Request();
        $response = $arguments->getSubject()->Response();
        $view = $arguments->getSubject()->View();
        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'frontend' || !$view->hasTemplate()) {
            return;
        }

        $user = Shopware()->Session()->sOrderVariables['sUserData'];
        if ($user['additional']['payment']['name'] === 'sofortideal') {
            $customerId = $user['additional']['user']['id'];
            $model = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')
                     ->findOneById($customerId);
            if (!function_exists($model->getAttribute()->getSofortIdealBank)) {
                $view->sRegisterFinished = 'false';
                $sql = "INSERT IGNORE INTO `s_user_attributes` (`userID`) VALUES (?), (?);";
                Shopware()->Db()->query($sql, array($customerId, $customerId));
            }
        }

        if ($request->getControllerName() == 'account'
            || $request->getControllerName() == 'register'
            || $request->getControllerName() == 'checkout'
        ) {
            $view->addTemplateDir( $this->Path() . 'Views/common/' );
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $helper->event()->designPaymentMeanSelection($arguments, $this);
        }
    }

    /**
     * Handles bank selection for ideal payments
     *
     * @param Enlight_Hook_HookArgs $arguments
     */
    public function onSavePayment(Enlight_Hook_HookArgs $arguments)
    {
        $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
        $params = Shopware()->Front()->Request()->getParams();
        $ideal = $this->Payments()->findOneBy(array('name'=> 'sofortideal'));

        if(!isset($params['payment'])){
            $paymentId = $params['register']['payment'];
        }

        if ($ideal->getId() == $paymentId && isset($params['sofort_ideal_bank_select'])) {
            $helper->event()->saveBankId($params['sofort_ideal_bank_select'], Shopware()->Session()->offsetGet('sUserId'));
        }
    }

    /**
     * Eventhandler for the Payment Mean 'Sofortdebit'
     *
     * @param Enlight_Event_EventArgs $arguments
     *
     * @return string
     */
    public function registerSofortbankingController(Enlight_Event_EventArgs $arguments)
    {
        Shopware()->Template()->addTemplateDir(Shopware()->Plugins()->Frontend()->SofortPayment()->Path()
                                               . 'Views/');

        return $this->Path() . 'Controller/Frontend/PaymentSofortbanking.php';
    }

    /**
     * Eventhandler for the Payment Mean 'iDeal'
     *
     * @param Enlight_Event_EventArgs $arguments
     *
     * @return string
     */
    public function registerIdealController(Enlight_Event_EventArgs $arguments)
    {
        Shopware()->Template()->addTemplateDir(Shopware()->Plugins()->Frontend()->SofortPayment()->Path()
                                               . 'Views/');

        return $this->Path() . 'Controller/Frontend/PaymentIdeal.php';
    }

    /**
     * Eventhandler disabling the status mailing for sofort payments
     *
     * @param Enlight_Event_EventArgs $arguments
     *
     * @return boolean|void
     */
    public function disableStatusMails(Enlight_Event_EventArgs $arguments)
    {
        $variables = $arguments->get('variables');
        $paymentName = $variables['additional']['payment']['name'];
        if ($paymentName === 'sofortideal' || $paymentName === 'sofortbanking') {
            if (Shopware()->Session()->sofortSendMail === false) {
                Shopware()->Session()->offsetSet('sofortMailVariables', serialize($variables));
                return true;
            }
        }
    }

    /**
     * Eventhandler for the admin view modification
     *
     * @param Enlight_Event_EventArgs $arguments
     *
     * @return string
     */
    public function registerAdminViewLogController(Enlight_Event_EventArgs $arguments)
    {
        Shopware()->Template()->addTemplateDir(Shopware()->Plugins()->Frontend()->SofortPayment()->Path()
                                               . 'Views/');

        return $this->Path() . 'Controller/Backend/SofortLogView.php';
    }

    /**
     * Eventhandler for the admin view modification
     *
     * @param Enlight_Event_EventArgs $arguments
     *
     * @return string
     */
    public function registerAdminViewOrderController(Enlight_Event_EventArgs $arguments)
    {
        Shopware()->Template()->addTemplateDir(Shopware()->Plugins()->Frontend()->SofortPayment()->Path()
                                               . 'Views/');

        return $this->Path() . 'Controller/Backend/SofortOrderView.php';
    }

    /**
    * Provide the file collection for js files
    *
    * @param Enlight_Event_EventArgs $args
    * @return \Doctrine\Common\Collections\ArrayCollection
    */
   public function addJsFiles(Enlight_Event_EventArgs $args)
   {
       $jsFiles = array($this->Path() . '/Views/common/frontend/_public/src/js/sofortheader.js');
       return new Doctrine\Common\Collections\ArrayCollection($jsFiles);
   }


}
