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
use Shopware\Components\DependencyInjection\Container;
use ShopwarePlugins\SwagCustomProducts\Structs\OptionStruct;

class CustomProductsService
{
    /** @var Container $container */
    private $container;

    /** @var Connection */
    private $connection;

    /**
     * CustomProductsService constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->connection = $this->container->get('dbal_connection');
    }

    /**
     * Possible inputs:
     * A product order number
     *      $this->isCustomProduct($Product_ORDERNUMBER)
     *
     * A basket id with isBasketId = true
     *      $this->isCustomProduct($BasketId, true)
     *
     * @param string $identifier
     * @param boolean $isBasketId
     * @return bool
     */
    public function isCustomProduct($identifier, $isBasketId = false)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        if ($isBasketId) {
            $result = $queryBuilder->select('swag_custom_products_configuration_hash')
                ->from('s_order_basket_attributes', 'attr')
                ->where('attr.basketID = :basketId')
                ->setParameter('basketId', $identifier)
                ->execute()
                ->fetchColumn();

            return !empty($result);
        }

        /** @var \Shopware_Components_Modules $modules */
        $modules = $this->container->get('modules');
        $identifier = $modules->Articles()->sGetArticleIdByOrderNumber($identifier);

        $result = $queryBuilder->select('article_id')
            ->from('s_plugin_custom_products_template_product_relation')
            ->where('article_id = :id')
            ->setParameter('id', $identifier)
            ->execute()
            ->fetchColumn();

        return !empty($result);
    }

    /**
     * @param integer $optionId
     * @param array $configuration
     * @param boolean $basketCalculation
     * @return array
     */
    public function getOptionById($optionId, array $configuration, $basketCalculation = false)
    {
        return array_shift($this->getOptionList([$optionId], $configuration, $basketCalculation));
    }

    /**
     * @param array $configuration
     * @return OptionStruct[]
     */
    public function getOptionsByConfiguration(array $configuration)
    {
        $optionIds = array_keys($configuration);
        $optionList = $this->getOptionList($optionIds, $configuration);

        return $this->createOptionStructs($optionList);
    }

    /**
     * @param int $productId
     * @return bool
     */
    public function checkForRequiredOptions($productId)
    {
        /** @var TemplateService $templateService */
        $templateService = $this->container->get('custom_products.template_service');
        $customProductTemplate = $templateService->getTemplateByProductId($productId);

        if (!$customProductTemplate) {
            return false;
        }

        foreach ($customProductTemplate['options'] as $option) {
            if ($option['required']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param integer[] $ids
     * @param array $configuration
     * @param boolean $basketCalculation
     * @return array
     */
    private function getOptionList(array $ids, array $configuration, $basketCalculation = false)
    {
        /** @var TemplateService $templateService */
        $templateService = $this->container->get('custom_products.template_service');

        $queryBuilder = $this->connection->createQueryBuilder();

        $optionList = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_option', 'opts')
            ->where('id IN (:ids)')
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($optionList as &$option) {
            $option['prices'] = $templateService->getPrices($option['id']);
            $option = $templateService->enrich($option, $basketCalculation);

            if (!$option['could_contain_values']) {
                $option['values'] = [];
                continue;
            }

            $queryBuilder = $this->connection->createQueryBuilder();
            $valueIds = array_values($configuration[$option['id']]);

            $option['values'] = $queryBuilder->select('*')
                ->from('s_plugin_custom_products_value')
                ->where('id IN (:ids)')
                ->andWhere('option_id = :optionId')
                ->setParameter('ids', $valueIds, Connection::PARAM_INT_ARRAY)
                ->setParameter('optionId', $option['id'])
                ->execute()
                ->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($option['values'] as &$value) {
                $value['prices'] = $templateService->getPrices(null, $value['id']);
                $value = $templateService->enrich($value, $basketCalculation);
            }
        }

        return $optionList;
    }

    /**
     * @param array $options
     * @return OptionStruct[]
     */
    private function createOptionStructs(array $options)
    {
        $optionList = [];
        foreach ($options as $option) {
            $optionStruct = new OptionStruct();
            $optionList[] = $optionStruct->fromArray($option);
        }

        return $optionList;
    }
}
