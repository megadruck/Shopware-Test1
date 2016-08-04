<?php

use Doctrine\ORM\Query;
use Shopware\CustomModels\Order\UserSenderAddress;
use Shopware\CustomModels\Order\OrderSenderAddress;

class Shopware_Plugins_Frontend_WLAbsendeAdresse_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

	private $config_fields = array(
			'vorname' => array(
			'label' => 'Absendeadresse Vorname',
			'value' => '',
			'description' => 'Absendeadresse Vorname',
			'type' => 'text',
			'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
		),
		'nachname' => array(
			'label' => 'Absendeadresse Nachname',
			'value' => 'Megadruck',
			'description' => 'Absendeadresse Nachname',
			'type' => 'text',
			'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
		),
		'strasse' => array(
			'label' => 'Absendeadresse Strasse',
			'value' => 'Eichendorffstraße 34',
			'description' => 'Absendeadresse Strasse',
			'type' => 'text',
			'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
		),
		'plz' => array(
			'label' => 'Absendeadresse PLZ',
			'value' => '26655 ',
			'description' => 'Absendeadresse PLZ',
			'type' => 'text',
			'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
		),
		'stadt' => array(
			'label' => 'Absendeadresse Stadt',
			'value' => 'Westerstede',
			'description' => 'Absendeadresse Stadt',
			'type' => 'text',
			'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
		)
	);

    public function getLabel()
    {
        return 'WL Absender Adresse';
    }
	
	public function getVersion()
    {
        return '1.0.1';
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
			'Enlight_Controller_Action_PostDispatch_Backend_Customer',
			'onCustomerPostDispatch'
		);

		$this->subscribeEvent(
			'Enlight_Controller_Action_PostDispatch_Backend_Order',
			'onOrderPostDispatch'
		);

		$this->subscribeEvent(
			'Shopware_Controllers_Backend_Customer::getDetailAction::after',
			'onGetDetailBackendCustomer'
		);

		$this->subscribeEvent(
			'Shopware_Controllers_Backend_Address::save::before',
			'onUpdateAddress'
		);

		$this->subscribeEvent(
			'Shopware_Controllers_Backend_Address::getList::after',
			'onAddressGetList'
		);

		// Dokumente
		$this->subscribeEvent(
			'Shopware_Components_Document::assignValues::after',
			'onAssignValues'
		);


		$this->subscribeEvent(
			'Enlight_Controller_Dispatcher_ControllerPath_Api_ASOrder',
			'onOrderAPIController'
		);


		$this->subscribeEvent(
			'Enlight_Controller_Front_StartDispatch',
			'onEnlightControllerFrontStartDispatch'
		);

		// Standard-Absender-Daten setzen
		$this->createConfigForm();

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


	public function createConfigForm(){
		$form = $this->Form();
		foreach($this->config_fields as $field_name=>$field){
			$form->setElement($field['type'], $field_name, array(
					'label' => $field['label'],
					'value' => $field['value'],
					'description' => $field['description'],
					'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
				)
			);
		}
		$form->setElement('combo', 'bundesland', array(
			'label'=>'Absendeadresse Bundesland','value'=> 2,
			'store' => 'base.CountryState'
			, 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
		$form->setElement('combo', 'land', array(
			'label'=>'Absendeadresse Land','value'=> 2,
			'store' => 'base.Country'
			, 'scope' => \Shopware\Models\Config\Element::SCOPE_SHOP));
	}

	/**
	 * @return string
	 */
	public function onOrderAPIController()
	{
		return $this->Path() . 'Controllers/Api/ASOrder.php';
	}

	/**
	 * @param Enlight_Event_EventArgs $args
	 */
	public function onEnlightControllerFrontStartDispatch(Enlight_Event_EventArgs $args)
	{
		$this->Application()->Loader()->registerNamespace(
			'Shopware\Components',
			$this->Path() . 'Components/'
		);
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

				$sConfigs = Shopware()->Plugins()->Frontend()->WLAbsendeAdresse()->Config();
				$vorname = $sConfigs->vorname;
				$nachname = $sConfigs->nachname;
				$strasse = $sConfigs->strasse;
				$plz = $sConfigs->plz;
				$stadt = $sConfigs->stadt;

				$bundesland = $sConfigs->bundesland;
				$land = $sConfigs->land;

				$query = $em->createQuery("SELECT state FROM Shopware\Models\Country\State state WHERE state.id = " . $bundesland);
				$state = $query->getResult ( Query::HYDRATE_ARRAY );
				if(!empty($state)){
					$state = $state[0];
				}


				$query = $em->createQuery("SELECT country FROM Shopware\Models\Country\Country country WHERE country.id = " . $land);
				$country = $query->getResult ( Query::HYDRATE_ARRAY );
				if(!empty($country)){
					$country = $country[0];
				}


				$senderAddress = array(
					'company' => '',
					'firstname' => $vorname,
					'lastname' => $nachname,
					'street' => $strasse,
					'zipcode' => $plz,
					'city' => $stadt,
					'state' => $state,
					'country' => $country,
					'land' => $land,
					'bundesland' => $bundesland

				);

				$view->assign('senderAddress', $senderAddress);
				// nicht aenderbar obv
				$view->assign('billingSame', true);

			} else {

				if($addressID == $userSenderAddresses){
					$billingSame = true;
				}
				$addressID = $userSenderAddresses;

				$query = $em->createQuery("SELECT address, attr, country, state FROM Shopware\Models\Customer\Address address LEFT JOIN address.attribute attr LEFT JOIN address.country country LEFT JOIN address.state state WHERE address.id = " . $addressID);
				$senderAddress = $query->getResult ( Query::HYDRATE_ARRAY );

				$view->assign('senderAddress', $senderAddress[0]);
				$view->assign('billingSame', $billingSame);

			}




		} catch (Exception $e) {
			mail('s.vgroenheim@wistundlaumann.de','Test',$e->getMessage());
			// besteht schon
		}
	}


	public function onCustomerPostDispatch(Enlight_Event_EventArgs $args)
	{
		/** @var \Enlight_Controller_Action $controller */
		$controller = $args->getSubject();
		$view = $controller->View();
		$request = $controller->Request();

		$view->addTemplateDir(__DIR__ . '/Views');

		if ($request->getActionName() === 'index') {
			$view->extendsTemplate('backend/customer/app.js');
		}

		if ($request->getActionName() === 'load') {
			$view->extendsTemplate('backend/customer/view/detail/senderoverview.js');
			$view->extendsTemplate('backend/customer/view/model/addressmodel.js');
			$view->extendsTemplate('backend/customer/view/address/senderaddress.js');
			$view->extendsTemplate('backend/customer/view/address/senderlist.js');
			//$view->extendsTemplate('backend/customer/view/address/senderlist2.js');
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


	public function onGetDetailBackendCustomer(Enlight_Event_EventArgs $args)
	{
		$db =  Shopware()->Db();
		$em = Shopware()->Models();
		$subject = $args->getSubject();
		$request = $subject->Request();
		$customerID = $request->getParam('customerID');
		$view = $subject->View();
		$data = $view->data;
		$data['default_sender_address_id'] = $this->getSenderAddressId($db, $customerID);

		$view->assign('data', $data);
	}

	public function onUpdateAddress(Enlight_Hook_HookArgs $arguments)
	{
		$db =  Shopware()->Db();
		$em = Shopware()->Models();
		$subject = $arguments->getSubject();
		$request = $subject->Request();

		$data = $arguments->get('data');


		if (!empty($data['setDefaultSenderAddress'])) {
			if($data['setDefaultSenderAddress'] == true){
				$db->query('UPDATE a_wluser_senderaddress SET senderAdressID = ' .  $data['id'] . ' WHERE userID = ' . $data['customer'][0]['id']);
			}
		}
	}


	public function onAddressGetList(Enlight_Event_EventArgs $args)
	{
		//$db =  Shopware()->Db();
		//$em = Shopware()->Models();
		//$queryBuilder = $args->getReturn();
		//$queryBuilder->leftJoin('Shopware\CustomModels\Order\UserSenderAddress', 'usa', 'WITH', 'usa.senderAdressID = address.id');
		//$queryBuilder->addSelect(['usa']);
		//$args->setReturn($queryBuilder);

		$db =  Shopware()->Db();
		$em = Shopware()->Models();
		$result = $args->getReturn();

		foreach ($result['data'] as &$data) {
			$data['customer']['default_sender_address_id'] = $this->getSenderAddressId($db, $data['customer']['id']);
		}
		$args->setReturn($result);



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

		$addressID = 0;

		// keine vorhanden
		if(empty($userSenderAddresses) || $userSenderAddresses == 0 || $userSenderAddresses == null){

			$sConfigs = Shopware()->Plugins()->Frontend()->WLAbsendeAdresse()->Config();
			$vorname = $sConfigs->vorname;
			$nachname = $sConfigs->nachname;
			$strasse = $sConfigs->strasse;
			$plz = $sConfigs->plz;
			$stadt = $sConfigs->stadt;

			$bundesland = $sConfigs->bundesland;
			$land = $sConfigs->land;

			if($land != null){
				$country = $land;
			} else {
				$country = 'NULL';
			}

			if($bundesland != null){
				$state = $bundesland;
			} else{
				$state = 'NULL';
			}

			$company = '';
			$department = '';
			$salutation = '';


			try {
				$firstname = $vorname;
			} catch (Exception $e) {
				$firstname = '';
			}

			try {
				$lastname = $nachname;
			} catch (Exception $e) {
				$lastname = '';
			}

			try {
				$street = $strasse;
			} catch (Exception $e) {
				$street = '';
			}

			try {
				$zipcode = $plz;
			} catch (Exception $e) {
				$zipcode = '';
			}

			try {
				$city = $stadt;
			} catch (Exception $e) {
				$city = '';
			}

			$additional_address_line1 = '';
			$additional_address_line2 = '';


			$sql = 'INSERT INTO ';
			$sql .= "`a_wlorder_senderaddress` ";
			$sql .= "(`orderID`, `countryID`, `stateID`, `userID`, `company`, `department`, `salutation`, `firstname`, `lastname`, `street`, `zipcode`, `city`, `additional_address_line1`, `additional_address_line2`) VALUES ";
			$sql .= "(" . $orderId. ", '" .$country.  "', '". $state . "', ". $userID .", '" . $company . "', '" . $department . "', '" . $salutation . "', '" . $firstname . "', '" . $lastname . "', '" . $street . "', '" . $zipcode . "', '" . $city . "', '" . $additional_address_line1 . "', '" . $additional_address_line2 . "') ";
			$db->query($sql);
			return $orderNumber;

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

	public function getSenderAddressId($db, $customerId){
		$sql = "SELECT senderAdressID FROM a_wluser_senderaddress WHERE userID=?";
		$userSenderAddresses = $db->fetchOne($sql, array($customerId));

		// keine vorhanden
		if(empty($userSenderAddresses) || $userSenderAddresses == 0 || $userSenderAddresses == null){
			return 0;
		}

		return number_format($userSenderAddresses);
	}

}
