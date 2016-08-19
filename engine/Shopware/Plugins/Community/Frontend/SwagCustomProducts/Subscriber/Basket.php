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
use Enlight_Event_EventArgs as EventArgs;
use Enlight_Hook_HookArgs as HookArgs;
use Shopware\Components\DependencyInjection\Container as DIContainer;
use ShopwarePlugins\SwagCustomProducts\Components\Calculator;
use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\ConverterInterface;
use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\Registry;
use ShopwarePlugins\SwagCustomProducts\Components\Services\BasketManager;
use ShopwarePlugins\SwagCustomProducts\Components\Services\CustomProductsService;
use ShopwarePlugins\SwagCustomProducts\Components\Services\HashManager;

class Basket implements SubscriberInterface
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
            'Shopware_Modules_Basket_AddArticle_Start' => 'addArticle',
            'sBasket::sDeleteArticle::before' => 'afterDeleteArticle',
            'sBasket::sGetAmountArticles::after' => 'getVoucherAmount',
            'Shopware_Modules_Basket_GetBasket_FilterSQL' => 'getBasketSqlQuery',
            'sBasket::sGetBasket::after' => 'getBasket',
            'Shopware_Modules_Basket_UpdateArticle_Start' => 'onUpdateArticle',
            'Shopware_Modules_Basket_GetBasket_FilterItemStart' => 'onFilterItemStart',
            'Shopware_Modules_Basket_GetBasket_FilterItemEnd' => 'onFilterItemEnd'
        ];
    }

    /**
     * Extends the amount which is used for percentage voucher calculation
     */
    public function getVoucherAmount(\Enlight_Hook_HookArgs $args)
    {
        $return = $args->getReturn();

        if (!$return['totalAmount']) {
            return;
        }

        $amount = $this->getCustomProductSurchargesAmount();

        if (!$amount) {
            return;
        }

        $return['totalAmount'] += $amount;

        $args->setReturn($return);
    }

    /**
     * @param EventArgs $args
     * @return array
     */
    public function onFilterItemStart(EventArgs $args)
    {
        $article = $args->getReturn();

        if ($article['modus'] == 4 && !empty($article['customProductHash'])) {
            $article['swag_custom_product_original_mode'] = 4;
            $article['modus'] = 0;
        }

        return $article;
    }

    /**
     * @param EventArgs $args
     * @return array
     */
    public function onFilterItemEnd(EventArgs $args)
    {
        $article = $args->getReturn();

        if (isset($article['swag_custom_product_original_mode']) && $article['swag_custom_product_original_mode'] == 4) {
            $article['modus'] = 4;
        }
        return $article;
    }

    /**
     * Updates taxes of custom products options and values.
     */
    public function onUpdateArticle()
    {
        /** @var BasketManager $basketManager */
        $basketManager = $this->container->get('custom_products.basket_manager');
        $basketManager->updateBasketTaxes();
    }

    /**
     * @param EventArgs $args
     * @return bool|null
     */
    public function addArticle(EventArgs $args)
    {
        $orderNumber = $args->get('id');
        $quantity = $args->get('quantity');
        $hash = $this->container->get('front')->Request()->get('customProductsHash');

        /** @var CustomProductsService $customProductsService */
        $customProductsService = $this->container->get('custom_products.service');

        if (!$hash || !$customProductsService->isCustomProduct($orderNumber)) {
            return null;
        }

        $contextService = $this->container->get('shopware_storefront.context_service');
        $context = $contextService->getShopContext();
        $storeFrontService = $this->container->get('shopware_storefront.product_service');
        $product = $storeFrontService->get($orderNumber, $context);

        /** @var BasketManager $basketManager */
        $basketManager = $this->container->get('custom_products.basket_manager');
        $basketId = $basketManager->addToBasket($product, $hash, $quantity);

        $this->container->get('template')->assign('lastInsertedCustomProductBasketId', $basketId);

        // we return true because we need to stop the sAddArticle action.
        return true;
    }

    /**
     * @param HookArgs $args
     */
    public function afterDeleteArticle(HookArgs $args)
    {
        $basketId = $args->get('id');

        /** @var BasketManager $basketManager */
        $basketManager = $this->container->get('custom_products.basket_manager');
        $basket = $basketManager->readBasketPosition($basketId);
        $hash = $basket['swag_custom_products_configuration_hash'];
        $basketManager->deleteFromBasket($hash);
    }

    /**
     * @param EventArgs $args
     * @return string
     */
    public function getBasketSqlQuery(EventArgs $args)
    {
        $sql = $args->getReturn();
        $search = 's_order_basket_attributes.attribute6 as ob_attr6';
        $replace = 's_order_basket_attributes.attribute6 as ob_attr6,
                    s_order_basket_attributes.swag_custom_products_configuration_hash as customProductHash,
                    s_order_basket_attributes.swag_custom_products_once_price as customProductIsOncePrice,
                    s_order_basket_attributes.swag_custom_products_mode as customProductMode';

        $sql = str_replace($search, $replace, $sql);

        return $sql;
    }

    /**
     * @param HookArgs $args
     * @return mixed
     */
    public function getBasket(HookArgs $args)
    {
        /** @var Registry $converterRegistry */
        $converterRegistry = $this->container->get('custom_products.data_converter.registry');

        $basket = $args->getReturn();
        $content = $basket['content'];

        /** @var HashManager $hashManager */
        $hashManager = $this->container->get('custom_products.hash_manager');
        /** @var CustomProductsService $customProductsService */
        $customProductsService = $this->container->get('custom_products.service');

        foreach ($content as &$basketPosition) {
            if ((int)$basketPosition['customProductMode'] != 1) {
                continue;
            }

            $configuration = $hashManager->findConfigurationByHash($basketPosition['customProductHash']);

            $options = [];
            foreach ($configuration as $key => $optionConfig) {
                if ($key == 'custom_product_created_at') {
                    continue;
                }

                $option = $customProductsService->getOptionById($key, $configuration, true);

                /** @var ConverterInterface $converter */
                $converter = $converterRegistry->get($option['type']);
                $options[] = $converter->convertBasketData($option, $optionConfig);
            }

            $basketPosition['custom_product_adds'] = $options;

            $basketPosition['custom_product_prices'] = $this->getPrice(
                $options,
                $configuration,
                $basketPosition['ordernumber'],
                $basketPosition['quantity']
            );
        }

        $basket['content'] = $content;

        return $basket;
    }

    /**
     * @param $options
     * @param $configuration
     * @param string $number
     * @param $quantity
     * @return array
     */
    private function getPrice($options, $configuration, $number, $quantity)
    {
        $calculator = new Calculator($this->container, $this->container->get('custom_products.template_service'));
        $result = $calculator->calculate($options, $configuration, $number, $quantity, true);

        return [
            'quantity' => $quantity,
            // price of the baseProduct
            'basePrice' => $result['basePrice'],
            // price of the surcharges
            'onlySurcharges' => ($result['totalPriceSurcharges'] + $result['totalPriceOnce']),
            // price of one Custom Product with surcharges
            'customProduct' => ($result['totalPriceSurcharges'] + $result['totalPriceOnce'] + $result['basePrice']),
            // price of (1 x (QUANTITY)) CustomProduct's
            'total' => (($result['totalPriceSurcharges'] * $quantity) + $result['totalPriceOnce']) + ($result['basePrice'] * $quantity),
            // price of the Surcharge x (QUANTITY)
            'surchargesTotal' => (($result['totalPriceSurcharges'] * $quantity) + $result['totalPriceOnce'])
        ];
    }

    /**
     * @return float|false
     */
    private function getCustomProductSurchargesAmount()
    {
        $query = $this->container->get('dbal_connection')->createQueryBuilder();
        $query->select(['SUM(quantity*(floor(basket.price * 100 + .55)/100))']);
        $query->from('s_order_basket', 'basket');
        $query->innerJoin('basket', 's_order_basket_attributes', 'attribute', 'attribute.basketID = basket.id');
        $query->andWhere('basket.modus = 4');
        $query->andWhere('attribute.swag_custom_products_configuration_hash IS NOT NULL');
        $amount = $query->execute()->fetch(\PDO::FETCH_COLUMN);
        return $amount;
    }
}
