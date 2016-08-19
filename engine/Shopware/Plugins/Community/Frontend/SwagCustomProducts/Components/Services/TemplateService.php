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
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\MediaServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Media;
use ShopwarePlugins\SwagCustomProducts\Components\Calculator;
use Shopware\Components\DependencyInjection\Container;
use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\FileTypeWhitelist;

class TemplateService
{
    /** @var MediaServiceInterface $mediaService */
    private $mediaService;

    /** @var ContextServiceInterface $contextService */
    private $contextService;

    /**@var Connection $connection */
    private $connection;

    /** @var Calculator $calculator */
    private $calculator;

    /**
     * @param MediaServiceInterface $mediaService
     * @param ContextServiceInterface $contextService
     * @param Connection $connection
     * @param Container $container
     */
    public function __construct(
        MediaServiceInterface $mediaService,
        ContextServiceInterface $contextService,
        Connection $connection,
        Container $container
    ) {
        $this->mediaService = $mediaService;
        $this->contextService = $contextService;
        $this->connection = $connection;
        $this->calculator = new Calculator($container, $this);
    }

    /**
     * @param integer $productId
     * @param boolean $enrichTemplate
     * @return array | null
     */
    public function getTemplateByProductId($productId, $enrichTemplate = true)
    {
        $templateId = $this->getTemplateId($productId);
        if (!$templateId) {
            return null;
        }

        $template = $this->getTemplate($templateId);
        if (!$template) {
            return null;
        }

        if (!$enrichTemplate) {
            return $template;
        }

        return $this->enrichTemplate($template, $templateId);
    }

    /**
     * @param integer|string $productId
     * @return array|boolean
     */
    private function getTemplateId($productId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('DISTINCT template_id')
            ->from('s_plugin_custom_products_template_product_relation')
            ->where('article_id = :id')
            ->setParameter('id', $productId)
            ->execute()
            ->fetch(\PDO::FETCH_COLUMN);
    }

    /**
     * @param integer|string $templateId
     * @return array|boolean
     */
    private function getTemplate($templateId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from('s_plugin_custom_products_template')
            ->where('id = :id')
            ->setParameter(':id', $templateId)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param integer $templateId
     * @return array
     */
    private function getOptions($templateId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from('s_plugin_custom_products_option')
            ->where('template_id = :id')
            ->setParameter('id', $templateId)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param array $optionIds
     * @return array
     */
    private function getValues(array $optionIds)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('option_id', 'value.*')
            ->from('s_plugin_custom_products_value', 'value')
            ->where('option_id IN (:ids)')
            ->setParameter(':ids', $optionIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);
    }

    /**
     * @param array $optionIds
     * @return array
     */
    private function getOptionPrices(array $optionIds)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('option_id', 'price.*')
            ->from('s_plugin_custom_products_price', 'price')
            ->where('option_id IN (:ids)')
            ->setParameter(':ids', $optionIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);
    }

