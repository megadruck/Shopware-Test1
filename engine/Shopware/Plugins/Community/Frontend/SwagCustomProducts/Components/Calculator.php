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

namespace ShopwarePlugins\SwagCustomProducts\Components;

use Shopware\Bundle\StoreFrontBundle\Struct\Country;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Tax;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Customer\Address;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TemplateService;

class Calculator
{
    // CustomProduct modes as constants
    const MODE_OPTION = 2;
    const MODE_VALUE = 3;

    // constants for calculation
    const DEFAULT_SURCHARGE = 0.00;

    /** @var Container */
    private $container;

    /** @var float */
    private $basePrice;

    /** @var  float */
    private $surchargesArray;

    /** @var array */
    private $onceSurchargesArray;

    /** @var float */
    private $totalOnceSurcharges;

    /** @var float */
    private $totalSurcharges;

    /** @var ContextService $contextService */
    private $contextService;

    /** @var TemplateService $templateService */
    private $templateService;

    /**
     * @param Container $container
     * @param TemplateService $templateService
     */
    public function __construct(Container $container, TemplateService $templateService)
    {
        $this->container = $container;
        $this->templateService = $templateService;
        $this->totalSurcharges = self::DEFAULT_SURCHARGE;
        $this->totalOnceSurcharges = self::DEFAULT_SURCHARGE;
        $this->surchargesArray = [];
        $this->onceSurchargesArray = [];
        $this->basePrice = self::DEFAULT_SURCHARGE;

        $this->contextService = $this->container->get('shopware_storefront.context_service');
    }

    /**
     * @param array $options
     * @param array $configuration
     * @param string $productNumber
     * @param int $quantity
     * @param bool $basketCalculation
     * @return array
     */
    public function calculate(array $options, array $configuration, $productNumber, $quantity = 1, $basketCalculation = false)
    {
        $this->basePrice = $this->getProductPrice($productNumber, $quantity, $basketCalculation);

        $this->iterate($options, $configuration);

        $unitPrice = $this->totalSurcharges + $this->basePrice;
        $total = $unitPrice * $quantity + $this->totalOnceSurcharges ;

        $result = [
            'totalPriceSurcharges' => $this->totalSurcharges,
            'totalPriceOnce' => $this->totalOnceSurcharges,
            'surcharges' => $this->surchargesArray,
            'onceprices' => $this->onceSurchargesArray,
            'basePrice' => $this->basePrice,
            'totalUnitPrice' => $unitPrice,
            'total' => $total
        ];

        return $result;
    }

    /**
     * calculate the price
     *
     * @param array $surcharge
     * @param ShopContextInterface $context
     * @param boolean $basketCalculation
     * @return array
     * @throws \Exception
     */
    public function getPrice($surcharge, ShopContextInterface $context, $basketCalculation = false)
    {
        $taxId = $surcharge['tax_id'];
        $isTaxFreeDelivery = $this->isTaxFreeDelivery();

        $price = $surcharge['surcharge'];

        if (empty($price)) {
            return [
                'tax_id' => $taxId,
                'netPrice' => self::DEFAULT_SURCHARGE,
                'surcharge' => self::DEFAULT_SURCHARGE,
                'tax' => 0,
                'isTaxFreeDelivery' => $isTaxFreeDelivery
            ];
        }

        // if there no TaxId throw a Exception.
        if (empty($taxId)) {
            throw new \Exception('Cannot proceed without a tax ID');
        }

        $customerGroup = $context->getCurrentCustomerGroup();

        if ($customerGroup->useDiscount() && $customerGroup->getPercentageDiscount()) {
            $price = $price - ($price / 100 * $customerGroup->getPercentageDiscount());
        }

        $price = $price * $context->getCurrency()->getFactor();
        $gross = $price * (100 + $this->getTaxRateByTaxId($taxId)) / 100;

        $result = [
            'netPrice' => $price,
            'surcharge' => $price,
            'tax_id' => $taxId,
            'tax' => round($gross - $price, 2),
            'isTaxFreeDelivery' => $isTaxFreeDelivery
        ];

        if ($basketCalculation === false) {
            $isTaxFreeDelivery = false;
        }

        if ($isTaxFreeDelivery || !$customerGroup->displayGrossPrices()) {
            return $result;
        }

        $result['surcharge'] = $gross;

        return $result;
    }

