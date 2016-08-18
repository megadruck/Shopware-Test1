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

namespace ShopwarePlugins\SwagCustomProducts\Components\OrderNumberValidation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class OrderNumberValidationService
{
    /** @var Connection $connection */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Validates the given order number on uniqueness
     *
     * @param string $orderNumber
     * @throws OrderNumberUsedByOptionException
     * @throws OrderNumberUsedByProductException
     * @throws OrderNumberUsedByValueException
     */
    public function validate($orderNumber)
    {
        $productOrderNumber = $this->checkForProductOrderNumber($orderNumber);
        if ($productOrderNumber) {
            throw new OrderNumberUsedByProductException();
        }

        $optionId = $this->checkForOptionOrderNumbers($orderNumber);
        if ($optionId) {
            throw new OrderNumberUsedByOptionException($optionId);
        }

        $valueId = $this->checkForValueOrderNumber($orderNumber);
        if ($valueId) {
            throw new OrderNumberUsedByValueException($valueId);
        }
    }

    /**
     * Checks if the given order number is already used by a product.
     *
     * @param string $orderNumber
     * @return string|false
     */
    private function checkForProductOrderNumber($orderNumber)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->connection->createQueryBuilder();
        $builder->select('product.ordernumber')
            ->from('s_articles_details', 'product')
            ->where('product.ordernumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber);

        return $builder->execute()->fetchColumn();
    }

    /**
     * Checks if the given order number is already used by an option.
     *
     * @param string $orderNumber
     * @return integer|false
     */
    private function checkForOptionOrderNumbers($orderNumber)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->connection->createQueryBuilder();
        $builder->select('cpOption.id')
            ->from('s_plugin_custom_products_option', 'cpOption')
            ->where('cpOption.ordernumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber);

        return (int) $builder->execute()->fetchColumn();
    }

    /**
     * Checks if the given order number is already used by a value.
     *
     * @param string $orderNumber
     * @return integer|false
     */
    private function checkForValueOrderNumber($orderNumber)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->connection->createQueryBuilder();
        $builder->select('cpValue.id')
            ->from('s_plugin_custom_products_value', 'cpValue')
            ->where('cpValue.ordernumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber);

        return (int) $builder->execute()->fetchColumn();
    }
}
