<?php

/**
 * configHelper
 *
 * @category   PayIntelligent
 * @package    Expression package is undefined on line 6, column 18 in Templates/Scripting/PHPClass.php.
 * @copyright  Copyright (c) 2011 PayIntelligent GmbH (http://payintelligent.de)
 */
class Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_ConfigHelper
{
	private $dbTable = 'sofort_config_data';

	/**
	 * creates the database table
	 */
	private function _initDb(){
		$sql = "CREATE TABLE IF NOT EXISTS ".$this->dbTable."("
               . "id int(1) NOT NULL UNIQUE,"
               . "option_general_reason_one varchar(255) DEFAULT '{{order_id}}',"
               . "option_general_reason_two varchar(255) DEFAULT '',"
               . "option_general_logging tinyint(1) DEFAULT 0,"
               . "option_general_create_transactions tinyint(1) DEFAULT 1,"
               . "option_sofortbanking_frontend_display tinyint(1) DEFAULT 1,"
               . "option_sofortbanking_key varchar(255) DEFAULT '',"
               . "option_sofortbanking_customer_protection tinyint(1) DEFAULT 0,"
               . "option_sofortbanking_recommended_payment tinyint(1) DEFAULT 1,"
               . "option_sofortbanking_state_temporary tinyint(1) DEFAULT 17,"
               . "option_sofortbanking_state_payment_confirmed tinyint(1) DEFAULT 12,"
               . "option_sofortbanking_state_payment_received tinyint(1) DEFAULT 0,"
               . "option_sofortbanking_state_payment_canceled tinyint(1) DEFAULT 35,"
               . "option_sofortbanking_state_investigation_needed tinyint(1) DEFAULT 21,"
               . "option_sofortbanking_state_refund_partial tinyint(1) DEFAULT 20,"
               . "option_sofortbanking_state_refund_full tinyint(1) DEFAULT 20,"
               . "option_ideal_frontend_display tinyint(1) DEFAULT 1,"
               . "option_ideal_key varchar(255) DEFAULT '',"
               . "option_ideal_project_password varchar(255) DEFAULT '',"
               . "option_ideal_notification_password varchar(255) DEFAULT '',"
               . "option_ideal_recommended_payment tinyint(1) DEFAULT 1,"
               . "option_ideal_state_temporary tinyint(1) DEFAULT 17,"
               . "option_ideal_state_payment_pending tinyint(1) DEFAULT 0,"
               . "option_ideal_state_payment_received tinyint(1) DEFAULT 12,"
               . "option_ideal_state_payment_canceled tinyint(1) DEFAULT 35,"
               . "option_ideal_state_storno tinyint(1) DEFAULT 35,"
               . "option_ideal_state_refund_partial tinyint(1) DEFAULT 20,"
               . "option_ideal_state_refund_full tinyint(1) DEFAULT 20"
               . ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
               . "INSERT IGNORE INTO ".$this->dbTable." ( id ) VALUES(1);";
        Shopware()->Db()->query($sql);
	}

	/**
     * Saves the config properties into the db
     */
    public function saveData()
    {
        $this->_initDb();
        $swConfig = Shopware()->Plugins()->Frontend()->SofortPayment()->Config();
        $data = array(
			'option_general_reason_one' => (string)$swConfig->get('option_general_reason_one'),
			'option_general_reason_two' => (string)$swConfig->get('option_general_reason_two'),
			'option_general_logging' => (int)$swConfig->get('option_general_logging'),
			'option_general_create_transactions' => (int)$swConfig->get('option_general_create_transactions'),
			'option_sofortbanking_frontend_display' => (int)$swConfig->get('option_sofortbanking_frontend_display'),
			'option_sofortbanking_key' => (string)$swConfig->get('option_sofortbanking_key'),
			'option_sofortbanking_customer_protection' => (int)$swConfig->get('option_sofortbanking_customer_protection'),
			'option_sofortbanking_recommended_payment' => (int)$swConfig->get('option_sofortbanking_recommended_payment'),
			'option_sofortbanking_state_temporary' => (int)$swConfig->get('option_sofortbanking_state_temporary'),
			'option_sofortbanking_state_payment_confirmed' => (int)$swConfig->get('option_sofortbanking_state_payment_confirmed'),
			'option_sofortbanking_state_payment_received' => (int)$swConfig->get('option_sofortbanking_state_payment_received'),
			'option_sofortbanking_state_payment_canceled' => (int)$swConfig->get('option_sofortbanking_state_payment_canceled'),
			'option_sofortbanking_state_investigation_needed' => (int)$swConfig->get('option_sofortbanking_state_investigation_needed'),
			'option_sofortbanking_state_refund_partial' => (int)$swConfig->get('option_sofortbanking_state_refund_partial'),
			'option_sofortbanking_state_refund_full' => (int)$swConfig->get('option_sofortbanking_state_refund_full'),
			'option_ideal_frontend_display' => (int)$swConfig->get('option_ideal_frontend_display'),
			'option_ideal_key' => (string)$swConfig->get('option_ideal_key'),
			'option_ideal_project_password' => (string)$swConfig->get('option_ideal_project_password'),
			'option_ideal_notification_password' => (string)$swConfig->get('option_ideal_notification_password'),
			'option_ideal_recommended_payment' => (int)$swConfig->get('option_ideal_recommended_payment'),
			'option_ideal_state_temporary' => (int)$swConfig->get('option_ideal_state_temporary'),
			'option_ideal_state_payment_pending' => (int)$swConfig->get('option_ideal_state_payment_pending'),
			'option_ideal_state_payment_received' => (int)$swConfig->get('option_ideal_state_payment_received'),
			'option_ideal_state_payment_canceled' => (int)$swConfig->get('option_ideal_state_payment_canceled'),
			'option_ideal_state_storno' => (int)$swConfig->get('option_ideal_state_storno'),
			'option_ideal_state_refund_partial' => (int)$swConfig->get('option_ideal_state_refund_partial'),
			'option_ideal_state_refund_full' => (int)$swConfig->get('option_ideal_state_refund_full'),
		);

        Shopware()->Db()->update($this->dbTable, $data, '`id`=1');
    }

	/**
     * Restores all configurations from a past installation
     * @return mixed
     */
    public function loadData()
    {
        $this->_initDb();
        $sql = "Select * FROM ".$this->dbTable." WHERE id = 1;";
        $result = Shopware()->Db()->fetchRow($sql);
        return $result;
    }
}