    /**
     * This method add the calculated prices to the properties and amount
     * them for displaying it in the Price Overview and the basket
     *
     * @param boolean $isOncePrice
     * @param string $name
     * @param float $surcharge
     * @param $netPrice
     * @param boolean $isParent
     * @param boolean $hasParent
     */
    private function add(
        $isOncePrice,
        $name,
        $surcharge,
        $netPrice,
        $isParent = false,
        $hasParent = false
    ) {
        if (!$isParent && empty($surcharge)) {
            return;
        }

        $option = [
            'name' => $name,
            'price' => $surcharge,
            'netPrice' => $netPrice,
            'tax' => round($surcharge - $netPrice, 2),
            'isParent' => $isParent,
            'hasParent' => $hasParent,
            'hasSurcharge' => !empty($surcharge)
        ];

        if ($isOncePrice) {
            $this->onceSurchargesArray[] = $option;
            $this->totalOnceSurcharges += round($surcharge, 2);
            return;
        }

        $this->surchargesArray[] = $option;
        $this->totalSurcharges += round($surcharge, 2);
    }

    /**
     * Iterate over all options and values to add them to the calculation.
     *
     * @param array $options
     * @param array $configuration
     */
    private function iterate(array $options, array $configuration)
    {
        foreach ($configuration as $optionId => $config) {
            $option = $this->find($optionId, $options);

            if (!$option) {
                continue;
            }

            if (!$option['could_contain_values']) {
                $this->add($option['is_once_surcharge'], $option['name'], $option['surcharge'], $option['netPrice']);
                continue;
            }

            // helper vars
            $optionIsAddedToOncePrice = false;
            $optionIsAddedToPrice = false;

            foreach ($config as $val) {
                $value = $this->find($val, $option['values']);

                if (!$value) {
                    continue;
                }

                list($optionIsAddedToOncePrice, $optionIsAddedToPrice) = $this->handleValues(
                    $value,
                    $optionIsAddedToOncePrice,
                    $option,
                    $optionIsAddedToPrice
                );
            }

            if ($option['is_once_surcharge'] && !$optionIsAddedToOncePrice && !empty($option['surcharge'])) {
                $this->add($option['is_once_surcharge'], $option['name'], $option['surcharge'], $option['netPrice']);
            }

            if (!$option['is_once_surcharge'] && !$optionIsAddedToPrice && !empty($option['surcharge'])) {
                $this->add($option['is_once_surcharge'], $option['name'], $option['surcharge'], $option['netPrice']);
            }
        }
    }

