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

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_ActionEventArgs as ActionEventArgs;
use Enlight_Event_EventArgs as EventArgs;
use Enlight_Exception;
use Shopware\Components\DependencyInjection\Container as DIContainer;
use ShopwarePlugins\SwagCustomProducts\Components\Services\BasketManager;
use ShopwarePlugins\SwagCustomProducts\Components\Services\CustomProductsService;
use ShopwarePlugins\SwagCustomProducts\Components\Services\HashManager;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TemplateService;

class Checkout implements SubscriberInterface
{
    /** @var DIContainer */
    private $container;

    /**
     * @param DIContainer $container
     */
    public function __construct(DIContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onPostDispatchCheckout',
            'Enlight_Controller_Action_Frontend_Checkout_changeQuantity' => 'onChangeQuantity',
            'Enlight_Controller_Action_Frontend_Checkout_addArticle' => 'onAddArticle',
            'Shopware_Modules_Order_SaveOrder_ProcessDetails' => 'onProcessDetails'
        ];
    }

    /**
     * Event listener which creates permanent hashes and updates all custom products in s_order_details with a
     * permanent hash.
     *
     * @param EventArgs $args
     * @throws Enlight_Exception
     */
    public function onProcessDetails(EventArgs $args)
    {
        /** @var HashManager $hashManager */
        $hashManager = $this->container->get('custom_products.hash_manager');
        $sBasketData = $args->getDetails();
        $connection = $this->container->get('dbal_connection');

        $connection->beginTransaction();

        try {
            $permanentHashMapping = $this->generateHashMapping($sBasketData, $hashManager);
            $this->updateOrderConfigurationHashes($permanentHashMapping);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Only has affect if off canvas cart is deactivated.
     * On adding a new custom product, the modal box has no content because the sAddArticle is not execute until the end
     * @see \ShopwarePlugins\SwagCustomProducts\Subscriber\Basket::addArticle()
     * Therefore the information must be assigned manually.
     *
     * @param ActionEventArgs $args
     */
    public function onPostDispatchCheckout(ActionEventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Checkout $controller */
        $controller = $args->getSubject();
        $request = $controller->Request();
        $view = $controller->View();

        $view->assign(
            'isCheckoutConfirm',
            strtolower($request->getControllerName()) == 'checkout' && strtolower($request->getActionName()) == 'confirm'
        );

        $basket = $view->getAssign('sBasket');
        if ($basket) {
            $basket = $this->clearCustomProductsOptionsAndValues($basket);
        }
        $view->assign('sBasket', $basket);

        if ($request->getActionName() !== 'ajax_add_article') {
            return;
        }

        $sArticle = $view->getAssign('sArticle');

        if ($sArticle) {
            return;
        }

        $basket = $controller->getBasket();
        $basketId = $view->getAssign('lastInsertedCustomProductBasketId');

        foreach ($basket['content'] as $item) {
            if ($item['id'] === $basketId) {
                $view->assign('sArticle', $item);
                break;
            }
        }
    }

    /**
     * @param EventArgs $args
     * @return null
     */
    public function onChangeQuantity(EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Checkout $controller */
        $controller = $args->get('subject');
        $request = $controller->Request();
        $basketId = $request->get('sArticle');
        $quantity = $request->get('sQuantity');

        /** @var CustomProductsService $customProductsService */
        $customProductsService = $this->container->get('custom_products.service');

        if (!$customProductsService->isCustomProduct($basketId, true)) {
            return null;
        }

        /** @var BasketManager $basketManager */
        $basketManager = $this->container->get('custom_products.basket_manager');
        $basketPosition = $basketManager->readBasketPosition($basketId);

        $hash = $basketPosition['swag_custom_products_configuration_hash'];
        $basketManager->setQuantity($hash, $quantity);
    }

    /**
     * @param EventArgs $args
     * @return bool|null
     */
    public function onAddArticle(EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Checkout $controller */
        $controller = $args->get('subject');
        $request = $controller->Request();
        $targetAction = $request->getParam('sTargetAction');

        if ($targetAction !== 'confirm' && $targetAction !== 'cart') {
            return null;
        }

        /** @var CustomProductsService $customProductsService */
        $customProductsService = $this->container->get('custom_products.service');
        $orderNumber = $request->getParam('sAdd');

        if (!$customProductsService->isCustomProduct($orderNumber)) {
            return null;
        }

        $productId = (int) $this->container->get('modules')->Articles()->sGetArticleIdByOrderNumber($orderNumber);

        if (!$customProductsService->checkForRequiredOptions($productId)) {
            return null;
        }

        $controller->redirect(
            [
                'controller' => 'detail',
                'action' => 'index',
                'sArticle' => $productId,
                'number' => $orderNumber,
                'customProductNeedsConfig' => true
            ]
        );

        return true;
    }

    /**
     * @param string $hash
     * @return array
     */
    private function getConfigurationByHash($hash)
    {
        $connection = $this->container->get('dbal_connection');

        $builder = $connection->createQueryBuilder();
        $builder->select('*')
            ->from('s_plugin_custom_products_configuration_hash')
            ->where('hash = :hash')
            ->setParameter('hash', $hash);

        return $builder->execute()->fetch();
    }

    /**
     * Generates a hash mapping for the old hash and the permanent hash.
     * The array is indexed by the old hash and uses the permanent hash as the value.
     *
     * index: old hash
     * value: permanent hash
     *
     * @param array $sBasketData
     * @param HashManager $hashManager
     * @return array
     */
    private function generateHashMapping(array $sBasketData, HashManager $hashManager)
    {
        $permanentHashMapping = []; //Indexed by non-permanent hash, value is the permanent hash
        $oldHash = '';
        foreach ($sBasketData as $key => $row) {
            if (empty($row['customProductHash'])) {
                continue;
            }

            //Check if the configuration has been changed
            if ($row['customProductHash'] !== $oldHash) {
                $oldHash = $row['customProductHash'];
            }

            $hashResult = $this->getConfigurationByHash($oldHash);
            $configuration = json_decode($hashResult['configuration'], true);
            $options = json_decode($hashResult['template'], true);

            //Check if the permanent hash already exists, if not generate a permanent hash
            if (!$permanentHashMapping[$oldHash]) {
                $permanentHash = $hashManager->manageHashByConfiguration($configuration, true, $options);
                $permanentHashMapping[$oldHash] = $permanentHash;
            }
        }

        return $permanentHashMapping;
    }

    /**
     * @param array $basket
     * @return array
     */
    private function clearCustomProductsOptionsAndValues(array $basket)
    {
        // remove custom products options and values from basket content, so $isLast is set correctly
        // all needed information for displaying them in the basket are on the product itself
        foreach ($basket['content'] as $key => $basketItem) {
            if ($basketItem['customProductMode'] == BasketManager::MODE_OPTION
                || $basketItem['customProductMode'] == BasketManager::MODE_VALUE
            ) {
                unset($basket['content'][$key]);
            }
        }

        return $basket;
    }

    /**
     * Replace old hashes with new permanent configuration hashes.
     *
     * @param array $permanentHashMapping - Indexed by old hash, value is the permanent hash
     */
    private function updateOrderConfigurationHashes(array $permanentHashMapping)
    {
        $builder = $this->container->get('dbal_connection')->createQueryBuilder();

        foreach ($permanentHashMapping as $oldHash => $newHash) {
            $builder->update('s_order_details_attributes')
                ->set('swag_custom_products_configuration_hash', ':permanentHash')
                ->where('swag_custom_products_configuration_hash = :oldHash')
                ->setParameter('permanentHash', $permanentHashMapping[$oldHash])
                ->setParameter('oldHash', $oldHash);

            $builder->execute();
        }
    }
}
