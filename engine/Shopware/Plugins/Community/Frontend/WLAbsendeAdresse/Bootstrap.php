<?php

use Doctrine\ORM\Query;
use Shopware\CustomModels\Order\UserSenderAddress;
use Shopware\CustomModels\Order\OrderSenderAddress;

class Shopware_Plugins_Frontend_WLAbsendeAdresse_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    public function getLabel()
    {
        return 'WL Absende Adresse';
    }
	
	public function getVersion()
    {
        return '0.9.1';
    }

	/**
	 * Register our custom models after initialisation.
	 *
	 * @return void
	 */

	public function afterInit()
	{
		// register our models
		$this->registerCustomModels();
	}
	
	/**
    * Gibt die gesammelten Plugin-Informationen zurück
    *
    */
    public function getInfo() {
        return array(
            // Die Plugin-Version.
            'version' => $this->getVersion(),
            // Copyright-Hinweis
            'copyright' => 'Copyright (c) 2016, WL Service GmbH',
			// Hersteller-Seite
			'supplier' => 'WL Service GmbH',
			// Hersteller-Seite
			'author' => 'WL Service GmbH',
            // Lesbarer Name des Plugins
            'label' => $this->getLabel(),
            // Anlaufstelle für den Support
            'support' => 'support@wistundlaumann.de',
            // Hersteller-Seite
            'link' => 'https://www.wistundlaumann.de',
            // Aktuelle Revision des Plugins
            'revision' => '1'
        );
    }

    // installieren, models erzeugen, views erzeugen, an events haengen
    public function install()
    {
       
    	$this->registerCustomModels();
    	
    	$modelManager = Shopware()->Models();
    	
    	try{
	    	$schemaTool = new Doctrine\ORM\Tools\SchemaTool($modelManager);
	    	$schemaTool->createSchema(
	    			array(
	    					$modelManager->getClassMetadata('Shopware\CustomModels\Order\UserSenderAddress')
	    			)
	    	);
    	} catch (Exception $e) {
		    // besteht schon
		}
		
		try{
			$schemaTool = new Doctrine\ORM\Tools\SchemaTool($modelManager);
			$schemaTool->createSchema(
					array(
							$modelManager->getClassMetadata('Shopware\CustomModels\Order\OrderSenderAddress')
					)
			);
		} catch (Exception $e) {
			//mail('s.vgroenheim@wistundlaumann.de','Test',$e->getMessage());
			// besteht schon
		}
		
		
		
		try{

			/*
			 * 	daten einfuegen / setup - nothing toDO yet
			 *
				$insert = "INSERT INTO ...";
				Shopware()->Db()->query($insert);
			*/
		} catch (Exception $e) {
			// besteht schon
		}

    	
    	$this->registerController('Backend', 'Senderaddress');
		$this->registerController('Frontend', 'Senderaddress');


		// Frontend Events
		$this->subscribeEvent(
			'Enlight_Controller_Action_PostDispatch_Frontend',
			'registerTemplates'
		);

		$this->subscribeEvent(
			'Theme_Compiler_Collect_Plugin_Less',
			'addLessFiles'
		);

		$this->subscribeEvent(
			'Theme_Compiler_Collect_Plugin_Javascript',
			'addJsFiles'
		);

		$this->subscribeEvent(
			'Enlight_Controller_Action_PostDispatch_Frontend_Checkout',
			'onPostDispatchCheckout'
		);

		$this->subscribeEvent(
			'Enlight_Controller_Action_PostDispatch_Frontend_Account',
			'onPostDispatchCheckout'
		);

		$this->subscribeEvent(
			'sOrder::sSaveOrder::after',
			'afterOrderCreation'
		);


		// Backend Events
		$this->subscribeEvent(
			'Enlight_Controller_Action_PostDispatch_Backend_Order',
			'onOrderPostDispatch'
		);
		
		// Dokumente
		$this->subscribeEvent(
			'Shopware_Components_Document::assignValues::after',
			'onAssignValues'
		);


		return array('success' => true, 'invalidateCache' => array('frontend', 'backend'));

    }

	/**
	 * Register the custom model dir
	 */
	protected function registerCustomModels()
	{
		$this->Application()->Loader()->registerNamespace(
			'Shopware\CustomModels',
			$this->Path() . 'Models/'
		);
		$this->Application()->ModelAnnotations()->addPaths(array(
			$this->Path() . 'Models/'
		));
	}
    
    // deinstallieren, models nicht entfernen
    public function uninstall()
    {
    	 
    	$this->registerCustomModels();
    	
    	
    	$modelManager = Shopware()->Models();

    	return true;
    }


	public function registerTemplates(Enlight_Event_EventArgs $args) {

		$controller = $args->getSubject();
		$view = $controller->View();
		$view->addTemplateDir($this->Path() . 'Views/');

	}



	/**
	 * CRUD function of the document store. The function creates the order document with the passed
	 * request parameters.
	 */
	public function onAssignValues(Enlight_Event_EventArgs $args)
	{
		$subject = $args->getSubject();

		$variables = $subject->_view->getTemplateVars();

		//mail('s.vgroenheim@wistundlaumann.de','OnAssignValues', print_r($args,TRUE) );
		$db = Shopware()->Db();
		$orderId = $this->getOrderIdByNumber($db,$variables['Order']['_order']['ordernumber']);
		$senderaddress = $this->getDataById($orderId);

		$subject->_view->assign('Senderaddress', $senderaddress);
	}



	public function addLessFiles(Enlight_Event_EventArgs $args)
	{
		$less = new \Shopware\Components\Theme\LessDefinition(
		//configuration
			array(),
			//less files to compile
			array(
				__DIR__ . '/Views/frontend/_resources/css/styles.less'
			),

			//import directory
			__DIR__
		);

		return new Doctrine\Common\Collections\ArrayCollection(array($less));
	}

	public function addJsFiles(Enlight_Event_EventArgs $args)
	{
		$jsFiles = array(
			__DIR__ . '/Views/frontend/_resources/js/as.js'
		);
		return new Doctrine\Common\Collections\ArrayCollection($jsFiles);
	}

	public function onPostDispatchCheckout(Enlight_Event_EventArgs $args) {

		$controller = $args->getSubject();
		$view = $controller->View();

		try {
			$db = Shopware()->Db();
			// Session auslesen
			$session = Shopware()->SessionID();
			$userID = Shopware()->Session()->sUserId;
			$em = Shopware()->Models();
			if($userID == '' || $userID == null || $userID == 0){
				// Benutzer ist noch nicht da
				return;
			}


			$sql = "SELECT senderAdressID FROM a_wluser_senderaddress WHERE userID=?";
			$userSenderAddresses = $db->fetchOne($sql, array($userID));
			$billingSame = false;


			// die Standard Rechnungsaddresse, falls der Benutzer noch keinen Eintrag hat, evtl. auch resetten nach Order immer?
			$sql = "SELECT default_billing_address_id FROM s_user WHERE s_user.id=?";
			$addressID = $db->fetchOne($sql, array($userID));
			if(empty($userSenderAddresses) || $userSenderAddresses == 0 || $userSenderAddresses == null){

				$billingSame = true;

			} else {

				if($addressID == $userSenderAddresses){
					$billingSame = true;
				}
				$addressID = $userSenderAddresses;

			}

			$query = $em->createQuery("SELECT address, attr, country, state FROM Shopware\Models\Customer\Address address LEFT JOIN address.attribute attr LEFT JOIN address.country country LEFT JOIN address.state state WHERE address.id = " . $addressID);
			$senderAddress = $query->getResult ( Query::HYDRATE_ARRAY );

			$view->assign('senderAddress', $senderAddress[0]);
			$view->assign('billingSame', $billingSame);


		} catch (Exception $e) {
			mail('s.vgroenheim@wistundlaumann.de','Test',$e->getMessage());
			// besteht schon
		}
	}


	public function onOrderPostDispatch(Enlight_Event_EventArgs $args)
	{
		/** @var \Enlight_Controller_Action $controller */
		$controller = $args->getSubject();
		$view = $controller->View();
		$request = $controller->Request();

		$view->addTemplateDir(__DIR__ . '/Views');

		if ($request->getActionName() === 'index') {
			$view->extendsTemplate('backend/order/view/app.js');
		}

		if ($request->getActionName() === 'load') {
			$view->extendsTemplate('backend/order/view/detail/senderdetail.js');
			$view->extendsTemplate('backend/order/view/detail/senderoverview.js');
		}

	}


	public function afterOrderCreation(Enlight_Hook_HookArgs $args)
	{
		$db =  Shopware()->Db();
		$em = Shopware()->Models();
		$userID = Shopware()->Session()->sUserId;
		$orderNumber = $args->getReturn();
		$orderId = $this->getOrderIdByNumber($db, $orderNumber);

		$sql = "SELECT senderAdressID FROM a_wluser_senderaddress WHERE userID=?";
		$userSenderAddresses = $db->fetchOne($sql, array($userID));


		$sql = "SELECT default_billing_address_id FROM s_user WHERE s_user.id=?";
		$addressID = $db->fetchOne($sql, array($userID));
		if(empty($userSenderAddresses) || $userSenderAddresses == 0 || $userSenderAddresses == null){

			// nothing to do imo

		} else {

			$addressID = $userSenderAddresses;

		}

		$query = $em->createQuery("SELECT address, attr, country, state FROM Shopware\Models\Customer\Address address LEFT JOIN address.attribute attr LEFT JOIN address.country country LEFT JOIN address.state state WHERE address.id = " . $addressID);
		$senderAddress = $query->getResult ( Query::HYDRATE_OBJECT)[0];

		if($senderAddress->getCountry() != null){
			$country = $senderAddress->getCountry()->getId();
		} else {
			$country = 'NULL';
		}

		if($senderAddress->getState() != null){
			$state = $senderAddress->getState()->getId();
		} else{
			$state = 'NULL';
		}

		try {
			$company = $senderAddress->getCompany();
		} catch (Exception $e) {
			$company = '';
		}

		try {
			$department = $senderAddress->getDepartment();
		} catch (Exception $e) {
			$department = '';
		}

		try {
			$salutation = $senderAddress->getSalutation();
		} catch (Exception $e) {
			$salutation = '';
		}

		try {
			$firstname = $senderAddress->getFirstname();
		} catch (Exception $e) {
			$firstname = '';
		}

		try {
			$lastname = $senderAddress->getLastname();
		} catch (Exception $e) {
			$lastname = '';
		}

		try {
			$street = $senderAddress->getStreet();
		} catch (Exception $e) {
			$street = '';
		}

		try {
			$zipcode = $senderAddress->getZipcode();
		} catch (Exception $e) {
			$zipcode = '';
		}

		try {
			$city = $senderAddress->getCity();
		} catch (Exception $e) {
			$city = '';
		}

		try {
			$additional_address_line1 = $senderAddress->getAdditionalAddressLine1();
		} catch (Exception $e) {
			$additional_address_line1 = '';
		}

		try {
			$additional_address_line2 = $senderAddress->getAdditionalAddressLine2();
		} catch (Exception $e) {
			$additional_address_line2 = '';
		}

		$sql = 'INSERT INTO ';
		$sql .= "`a_wlorder_senderaddress` ";
		$sql .= "(`orderID`, `countryID`, `stateID`, `userID`, `company`, `department`, `salutation`, `firstname`, `lastname`, `street`, `zipcode`, `city`, `additional_address_line1`, `additional_address_line2`) VALUES ";
		$sql .= "(" . $orderId. ", '" .$country.  "', '". $state . "', ". $userID .", '" . $company . "', '" . $department . "', '" . $salutation . "', '" . $firstname . "', '" . $lastname . "', '" . $street . "', '" . $zipcode . "', '" . $city . "', '" . $additional_address_line1 . "', '" . $additional_address_line2 . "') ";
		$db->query($sql);
		return $orderNumber;
	}

	private function getOrderIdByNumber($db, $number) {
		$sql = "SELECT id FROM s_order WHERE ordernumber=?";
		return $db->fetchOne($sql, array($number));
	}


	public function getDataById($orderId){
		$em = Shopware()->Models();
		$em->getClassMetadata('Shopware\CustomModels\Order\OrderSenderAddress');
		$sql = "SELECT address, ord, state, country FROM Shopware\CustomModels\Order\OrderSenderAddress address ";
		$sql .= " LEFT JOIN address.order ord ";
		$sql .= " LEFT JOIN address.state state ";
		$sql .= " LEFT JOIN address.country country ";
		$sql .= " WHERE address.order = " . $orderId;
		$query = $em->createQuery($sql);
		return $query->getResult ( Query::HYDRATE_ARRAY )[0];
	}

}