    /**
     * @param integer $id
     * @param array $items
     * @return mixed
     */
    private function find($id, array $items)
    {
        foreach ($items as $item) {
            if ($id == $item['id']) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param array $value
     * @param boolean $optionIsAddedToOncePrice
     * @param array $option
     * @param boolean $optionIsAddedToPrice
     * @return array
     */
    private function handleValues(array $value, $optionIsAddedToOncePrice, array $option, $optionIsAddedToPrice)
    {
        if (empty($value['surcharge'])) {
            return [
                $optionIsAddedToOncePrice,
                $optionIsAddedToPrice
            ];
        }

        if ($value['is_once_surcharge']) {
            if (!$optionIsAddedToOncePrice) {
                if ($option['is_once_surcharge']) {
                    $this->add(true, $option['name'], $option['surcharge'], $option['netPrice'], true);
                } else {
                    $this->add(true, $option['name'], self::DEFAULT_SURCHARGE, self::DEFAULT_SURCHARGE, true);
                }

                $optionIsAddedToOncePrice = true;
            }

            $this->add($value['is_once_surcharge'], $value['name'], $value['surcharge'], $value['netPrice'], false);

            return [
                $optionIsAddedToOncePrice,
                $optionIsAddedToPrice
            ];
        }

        if (!$optionIsAddedToPrice) {
            if ($option['is_once_surcharge']) {
                $this->add(false, $option['name'], self::DEFAULT_SURCHARGE, self::DEFAULT_SURCHARGE, true, false);
            } else {
                $this->add(false, $option['name'], $option['surcharge'], $option['netPrice'], true, false);
            }

            $optionIsAddedToPrice = true;
        }

        $this->add($value['is_once_surcharge'], $value['name'], $value['surcharge'], $value['netPrice'], true, false);

        return [
            $optionIsAddedToOncePrice,
            $optionIsAddedToPrice
        ];
    }

    /**
     * get the TaxId by customProductMode and (Option/Value - ID)
     *
     * @param int $customProductMode
     * @param int $id
     * @return int|string
     */
    public function getTaxId($customProductMode, $id)
    {
        if ($customProductMode == self::MODE_OPTION) {
            $option = $this->templateService->getOptionById($id);
            return $option['tax_id'];
        }

        if ($customProductMode == self::MODE_VALUE) {
            $value = $this->templateService->getValueById($id);
            return $value['tax_id'];
        }
    }

    /**
     * @param integer $id
     * @return float
     */
    public function getTaxRateByTaxId($id)
    {
        if (!$id) {
            return 0;
        }

        /** @var Tax $tax */
        $tax = $this->contextService->getProductContext()->getTaxRule($id);

        return $tax->getTax();
    }

    /**
     * @param string $number
     * @param int $quantity
     * @param bool $basketCalculation
     * @return float
     */
    private function getProductPrice($number, $quantity, $basketCalculation = false)
    {
        $context = $this->container->get('shopware_storefront.context_service')->getShopContext();

        /** @var ListProduct $product */
        $product = $this->container->get('shopware_storefront.list_product_service')->get($number, $context);

        $price = $product->getVariantPrice();

        foreach ($product->getPrices() as $graduation) {
            if ($graduation->getFrom() <= $quantity && ($graduation->getTo() >= $quantity || $graduation->getTo() === null)) {
                $price = $graduation;
                break;
            }
        }

        $taxFreeDelivery = $this->isTaxFreeDelivery() && $basketCalculation;

        if ($taxFreeDelivery || !$context->getCurrentCustomerGroup()->displayGrossPrices()) {
            return $this->calculateProductNetPrice($price->getRule()->getPrice(), $context);
        }

        return round($price->getCalculatedPrice(), 2);
    }

    /**
     * @param float $price
     * @param ShopContextInterface $context
     * @return float
     */
    private function calculateProductNetPrice($price, ShopContextInterface $context)
    {
        $customerGroup = $context->getCurrentCustomerGroup();
        if ($customerGroup->useDiscount() && $customerGroup->getPercentageDiscount()) {
            $price = $price - ($price / 100 * $customerGroup->getPercentageDiscount());
        }
        $price = $price * $context->getCurrency()->getFactor();
        return round($price, 2);
    }

    /**
     * @return bool
     */
    private function isTaxFreeDelivery()
    {
        $deliveryAddress = $this->getDeliveryAddress();

        if (!$deliveryAddress) {
            return false;
        }

        $deliveryCountry = $deliveryAddress->getCountry();

        if (!$deliveryCountry) {
            return false;
        }

        if ($deliveryCountry->getTaxFree()) {
            return true;
        }

        return ($deliveryCountry->getTaxFreeUstId() && !empty($deliveryAddress->getVatId()));
    }

    /**
     * @return null|Address
     */
    private function getDeliveryAddress()
    {
        $session = $this->container->get('session');

        //customer switched checkout billing address
        if ($id = $session->offsetGet('checkoutShippingAddressId')) {
            return $this->container->get('models')->find('Shopware\Models\Customer\Address', $id);
        }

        //customer isn't logged in
        if (!($id = $session->offsetGet('sUserId'))) {
            return null;
        }

        $customer = $this->container->get('models')->find('Shopware\Models\Customer\Customer', $id);

        return $customer->getDefaultShippingAddress();
    }
}
