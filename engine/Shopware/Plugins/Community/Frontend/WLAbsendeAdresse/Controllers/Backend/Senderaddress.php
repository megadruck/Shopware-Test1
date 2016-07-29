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
			'updateSenderData'
		];
	}

}