    /**
     * @param array $valueIds
     * @return array
     */
    private function getValuePrices(array $valueIds)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('value_id', 'price.*')
            ->from('s_plugin_custom_products_price', 'price')
            ->where('value_id IN (:ids)')
            ->setParameter(':ids', $valueIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);
    }

    /**
     * @param array $prices
     * @param integer|string $customerGroupId
     * @param integer|string $customerGroupFallbackId
     * @return array
     */
    private function getCurrentPrice(array $prices, $customerGroupId, $customerGroupFallbackId)
    {
        $defaultPrice = null;

        foreach ($prices as $price) {
            if ($price['customer_group_id'] == $customerGroupId) {
                return $price;
            }

            if ($price['customer_group_id'] == $customerGroupFallbackId) {
                $defaultPrice = $price;
            }
        }

        return $defaultPrice;
    }

    /**
     * @param array $values
     * @param array $valuePrices
     * @param string|integer $customerGroupId
     * @param string|integer $fallbackId
     * @param Media[] $medias
     * @return array
     */
    public function enrichValues(array $values, array $valuePrices, $customerGroupId, $fallbackId, array $medias)
    {
        foreach ($values as &$row) {
            foreach ($row as &$value) {
                $value['prices'] = $valuePrices[$value['id']];
                $value = array_merge($value, $this->calculator->getPrice(
                    $this->getCurrentPrice($value['prices'], $customerGroupId, $fallbackId),
                    $this->contextService->getShopContext(),
                    false
                ));

                $value['image'] = json_decode(json_encode($medias[$value['media_id']]), true);
            }
        }

        return $values;
    }

    /**
     * @param array $options
     * @param array $values
     * @param array $optionPrices
     * @param string|integer $customerGroupId
     * @param string|integer $fallbackId
     * @return array
     */
    public function enrichOptions(array $options, array $values, array $optionPrices, $customerGroupId, $fallbackId)
    {
        foreach ($options as &$option) {
            $option = $this->applyMimeTypes($option);
            $option['prices'] = $optionPrices[$option['id']];
            $option = array_merge($option, $this->calculator->getPrice(
                $this->getCurrentPrice($option['prices'], $customerGroupId, $fallbackId),
                $this->contextService->getShopContext(),
                false
            ));

            if (!$option['could_contain_values']) {
                continue;
            }

            $option['values'] = array_key_exists($option['id'], $values) ? $values[$option['id']] : [];
        }

        return $options;
    }

    /**
     * @param array $option
     * @return array
     */
    private function applyMimeTypes(array $option)
    {
        if (!in_array($option['type'], ['fileupload', 'imageupload'])) {
            return $option;
        }

        if($option['type'] == 'fileupload') {
            $option['allowed_mime_types'] = json_encode(FileTypeWhitelist::$mimeTypeWhitelist['image']);

            return $option;
        }

        $option['allowed_mime_types'] = json_encode(FileTypeWhitelist::$mimeTypeWhitelist['file']);
        return $option;
    }

    /**
     * @param string $internalName
     * @return boolean
     */
    public function isInternalNameAssigned($internalName)
    {
        $query = $this->connection->createQueryBuilder();

        $result = $query->select('id')
            ->from('s_plugin_custom_products_template', 'template')
            ->where('internal_name = :internalName')
            ->setParameter(':internalName', $internalName)
            ->execute()
            ->fetchColumn();

        if ($result) {
            return false;
        }

        return true;
    }

    /**
     * @param integer $templateId
     * @return array
     */
    public function getOptionsByTemplateId($templateId)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $options = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_option')
            ->where('template_id = :templateId')
            ->setParameter('templateId', $templateId)
            ->orderBy('position')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($options as &$option) {
            $option['prices'] = $this->getPrices($option['id']);
            $option = $this->enrich($option);

            if (!$option['could_contain_values']) {
                continue;
            }

            $option['values'] = $this->getValuesByOptionId($option['id'], $option['type']);
        }

        return $options;
    }

    /**
     * @param integer $optionId
     * @param string $type
     * @return array
     */
    private function getValuesByOptionId($optionId, $type)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $values = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_value')
            ->where('option_id = :optionId')
            ->setParameter('optionId', $optionId)
            ->orderBy('position')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($values as &$value) {
            $value['prices'] = $this->getPrices(null, $value['id']);
            $value = $this->enrich($value);

            if ($type == 'imageselect' && $value['media_id']) {
                $value['image'] = $this->getMediaById($value['media_id']);
            }
        }

        return $values;
    }

    /**
     * Reads the prices by optionId OR by valueId.
     * it is important to make only one indication!
     *
     * @param null|integer $optionId
     * @param null|integer $valueId
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getPrices($optionId = null, $valueId = null)
    {
        if (($optionId && $valueId) || (!$optionId && !$valueId)) {
            throw new \Exception('optionId OR valueId is needed!');
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('*')->from('s_plugin_custom_products_price');

        if ($optionId) {
            $queryBuilder->where('option_id = :optionId')
                ->setParameter('optionId', $optionId);
        }

        if ($valueId) {
            $queryBuilder->where('value_id = :valueId')
                ->setParameter('valueId', $valueId);
        }

        return $queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param integer $id
     * @param boolean $basketCalculation
     * @return array|null
     */
    public function getOptionById($id, $basketCalculation = false)
    {
        if (!$id) {
            return null;
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $option = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_option')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        $option['prices'] = $this->getPrices($id);

        return $this->enrich($option, $basketCalculation);
    }

    /**
     * @param integer $id
     * @param boolean $basketCalculation
     * @return array|null
     */
    public function getValueById($id, $basketCalculation = false)
    {
        if (!$id) {
            return null;
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $values = $queryBuilder->select('*')
            ->from('s_plugin_custom_products_value')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        $values['prices'] = $this->getPrices(null, $id);

        return $this->enrich($values, $basketCalculation);
    }

    /**
     * @param integer $mediaId
     * @return array
     */
    private function getMediaById($mediaId)
    {
        $context = $this->contextService->getShopContext();

        return json_decode(json_encode($this->mediaService->get($mediaId, $context)), true);
    }

    /**
     * Enrich objects with prices and tax Ids
     *
     * @param array $data
     * @param bool $basketCalculation
     * @return array
     * @throws \Exception
     */
    public function enrich(array $data, $basketCalculation = false)
    {
        if (empty($data['prices'])) {
            return $data;
        }

        $customerGroup = $this->contextService->getShopContext()->getCurrentCustomerGroup();

        $price = $this->getRightPrice($data['prices'], $customerGroup->getId());

        if (empty($price)) {
            return $data;
        }

        $price = $this->calculator->getPrice($price, $this->contextService->getShopContext(), $basketCalculation);
        $data = array_merge($data, $price);

        return $data;
    }

    /**
     * @param array $prices
     * @param integer $customerGroupId
     * @return null|array
     */
    private function getRightPrice(array $prices, $customerGroupId)
    {
        $fallbackId = $this->contextService->getShopContext()->getFallbackCustomerGroup()->getId();

        $defaultPrice = null;

        foreach ($prices as $price) {
            if ($price['customer_group_id'] == $customerGroupId) {
                return $price;
            }

            if ($price['customer_group_id'] == $fallbackId) {
                $defaultPrice = $price;
            }
        }

        return $defaultPrice;
    }

    /**
     * @param $template
     * @param $templateId
     * @return mixed
     */
    public function enrichTemplate($template, $templateId)
    {
        $shopContext = $this->contextService->getShopContext();
        $customerGroupId = $shopContext->getCurrentCustomerGroup()->getId();
        $fallbackId = $shopContext->getFallbackCustomerGroup()->getId();
        $mediaIds = [];

        if ($template['media_id']) {
            $mediaIds[] = $template['media_id'];
        }

        $options = $this->getOptions($templateId);
        $optionIds = array_column($options, 'id');

        $valueIds = [];
        $values = $this->getValues($optionIds);

        foreach ($values as $value) {
            $valueIds = array_merge(array_column($value, 'id', 'id'), $valueIds);
            $mediaId = array_column($value, 'media_id');
            if (!$mediaId) {
                continue;
            }

            $mediaIds = array_merge($mediaIds, $mediaId);
        }

        $medias = $this->mediaService->getList($mediaIds, $shopContext);
        $optionPrices = $this->getOptionPrices($optionIds);
        $valuePrices = $this->getValuePrices($valueIds);
        $values = $this->enrichValues($values, $valuePrices, $customerGroupId, $fallbackId, $medias);
        $template['options'] = $this->enrichOptions($options, $values, $optionPrices, $customerGroupId, $fallbackId);

        if ($template['media_id']) {
            $template['media'] = json_decode(json_encode($medias[$template['media_id']]), true);
        }
        return $template;
    }
}
