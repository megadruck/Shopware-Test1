<?php

/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace ShopwarePlugins\SwagCustomProducts\Components\Services;

use Doctrine\DBAL\Connection;
use Enlight_Hook_HookArgs;
use Shopware_Components_Document;
use Smarty_Data;

class DocumentValueExtender
{
    /** @var array */
    private $whiteList = ['date', 'numberfield', 'textarea', 'textfield', 'time', 'wysiwyg'];

    /** @var  Connection */
    private $connection;

    /** @var HashManager */
    private $hashManager;

    /**
     * DocumentValueExtender constructor.
     *
     * @param Connection $connection
     * @param HashManager $hashManager
     */
    public function __construct(Connection $connection, HashManager $hashManager)
    {
        $this->connection = $connection;
        $this->hashManager = $hashManager;
    }

    /**
     * Extends the view with option values
     *
     * @param Enlight_Hook_HookArgs $args
     */
    public function extendWithValues(Enlight_Hook_HookArgs $args)
    {
        /* @var Shopware_Components_Document $document */
        $document = $args->getSubject();

        /* @var Smarty_Data $view */
        $view = $document->_view;

        $orderData = $view->getTemplateVars('Order');
        $positions = $orderData['_positions'];

        $values = [];

        foreach ($positions as &$position) {
            if (!isset($position['attributes']['swag_custom_products_mode'])) {
                continue;
            }

            if ($position['attributes']['swag_custom_products_mode'] != BasketManager::MODE_OPTION) {
                continue;
            }

            $optionId = $position['articleID'];
            $optionType = $this->getOptionTypeById($optionId);

            if (!in_array($optionType, $this->whiteList)) {
                continue;
            }

            $hash = $position['attributes']['swag_custom_products_configuration_hash'];
            $config = $this->hashManager->findConfigurationByHash($hash);

            $values[$position['id']][$optionId] = $config[$optionId][0];
        }

        $view->assign('customProductOptionValues', $values);
    }

    /**
     * Try to find the type of the option by the option id.
     *
     * @param $id
     * @return bool|string
     */
    private function getOptionTypeById($id)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('type')
            ->from('s_plugin_custom_products_option')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchColumn();
    }
}
