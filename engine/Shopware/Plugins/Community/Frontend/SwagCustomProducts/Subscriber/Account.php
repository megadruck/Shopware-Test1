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

namespace ShopwarePlugins\SwagCustomProducts\Subscriber;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query;
use Shopware\Components\DependencyInjection\Container as DIContainer;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs as ActionEventArgs;
use PDO;
use ShopwarePlugins\SwagCustomProducts\Components\Services\CustomProductsService;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TemplateService;

class Account implements SubscriberInterface
{
    /** @var DIContainer $container */
    private $container;

    /** @var Connection $connection */
    private $connection;

    /**
     * @param DIContainer $container
     */
    public function __construct(DIContainer $container)
    {
        $this->container = $container;
        $this->connection = $container->get('dbal_connection');
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Account' => 'extendAccountOrder',
            'sAdmin::sGetOpenOrderData::after' => 'afterGetOpenOrderData'
        ];
    }

    /**
     * Iterates through all order-positions and checks for a customized product.
     * In this case, the "repeat order"-button becomes hidden.
     *
     * @param \Enlight_Hook_HookArgs $hookArgs
     * @return array
     */
    public function afterGetOpenOrderData(\Enlight_Hook_HookArgs $hookArgs)
    {
        $return = $hookArgs->getReturn();
        $orderData = $return['orderData'];

        $orderData = array_map(function ($item) {
            foreach ($item['details'] as $detailItem) {
                if ($this->shouldHideButton($detailItem)) {
                    $item['activeBuyButton'] = 0;
                }
            }
            return $item;
        }, $orderData);

        $return['orderData'] = $orderData;

        return $return;
    }

    /**
     * @param ActionEventArgs $args
     */
    public function extendAccountOrder(ActionEventArgs $args)
    {
        $action = strtolower($args->getRequest()->getActionName());

        if ($action !== 'orders') {
            return;
        }

        $view = $args->getSubject()->View();
        $orders = $view->getAssign('sOpenOrders');

        $detailIds = [];
        foreach ($orders as $order) {
            $detailIds = array_merge($detailIds, array_column($order['details'], 'id'));
        }

        $attributes = $this->getDetailAttributes($detailIds);

        foreach ($orders as &$order) {
            $customProductsAmounts = [];
            foreach ($order['details'] as &$detail) {
                $id = $detail['id'];

                if (isset($attributes[$id])) {
                    $detail['attribute'] = $attributes[$id];
                }

                $amountKey = $detail['attribute']['swag_custom_products_configuration_hash'];
                $detailPrice = (float) str_replace(',', '.', $detail['price']);
                if ($customProductsAmounts[$amountKey]) {
                    $customProductsAmounts[$amountKey] += $detail['quantity'] * $detailPrice;
                } elseif ($amountKey) {
                    $customProductsAmounts[$amountKey] = $detail['quantity'] * $detailPrice;
                }
            }

            foreach ($order['details'] as &$detail) {
                if ($detail['attribute']['swag_custom_products_mode'] != 1) {
                    continue;
                }

                $hash = $detail['attribute']['swag_custom_products_configuration_hash'];
                if (array_key_exists($hash, $customProductsAmounts)) {
                    $detail['attribute']['custom_products_amount'] = $customProductsAmounts[$hash];
                }
            }
        }

        $view->assign('sOpenOrders', $orders);
    }

    /**
     * @param integer[] $detailIds
     * @return array
     */
    private function getDetailAttributes(array $detailIds)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select(['attribute.detailID', 'attribute.*']);
        $query->from('s_order_details_attributes', 'attribute');
        $query->where('detailID IN (:ids)');
        $query->setParameter(':ids', $detailIds, Connection::PARAM_INT_ARRAY);

        return $query->execute()->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);
    }

    /**
     * Checks if an order-position is part of a custom-product template and if this template contains a required question.
     * In this case, the "repeat order"-button must not be shown and the button should be hidden.
     *
     * @param $detailItem
     * @return mixed
     */
    private function shouldHideButton($detailItem)
    {
        /** @var CustomProductsService $customProductsService */
        $customProductsService = $this->container->get('custom_products.service');

        /** @var TemplateService $templateService */
        $templateService = $this->container->get('custom_products.template_service');
        $customProductTemplate = $templateService->getTemplateByProductId($detailItem['articleID']);

        return (int) $customProductTemplate['active'] === 1
            && $customProductsService->isCustomProduct($detailItem['articleordernumber'])
            && $customProductsService->checkForRequiredOptions($detailItem['articleID']);
    }
}
