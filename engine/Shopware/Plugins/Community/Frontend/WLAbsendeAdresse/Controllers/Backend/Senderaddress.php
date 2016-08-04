<?php

use Doctrine\ORM\Query;
use Shopware\Components\CSRFWhitelistAware;


class Shopware_Controllers_Backend_Senderaddress extends Shopware_Controllers_Backend_ExtJs implements CSRFWhitelistAware
{
	/**
	 * Provides an action which can be triggered using an AJAX request to get the basic
	 * sender data.
	 *
	 */
	public function getSenderDataAction()
	{
		$orderId = (int)$this->Request()->getParam('orderId');

		/** @var Connection $connection */
		$connection = $this->get('dbal_connection');

		$em = Shopware()->Models();
		$sql = "SELECT address, ord, state, country FROM Shopware\CustomModels\Order\OrderSenderAddress address ";
		$sql .= " LEFT JOIN address.order ord ";
		$sql .= " LEFT JOIN address.state state ";
		$sql .= " LEFT JOIN address.country country ";
		$sql .= " WHERE address.order = " . $orderId;
		$query = $em->createQuery($sql);
		$data = $query->getResult ( Query::HYDRATE_ARRAY )[0];

		$this->view->assign(['success' => true, 'data' => $data]);
	}


	public function getCustomerSenderDataAction()
	{
		$customerId = (int)$this->Request()->getParam('customerId');
		$db = Shopware()->Db();
		$em = Shopware()->Models();


		$sql = "SELECT senderAdressID FROM a_wluser_senderaddress WHERE userID=?";
		$userSenderAddressId = $db->fetchOne($sql, array($customerId));

		if($userSenderAddressId == null || $userSenderAddressId == 0 || empty($userSenderAddressId)){
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


			$data = array(
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
		} else {
			$sql = "SELECT address, state, country FROM Shopware\Models\Customer\Address address ";
			$sql .= " LEFT JOIN address.state state ";
			$sql .= " LEFT JOIN address.country country ";
			$sql .= " WHERE address.id = " . $userSenderAddressId;
			$query = $em->createQuery($sql);
			$data = $query->getResult ( Query::HYDRATE_ARRAY )[0];
		}



		$this->view->assign(['success' => true, 'data' => $data]);
	}

	/**
	 * Get the update sender data
	 */
	public function updateSenderDataAction()
	{

		$orderId = (int)$this->Request()->getParam('orderId');
		$rawBody = $this->Request()->getRawBody();

		if (!empty($rawBody)){
			$params = json_decode($rawBody, true); // 2nd param to get as array
		}

		$salutation = $params['salutation'];
		$firstname = $params['firstname'];
		$lastname= $params['lastname'];
		$company = $params['company'];
		$department = $params['department'];
		$street = $params['street'];
		$additional_address_line1 = $params['additional_address_line1'];
		$additional_address_line2 = $params['additional_address_line2'];
		$zipcode = $params['zipcode'];
		$city = $params['city'];
		$stateId = $params['stateId'];
		$countryId = $params['countryId'];

		$sql = "UPDATE `a_wlorder_senderaddress` SET";
		$sql .= " `company` = '" .$company. "',";
		$sql .= " `department` = '" .$department. "',";
		$sql .= " `salutation` = '" .$salutation. "',";
		$sql .= " `firstname` = '" .$firstname. "',";
		$sql .= " `lastname` = '" .$lastname. "',";
		$sql .= " `street` = '" .$street. "',";
		$sql .= " `zipcode` = '" .$zipcode. "',";
		$sql .= " `city` = '" .$city. "',";
		$sql .= " `additional_address_line1` = '".$additional_address_line1."',";
		$sql .= " `additional_address_line2` = '" .$additional_address_line2. "',";
		$sql .= " `countryID` = '" .$countryId. "',";
		$sql .= " `stateID` = '" .$stateId. "'";
		$sql .= " WHERE `orderID` = '" .$orderId. "'";

		$db = Shopware()->Db();
		$db->query($sql);

		$this->view->assign(['success' => true]);
	}

	public function getWhitelistedCSRFActions()
	{
		return [
			'getSenderData',
			'getCustomerSenderDataAction',
			'updateSenderData'
		];
	}

}