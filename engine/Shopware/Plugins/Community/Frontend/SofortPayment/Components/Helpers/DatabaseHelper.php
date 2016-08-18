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
 * This Helper class provides easier access to content of the database
 */
class Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_DatabaseHelper
{

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * Creates an instance of this class
     */
    public function __construct()
    {
        $this->log = Shopware()->Pluginlogger();
    }

    /**
     * Creates the Table for temporary basket storage
     *
     * @throws Exception
     */
    public function createBasketTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `sofort_payment_basket` (" .
                "`id` int(11) NOT NULL," .
                "`sessionID` varchar(70) COLLATE utf8_unicode_ci NOT NULL," .
                "`userID` int(11) NOT NULL DEFAULT '0'," .
                "`articlename` varchar(255) COLLATE utf8_unicode_ci NOT NULL," .
                "`articleID` int(11) NOT NULL DEFAULT '0'," .
                "`ordernumber` varchar(30) COLLATE utf8_unicode_ci NOT NULL," .
                "`shippingfree` int(1) NOT NULL DEFAULT '0'," .
                "`quantity` int(11) NOT NULL DEFAULT '0'," .
                "`price` double NOT NULL DEFAULT '0'," .
                "`netprice` double NOT NULL DEFAULT '0'," .
                "`tax_rate` double NOT NULL," .
                "`datum` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'," .
                "`modus` int(11) NOT NULL DEFAULT '0'," .
                "`esdarticle` int(1) NOT NULL," .
                "`partnerID` varchar(45) COLLATE utf8_unicode_ci NOT NULL," .
                "`lastviewport` varchar(255) COLLATE utf8_unicode_ci NOT NULL," .
                "`useragent` varchar(255) COLLATE utf8_unicode_ci NOT NULL," .
                "`config` mediumtext COLLATE utf8_unicode_ci NOT NULL," .
                "`currencyFactor` double NOT NULL," .
                "PRIMARY KEY (`id`)" .
                ")" .
                "ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci";
            Shopware()->Db()->query($sql);
        } catch (Exception $exception) {
            $this->log->error("There was an Error creating the Basket-Table: " . $exception->getMessage());
            throw new Exception("There was an Error creating the Basket-Table: " . $exception->getMessage());
        }
    }

    /**
     * Updates the Table for temporary basket storage
     *
     * @throws Exception
     */
    public function updateBasketTable()
    {
        try {
            $sql = "ALTER TABLE sofort_payment_basket DROP PRIMARY KEY, ADD PRIMARY KEY(id);";
            Shopware()->Db()->query($sql);
        } catch (Exception $exception) {
            $this->log->error("There was an Error updating the Sofort Basket-Table: " . $exception->getMessage());
            throw new Exception("There was an Error creating the Sofort Basket-Table: " . $exception->getMessage());
        }
    }

    /**
     * Removes the sofort_payment_basket table from the db
     *
     * @throws Exception
     */
    public function removeBasketTable()
    {
        $sql = "DROP TABLE IF EXISTS `sofort_payment_basket`";
        try {
            Shopware()->Db()->query($sql);
        } catch (Exception $exception) {
            $this->log->error("There was an Error removing the Basket-Table: " . $exception->getMessage());
            throw new Exception("There was an Error removing the Basket-Table: " . $exception->getMessage());
        }
    }

    /**
     * This function saves the Basket temporary
     * Also it removes all entries older than 2 weeks to maintain db space
     *
     * @param string $sessionId
     */
    public function saveBasket($sessionId)
    {
        $column = "`id`, `sessionID`, `userID`, `articlename`, `articleID`, `ordernumber`, `shippingfree`, `quantity`, `price`, `netprice`, "
            . " `tax_rate`, `datum`, `modus`, `esdarticle`, `partnerID`, `lastviewport`, `useragent`, `config`, `currencyFactor`";

        $sql = "REPLACE INTO `sofort_payment_basket` "
            . "($column) "
            . "SELECT $column FROM `s_order_basket` WHERE `sessionID`=?;";

        try {
            Shopware()->Db()->query($sql, array($sessionId));
            Shopware()->Db()->query("DELETE FROM `sofort_payment_basket` WHERE  DATEDIFF(NOW(), `datum`) >= 14;");
        } catch (Exception $exception) {
            $this->log->logManually(__CLASS__, __METHOD__ . ": " . $exception->getMessage());
        }
    }

    /**
     * This function clears the Basket
     *
     * @param string $sessionId
     */
    public function clearBasket($sessionId)
    {
        $sql = "DELETE FROM `s_order_basket` WHERE `sessionID`=?;";
        try {
            Shopware()->Db()->query($sql, array($sessionId));
        } catch (Exception $exception) {
            $this->log->logManually(__CLASS__, __METHOD__ . ": " . $exception->getMessage());
        }
    }

    /**
     * This function restores the Basket to its origin-state
     *
     * @param string $sessionId
     */
    public function restoreBasket($sessionId)
    {
        $column = "`id`, `sessionID`, `userID`, `articlename`, `articleID`, `ordernumber`, `shippingfree`, `quantity`, `price`, `netprice`, "
            . " `tax_rate`, `datum`, `modus`, `esdarticle`, `partnerID`, `lastviewport`, `useragent`, `config`, `currencyFactor`";

        $sql = "REPLACE INTO `s_order_basket` "
            . "($column) "
            . "SELECT $column FROM `sofort_payment_basket` WHERE `sessionID`=?;";
        $sqlDelete = "DELETE FROM `sofort_payment_basket` WHERE `sessionID`=?;";
        try {
            Shopware()->Db()->query($sql, array($sessionId));
            Shopware()->Db()->query($sqlDelete, array($sessionId));
        } catch (Exception $exception) {
            $this->log->logManually(__CLASS__, __METHOD__ . ": " . $exception->getMessage());
        }
    }

    /**
     * Changes the transaction id of the given order to the specified new one.
     *
     * @param String $ordernumber   order identifier
     * @param String $transactionId New transaction Id to replace the old one
     *
     * @throws Exception
     */
    public function changeTransactionId($ordernumber, $transactionId)
    {
        if (empty($ordernumber) || empty($transactionId)) {
            $this->log->logManually(__CLASS__, "Trying to change a transaction id using illegal parameters"
				."(ordernumber:".var_export($ordernumber,true)."|transactionId:".var_export($transactionId,true).")");
			return;
        }
        $sql = "UPDATE `s_order` SET `transactionID`= ? WHERE `ordernumber` = ?";
        Shopware()->Db()->query($sql, array($transactionId, $ordernumber));
    }

    /**
     * Returns the Ordernumber of the order mapped to the given transaction id
     *
     * @param String $transactionId
     *
     * @return String
     */
    public function getOrdernumberByTransactionId($transactionId)
    {
        $sql = "SELECT `ordernumber` FROM `s_order` WHERE `transactionID` = ?";

        return Shopware()->Db()->fetchOne($sql, array($transactionId));
    }

    /**
     * Returns the state of the order with the given ordernumber
     *
     * @param string $orderId
     *
     * @return string orderstate
     */
    public function getOderStateById($orderId)
    {
        $sql = "SELECT `cleared` FROM `s_order` WHERE `id` = ?";

        return Shopware()->Db()->fetchOne($sql, array($orderId));
    }

    /**
     * Removes the order with the respective ordernumber
     *
     * @param String $orderNumber
     *
     * @return bool
     */
    public function removeOrder($orderNumber)
    {
        try {
            $this->restockShopForOrder($orderNumber);
            $entity = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')
                ->findOneByNumber($orderNumber);
            $manager = Shopware()->Models();
            $manager->remove($entity);
            $manager->flush();
        } catch (Exception $exception) {
            $this->log->logManually(__CLASS__, __METHOD__ . ": " . $exception->getMessage());
        }
    }

    /**
     * Adjusts the stock for an aborted/failed order
     *
     * @param String $orderNumber
     */
    public function restockShopForOrder($orderNumber)
    {
        try {
            $sql = Shopware()->Db()->select()
                    ->from('s_order_details',array('articleID','quantity'))
                    ->join('s_articles_details', '`s_order_details`.`articleID` = `s_articles_details`.`articleID`', array('instock', 'sales'))
                    ->where('`s_order_details`.`ordernumber` = ?', array($orderNumber));
            $articles = Shopware()->Db()->fetchAll($sql);

            if(!$articles || is_array($articles) || count($articles) === 0){
                return;
            }
            foreach($articles as $article){
                Shopware()->Db()->update('s_articles_details', array(
                    'instock' => $article['instock'] + $article['quantity'],
                    'sales' => $article['sales'] - $article['quantity']
                ), '`articleID` = '. $article['articleID']);
            }
        } catch (Exception $exception) {
            $this->log->logManually(__CLASS__, __METHOD__ . ": " . $exception->getMessage());
        }
    }

    /**
     * Returns the Ordernumber of the order mapped to the given transaction id
     *
     * @param String $transactionId
     *
     * @return String
     */
    public function getOrderByTransactionId($transactionId)
    {
        $sql = "SELECT `id` FROM `s_order` WHERE `transactionID` = ?";

        return Shopware()->Db()->fetchOne($sql, array($transactionId));
    }

    /**
     * Returns the key of the payment with the given name
     *
     * @param String $name
     *
     * @return String Key of the payment
     */
    public function getPaymentKey($name)
    {
        $sql = "SELECT  `id` FROM  `s_core_paymentmeans` WHERE  `name` =  ?";

        return Shopware()->Db()->fetchOne($sql, array($name));
    }

    /**
     * Returns the translation for the given name from the s_core_snippets table.
     * If no translation is found the given defaulttext will be returned
     *
     * @param String $name        Name of the Snippet
     * @param String $defaulttext Text to display if there is no translation
     * @param String $language    Shortname of the language e.g. de_DE
     * @param int    $localeId    localeId can be argumented. if not given, it will be generated from a database context.
     *
     * @return String
     */
    public function getTranslationSnippet($name, $defaulttext = "", $language = null, $localeId = null)
    {

        try {
            $shopId = Shopware()->Shop()->getId();
            $sql = "SELECT `value` FROM `s_core_snippets`" .
                " WHERE `namespace` = 'Sofort'" .
                " AND `shopID` = ?" .
                " AND `name` = ?";
            $translation = Shopware()->Db()->fetchOne($sql, array($shopId, $name));
        } catch (Exception $exception) {
            if (empty($language)) {
                $language = Shopware()->Shop()->getLocale()->getLanguage();
            }

            if (empty($localeId)) {
                $localeId = $this->getLocaleId($language);
            }

            $sql = "SELECT `value` FROM `s_core_snippets`" .
                " WHERE `namespace` = 'Sofort'" .
                " AND `localeID` = ?" .
                " AND `name` = ?";
            $translation = Shopware()->Db()->fetchOne($sql, array($localeId, $name));
        }

        if (empty($translation)) {
            $translation = $defaulttext;
        }

        return $translation;
    }

    /**
     * Returns the Id of a language from the shopware core tables
     *
     * @param String $code Shortname of the language you want the Id of (e.g. de_DE or en_GB)
     *
     * @return String
     */
    public function getLocaleId($code)
    {
        $sql = "SELECT `id` FROM `s_core_locales` WHERE `locale` LIKE  ?";

        return Shopware()->Db()->fetchOne($sql, array($code));
    }

    /**
     * Adds a single translation snippet to the shops database
     *
     * @param String $value New translation used for the given name
     * @param String $name  Name of the snippet. Names can be taken from the name list
     * @param String $parameter
     *
     * @throws Exception
     */
    public function addLocaleSnippet($value, $name, $parameter)
    {
        $sql_snippet = "REPLACE INTO `s_core_snippets` (" .
            "`namespace`," .
            " `name`," .
            " `value`," .
            " `localeID`," .
            " `shopID`," .
            "`created`, " .
            "`updated`" .
            ") VALUES ('Sofort', ?, ?, ?, ?, NOW(), NOW())";
        $localeId = $parameter['localeId'];
        $shopIds = $parameter['shopIds'];
        foreach ($shopIds as $shop) {
            Shopware()->Db()->query($sql_snippet, array($name, $value, $localeId, $shop['id']));
        }
    }

    /**
     * Executes the description changes for the update to version 2.0.4
     */
    public function updatePaymentDescription()
    {
        $sql = "UPDATE `s_core_paymentmeans` SET  `description` = 'SOFORT Überweisung / SOFORT Banking' WHERE  `name` = 'sofortbanking';";
        Shopware()->Db()->query($sql);
    }

    /**
     * Returns an array of all shop ids with the given locale
     *
     * @param String $locale Short tag of the desired language (e.G. "de_DE")
     *
     * @return array $shopIds all shop ids with the given language
     *
     * @throws Exception
     */
    public function getShopIds($locale)
    {
        $sql_shop_ids = "SELECT `id` FROM `s_core_shops` WHERE `locale_id`= ?";
        $localeId = $this->getLocaleId($locale);
        try {
            $shopIds = Shopware()->Db()->fetchAll($sql_shop_ids, array($localeId));
        } catch (Exception $exception) {
            return array();
        }

        return $shopIds;
    }

    /**
     * Removes unnecessary 'Sofort' Snippets from the database
     *
     * @throws Exception
     */
    public function removeSnippets()
    {
        $sql = "DELETE FROM `s_core_snippets` WHERE `namespace` = 'Sofort'";
        try {
            Shopware()->Db()->query($sql);
        } catch (Exception $exception) {
            $this->log->error("There was an Error removing the snippets: " . $exception->getMessage());
            throw new Exception("There was an Error removing the snippets: " . $exception->getMessage());
        }
    }

    /**
     * Creates the Table for the Modules Log
     *
     * @throws Exception
     */
    public function createLoggingTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `sofort_payment_log` (" .
                "`id` int(11) NOT NULL AUTO_INCREMENT," .
                "`entry_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP," .
                "`version_module` varchar(25) NOT NULL COLLATE utf8_unicode_ci," .
                "`source` varchar(250) COLLATE utf8_unicode_ci NOT NULL," .
                "`message` text COLLATE utf8_unicode_ci NOT NULL," .
                "PRIMARY KEY (`id`)" .
                ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1";
            Shopware()->Db()->query($sql);
        } catch (Exception $exception) {
            $this->log->error("There was an Error creating the Log-Table: " . $exception->getMessage());
            throw new Exception("There was an Error creating the Log-Table: " . $exception->getMessage());
        }
    }

    /**
     * Removes the sofort_payment_log table from the db
     *
     * @throws Exception
     */
    public function removeLogTable()
    {
        $sql = "DROP TABLE IF EXISTS `sofort_payment_log`";
        try {
            Shopware()->Db()->query($sql);
        } catch (Exception $exception) {
            $this->log->error("There was an Error removing the Log-Table: " . $exception->getMessage());
            throw new Exception("There was an Error removing the Log-Table: " . $exception->getMessage());
        }
    }

    /**
     * Returns an array of log entries according to the arguments
     *
     * @param int $start first entry to be displayed
     * @param int $limit number of entries to be displayed
     *
     * @return array Log Entries
     */
    public function getLog($start, $limit)
    {
        $start = (int) $start;
        $limit = (int) $limit;
        if ($start > $limit) {
            $limit = $start;
        }
        //Build SQL Statement using arguments
        $read = "SELECT * FROM `sofort_payment_log` LIMIT " . $start . ", " . $limit;
        //Process Select and return result
        $result = Shopware()->Db()->fetchAll($read);

        return $result;
    }

    /**
     * Inserts an entry in the log table (sofort_payment_log)
     *
     * @param String $moduleVersion
     * @param String $source
     * @param String $message
     */
    public function insertLogEntry($moduleVersion, $source, $message)
    {
        $sql = "INSERT INTO `sofort_payment_log`(`version_module`,`source`, `message`)
            VALUES(?,?,?)";
        Shopware()->Db()->query($sql, array($moduleVersion, $source, $message));
    }

    /**
     * Returns the number of log entries as an integer
     *
     * @return int logCount
     */
    public function getLogCount()
    {
        $getTotal = "SELECT count(*) FROM `sofort_payment_log`";
        $count = Shopware()->Db()->fetchOne($getTotal);

        return (int) $count;
    }

    /**
     * Removes all payment rules from the db
     */
    public function removePaymentRules($paymentId)
    {
        $sql = "DELETE FROM `s_core_rulesets` WHERE `paymentID` = ?";
        try {
            Shopware()->Db()->query($sql, array($paymentId));
        } catch (Exception $exception) {
            $this->log->error("There was an Error removing the payment rules: " . $exception->getMessage());
            throw new Exception("There was an Error removing the payment rules: " . $exception->getMessage());
        }
    }

}
