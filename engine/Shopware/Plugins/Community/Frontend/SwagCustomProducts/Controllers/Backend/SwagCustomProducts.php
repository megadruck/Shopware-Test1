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

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Components\Model\ModelManager;
use Shopware\CustomModels\SwagCustomProducts\Template;
use ShopwarePlugins\SwagCustomProducts\Components\OrderNumberValidation\OrderNumberUsedByOptionException;
use ShopwarePlugins\SwagCustomProducts\Components\OrderNumberValidation\OrderNumberUsedByProductException;
use ShopwarePlugins\SwagCustomProducts\Components\OrderNumberValidation\OrderNumberUsedByValueException;
use ShopwarePlugins\SwagCustomProducts\Components\OrderNumberValidation\OrderNumberValidationService;
use ShopwarePlugins\SwagCustomProducts\Components\Services\Migration;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TemplateService;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TranslationService;
use ShopwarePlugins\SwagCustomProducts\Components\Types\TypeInterface;

class Shopware_Controllers_Backend_SwagCustomProducts extends Shopware_Controllers_Backend_Application
{
    /** @var string */
    protected $model = 'Shopware\CustomModels\SwagCustomProducts\Template';

    /** @var string */
    protected $alias = 'template';

    /** @var array */
    protected $sortFields = ['internalName', 'displayName'];

    /** @var array */
    protected $filterFields = ['internalName', 'displayName'];

    /**
     * Overriding to add custom selections to the detail query.
     *
     * @inheritdoc
     */
    protected function getDetailQuery($id)
    {
        $builder = parent::getDetailQuery($id);

        $builder->leftJoin('template.options', 'options')
            ->leftJoin('options.values', 'values')
            ->leftJoin('options.prices', 'optionPrices')
            ->leftJoin('values.prices', 'valuePrices')
            ->addSelect(['options', 'values', 'optionPrices', 'valuePrices']);

        return $builder;
    }

    /**
     * This is ajax called action for the Backend to get all assigned Products
     * Required request parameters: (templateId, limit, start)
     */
    public function getSelectedArticlesAction()
    {
        $templateId = $this->request->get('templateId');
        $limit = (int)$this->request->getParam('limit');
        $offset = (int)$this->request->getParam('start');
        $dbalConnection = $this->container->get('dbal_connection');

        $queryBuilder = $dbalConnection->createQueryBuilder();
        $total = $queryBuilder->select('COUNT(article_id)')
            ->from('s_plugin_custom_products_template_product_relation')
            ->where('template_id = :templateId')
            ->setParameter('templateId', $templateId)
            ->execute()
            ->fetch(\PDO::FETCH_COLUMN);

        $queryBuilder = $dbalConnection->createQueryBuilder();
        $articleIds = $queryBuilder->select('article_id')
            ->from('s_plugin_custom_products_template_product_relation')
            ->where('template_id = :templateId')
            ->orderBy('article_id')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('templateId', $templateId)
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);

        $queryBuilder = $dbalConnection->createQueryBuilder();
        $orderNumbers = $queryBuilder->select('articleID', 'ordernumber as number')
            ->from('s_articles_details')
            ->where('articleID IN (:ids)')
            ->andWhere('kind = 1')
            ->setParameter(':ids', $articleIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);

        $queryBuilder = $dbalConnection->createQueryBuilder();
        $articles = $queryBuilder->select('id', 'name')
            ->from('s_articles')
            ->where('id IN (:ids)')
            ->setParameter(':ids', $articleIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($articles as &$article) {
            $article['number'] = $orderNumbers[$article['id']][0]['number'];
        }

        $this->view->assign([
            'data' => $articles,
            'total' => $total,
        ]);
    }

    /**
     * This is a ajaxCalled action for the backend to find articles by (name / orderNumber) or all
     * Required request parameters: (query, limit, start)
     */
    public function searchArticleAction()
    {
        $limit = $this->request->getParam('limit');
        $offset = $this->request->getParam('start');
        $searchTerm = $this->request->getParam('query');
        $dbalConnection = $this->container->get('dbal_connection');

        $queryBuilder = $dbalConnection->createQueryBuilder();
        $queryBuilder->select('articles.id, articles.name', 'details.ordernumber as number')
            ->from('s_articles', 'articles')
            ->where('details.kind = 1')
            ->leftJoin('articles', 's_articles_details', 'details', 'articles.id = details.articleID')
            ->orderBy('articles.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($searchTerm) {
            $searchTerm = '%' . $searchTerm . '%';
            $queryBuilder->andWhere('articles.name LIKE :searchTerm')
                ->orWhere('articles.description LIKE :searchTerm')
                ->orWhere('details.ordernumber LIKE :searchTerm');

            $queryBuilder->setParameters([
                'searchTerm' => $searchTerm
            ]);
        }

        $result = $queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC);
        $total = $queryBuilder->select('COUNT(articles.id)')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->execute()
            ->fetch(\PDO::FETCH_COLUMN);

        $this->view->assign([
            'data' => $result,
            'total' => $total
        ]);
    }

    /**
     * this action is to assign a article to the template.
     * Required request parameters: (templateId, articleId)
     */
    public function addArticleAction()
    {
        $templateId = $this->request->get('templateId');
        $articleId = $this->request->get('articleId');

        $queryBuilder = $this->container->get('dbal_connection')->createQueryBuilder();
        $queryBuilder->insert('s_plugin_custom_products_template_product_relation')
            ->values([
                'article_id' => ':articleId',
                'template_id' => ':templateId'
            ])
            ->setParameter('articleId', $articleId)
            ->setParameter('templateId', $templateId)
            ->execute();

        $this->invalidateCache([$articleId]);

        $this->view->assign([
            'success' => true
        ]);
    }

    /**
     * this action is to delete a (article / template) relation
     * Required request parameters: (templateId, articleId)
     */
    public function removeArticleAction()
    {
        $templateId = $this->request->get('templateId');
        $articleId = $this->request->get('articleId');

        $this->invalidateCache([$articleId]);

        $queryBuilder = $this->container->get('dbal_connection')->createQueryBuilder();
        $queryBuilder->delete('s_plugin_custom_products_template_product_relation')
            ->where('article_id = :articleId')
            ->andWhere('template_id = :templateId')
            ->setParameter('articleId', $articleId)
            ->setParameter('templateId', $templateId)
            ->execute();

        $this->view->assign([
            'success' => true
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getDetail($id)
    {
        $data = parent::getDetail($id);

        $data['data']['articles'] = array_map(function ($article) {
            $article['number'] = $article['mainDetail']['number'];
            return $article;
        }, $data['data']['articles']);

        $service = Shopware()->Container()->get('shopware_media.media_service');
        foreach ($data['data']['options'] as &$option) {
            $option['prices'] = $this->convertPrices($option['prices'], false);
            $media = $this->getMedia(array_column($option['values'], 'mediaId'));

            foreach ($option['values'] as &$value) {
                $value['prices'] = $this->convertPrices($value['prices'], false);
                $id = $value['mediaId'];
                if (isset($media[$id])) {
                    $value['value'] = $service->getUrl($media[$id]);
                }
            }
        }

        return $data;
    }

    /**
     * Get the necessary params from request and set it to the template model for saving.
     */
    public function rowEditingAction()
    {
        $templateId = $this->request->getParam('id');
        $displayName = $this->request->getParam('displayName');

        /** @var EntityManager $entityManager */
        $entityManager = $this->get('models');

        try {
            $template = $entityManager->find($this->model, $templateId);

            /** @var Template $template */
            $template->setDisplayName($displayName);

            $entityManager->persist($template);
            $entityManager->flush();

            $this->view->assign([
                'success' => true
            ]);
        } catch (\Exception $ex) {
            $this->view->assign([
                'success' => false,
                'errorMessage' => $ex->getMessage()
            ]);
        }
    }

    /**
     * Overwrite to check the mediaItem
     *
     * @param array $data
     * @return array
     */
    public function save($data)
    {
        if (empty($data['media'])) {
            $data['media'] = null;
        }

        foreach ($data['options'] as &$option) {
            $option['prices'] = $this->convertPrices($option['prices'], true);

            foreach ($option['values'] as &$value) {
                $value['prices'] = $this->convertPrices($value['prices'], true);
            }
        }

        $data['articles'] = $this->getAssociatedArticles($data['id']);

        return parent::save($data);
    }

    /**
     * Delete a single template
     */
    public function deleteTemplateAction()
    {
        $templateId = $this->request->getParam('id');

        if (!$templateId) {
            return;
        }

        /** @var ModelManager $entityManager */
        $entityManager = $this->container->get('models');
        $repository = $entityManager->getRepository($this->model);
        $template = $repository->find($templateId);

        if (!$template) {
            return;
        }

        $entityManager->remove($template);
        $entityManager->flush($template);
    }

    /**
     * Reads all possible Taxes
     */
    public function getTaxesAction()
    {
        /** @var Connection $connection */
        $connection = $this->get('dbal_connection');
        $queryBuilder = $connection->createQueryBuilder();

        $taxes = $queryBuilder->select('id, description')
            ->from('s_core_tax', 'tax')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        $this->view->assign([
            'success' => true,
            'data' => $taxes,
            'total' => count($taxes)
        ]);
    }

    /**
     * Read all customer groups and assign to view
     */
    public function getCustomerGroupsAction()
    {
        /** @var Connection $connection */
        $connection = $this->get('dbal_connection');
        $queryBuilder = $connection->createQueryBuilder();

        $customerGroups = $queryBuilder->select('*')
            ->from('s_core_customergroups')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        $this->view->assign([
            'success' => true,
            'data' => $customerGroups,
            'total' => count($customerGroups)
        ]);
    }

    public function getPluginDataAction()
    {
        /** @var Connection $connection */
        $connection = $this->get('dbal_connection');
        $queryBuilder = $connection->createQueryBuilder();

        $currency = $queryBuilder->select('*')
            ->from('s_core_currencies')
            ->where('standard = 1')
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        $customerGroup = $queryBuilder->select('*')
            ->from('s_core_customergroups')
            ->where('groupkey = :key')
            ->setParameter('key', ContextService::FALLBACK_CUSTOMER_GROUP)
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        $this->view->assign([
            'data' => [
                'currencyData' => $currency,
                'defaultCustomerGroup' => $customerGroup
            ]
        ]);
    }

    /**
     * Action to get all available option types
     */
    public function getOptionTypesAction()
    {
        /** @var \ShopwarePlugins\SwagCustomProducts\Components\Types\TypeFactory $typeFactory */
        $typeFactory = $this->container->get('custom_products.type_factory');

        $this->view->assign([
            'success' => true,
            'data' => $this->prepareTypeForBackend($typeFactory->factory())
        ]);
    }

    /**
     * Validation of the internal name field - the internalName must be unique
     */
    public function validateInternalNameAjaxAction()
    {
        $internalName = $this->Request()->getParam('value');

        /** @var TemplateService $templateService */
        $templateService = $this->get('custom_products.template_service');

        if (!$templateService->isInternalNameAssigned($internalName)) {
            $this->view->assign(['success' => false]);
        } else {
            $this->view->assign(['success' => true]);
        }
    }

    public function cloneTemplateAction()
    {
        $id = $this->request->getParam('id');
        $internalName = $this->request->getParam('internalName');

        /** @var EntityManager $entityManager */
        $entityManager = $this->get('models');
        $template = $entityManager->getRepository('Shopware\CustomModels\SwagCustomProducts\Template')->find($id);

        if (!$template) {
            return;
        }

        $newTemplate = clone $template;

        $newTemplate->setInternalName($internalName);

        $entityManager->persist($newTemplate);
        $entityManager->flush($newTemplate);

        /** @var TranslationService $translationService */
        $translationService = $this->get('custom_products.translation_service');
        $translationService->cloneTranslations($template, $newTemplate);
    }

    public function getBackendLocaleAction()
    {
        $user = Shopware()->Auth()->getIdentity();
        /** @var $locale \Shopware\Models\Shop\Locale */
        $locale = $user->locale;

        $this->view->assign([
            'data' => $locale->getLocale(),
            'success' => true,
        ]);
    }

    /**
     * Validation
     */
    public function validateArticleSelectionAjaxAction()
    {
        $id = (int)$this->Request()->getParam('id');
        $template = $this->productHasTemplate($id);

        if (empty($template)) {
            $this->view->assign(['success' => true]);

            return;
        }

        $this->view->assign([
            'data' => $template,
            'success' => false,
            'total' => 1
        ]);
    }

    /**
     * Reads all groups from the old plugin installation and assign to view
     */
    public function getOldGroupsForMigrationAction()
    {
        /** @var Migration $migrationService */
        $migrationService = $this->container->get('custom_products.migration');

        $groups = $migrationService->getGroups();

        $this->view->assign([
            'data' => $groups,
            'total' => count($groups),
            'success' => true
        ]);
    }

    public function migrateAction()
    {
        $groupId = $this->request->getParam('id');

        /** @var Migration $migrationService */
        $migrationService = $this->container->get('custom_products.migration');
        $result = $migrationService->startMigration($groupId);

        $errors = $migrationService->getErrorLog();

        $this->view->assign([
            'success' => $result,
            'errorLog' => $errors,
            'errorCount' => count($errors)
        ]);
    }

    /**
     * checks if a migration is possible
     */
    public function isMigrationPossibleAction()
    {
        /** @var Migration $migrationService */
        $migrationService = $this->container->get('custom_products.migration');

        $this->view->assign([
            'success' => true,
            'migrationPossible' => $migrationService->isMigrationPossible()
        ]);
    }

    public function saveHideMigrationButtonAction()
    {
        $value = $this->request->getParam('doNotShowAgain');

        if (!$value) {
            return;
        }

        /** @var Migration $migrationService */
        $migrationService = $this->container->get('custom_products.migration');
        $migrationService->saveHideMigrationButton($value);
    }

    /**
     * Validates the given order number for a Custom Products option.
     * Checks for product order numbers, for value order numbers and for other option order numbers
     */
    public function validateOptionOrderNumberAjaxAction()
    {
        $orderNumber = $this->Request()->getParam('orderNumber');

        /** @var OrderNumberValidationService $orderNumberValidationService */
        $orderNumberValidationService = $this->get('custom_products.order_number.validation_service');

        try {
            $orderNumberValidationService->validate($orderNumber);
            $this->View()->assign('success', true);
        } catch (OrderNumberUsedByProductException $e) {
            $this->View()->assign('success', false);
        } catch (OrderNumberUsedByOptionException $e) {
            if ($e->getOptionId() == $this->Request()->getParam('optionId')) {
                $this->View()->assign('success', true);

                return;
            }
            $this->View()->assign('success', false);
        } catch (OrderNumberUsedByValueException $e) {
            $this->View()->assign('success', false);
        }
    }

    /**
     * Validates the given order number for a Custom Products value.
     * Checks for product order numbers, for option order numbers, and for other value order numbers
     */
    public function validateValueOrderNumberAjaxAction()
    {
        $orderNumber = $this->Request()->getParam('orderNumber');

        /** @var OrderNumberValidationService $orderNumberValidationService */
        $orderNumberValidationService = $this->get('custom_products.order_number.validation_service');

        try {
            $orderNumberValidationService->validate($orderNumber);
            $this->View()->assign('success', true);
        } catch (OrderNumberUsedByProductException $e) {
            $this->View()->assign('success', false);
        } catch (OrderNumberUsedByOptionException $e) {
            $this->View()->assign('success', false);
        } catch (OrderNumberUsedByValueException $e) {
            if ($e->getValueId() == $this->Request()->getParam('valueId')) {
                $this->View()->assign('success', true);

                return;
            }
            $this->View()->assign('success', false);
        }
    }

    /**
     * The getList function returns an array of the configured class model.
     * The listing query created in the getListQuery function.
     * The pagination of the listing is handled inside this function.
     *
     * @inheritdoc
     */
    protected function getList($offset, $limit, $sort = [], $filter = [], array $wholeParams = [])
    {
        $data = parent::getList($offset, $limit, $sort, $filter, $wholeParams);

        $ids = array_column($data['data'], 'id');
        $productCount = $this->getProductCount($ids);
        $optionCount = $this->getOptionCount($ids);

        foreach ($data['data'] as &$row) {
            $id = $row['id'];
            $row = array_merge($row, ['productCount' => 0, 'optionCount' => 0]);

            if (!empty($productCount[$id])) {
                $row['productCount'] = $productCount[$id];
            }
            if (!empty($optionCount[$id])) {
                $row['optionCount'] = $optionCount[$id];
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    protected function getSearchAssociationQuery($association, $model, $search)
    {
        $builder = parent::getSearchAssociationQuery($association, $model, $search);

        $builder->select($this->getSearchAssociationSelection())
            ->leftJoin('articles.details', 'detail');

        if (strlen($search) > 0) {
            $builder->orWhere('detail.number LIKE :search');
        }

        return $builder;
    }

    /**
     * @return array
     */
    private function getSearchAssociationSelection()
    {
        return [
            'articles.id',
            'articles.name',
            'detail.number'
        ];
    }

    /**
     * @param string | integer $templateId
     * @return array
     */
    private function getAssociatedArticles($templateId)
    {
        $query = $this->container->get('dbal_connection')->createQueryBuilder();
        return $query->select('article_id as id')
            ->from('s_plugin_custom_products_template_product_relation')
            ->where('template_id = :id')
            ->setParameter('id', $templateId)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param int[] $ids
     * @return string[]
     */
    private function getMedia($ids)
    {
        $query = $this->container->get('dbal_connection')->createQueryBuilder();
        $query->select(['id', 'path']);
        $query->from('s_media');
        $query->where('id IN (:ids)');
        $query->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY);
        return $query->execute()->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * @param array $prices
     * @param bool $grossToNet
     * @return array
     */
    private function convertPrices($prices, $grossToNet)
    {
        if (empty($prices)) {
            return [];
        }

        $taxIds = array_column($prices, 'taxId');
        $customerGroupIds = array_column($prices, 'customerGroupId');

        $taxes = $this->getTaxes($taxIds);
        $customerGroups = $this->getCustomerGroups($customerGroupIds);

        foreach ($prices as &$price) {
            $customerGroupId = $price['customerGroupId'];

            $taxInput = true;
            if (isset($customerGroups[$customerGroupId])) {
                $taxInput = $customerGroups[$customerGroupId];
            }
            if (!$taxInput) {
                continue;
            }
            $tax = $taxes[$price['taxId']];

            if ($grossToNet) {
                $price['surcharge'] = $price['surcharge'] / (100 + $tax) * 100;
            } else {
                $price['surcharge'] = round($price['surcharge'] * (100 + $tax) / 100, 3);
            }
        }

        return $prices;
    }

    /**
     * @param int[] $ids
     * @return int[] indexed by customer group id
     */
    private function getCustomerGroups($ids)
    {
        /** @var Connection $connection */
        $connection = $this->get('dbal_connection');
        $query = $connection->createQueryBuilder();

        $query->select(['id', 'taxinput']);
        $query->from('s_core_customergroups', 'customergroup');
        $query->where('customergroup.id IN (:ids)');
        $query->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY);
        return $query->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * @param int[] $ids
     * @return float[] indexed by tax id
     */
    private function getTaxes($ids)
    {
        /** @var Connection $connection */
        $connection = $this->get('dbal_connection');
        $query = $connection->createQueryBuilder();
        $query->select(['tax.id', 'tax.tax']);
        $query->from('s_core_tax', 'tax');
        $query->where('tax.id IN (:ids)');
        $query->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY);

        return $query->execute()->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * @param integer $articleId
     * @return array
     */
    private function productHasTemplate($articleId)
    {
        /** @var TemplateService $templateService */
        $templateService = $this->get('custom_products.template_service');

        return $templateService->getTemplateByProductId($articleId, false);
    }

    /**
     * @param TypeInterface[] $types
     * @return array
     */
    private function prepareTypeForBackend(array $types)
    {
        $returnArray = [];

        foreach ($types as $type) {
            $returnArray[] = [
                'name' => '',
                'type' => $type->getType(),
                'couldContainValues' => $type->couldContainValues()
            ];
        }

        return $returnArray;
    }

    /**
     * Gets the amount of products which are assigned to a template.
     *
     * @param array $ids
     * @return array
     */
    private function getProductCount(array $ids)
    {
        /** @var Connection $connection */
        $connection = $this->get('dbal_connection');
        $query = $connection->createQueryBuilder();

        $query->select(['template_id', 'COUNT(article_id)'])
            ->from('s_plugin_custom_products_template_product_relation', 'product')
            ->where('product.template_id IN (:ids)')
            ->groupBy('template_id')
            ->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY);

        return $query->execute()->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Gets the amount of options which are assigned to a template.
     *
     * @param array $ids
     * @return array
     */
    private function getOptionCount(array $ids)
    {
        /** @var Connection $connection */
        $connection = $this->get('dbal_connection');
        $query = $connection->createQueryBuilder();

        $query->select(['template_id', 'COUNT(id)'])
            ->from('s_plugin_custom_products_option', 'options')
            ->where('template_id IN (:ids)')
            ->groupBy('template_id')
            ->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY);

        return $query->execute()->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Invalidates cache for the given products
     *
     * @param array $products
     */
    private function invalidateCache(array $products)
    {
        if (empty($products)) {
            return;
        }

        /** @var Enlight_Event_EventManager $eventManager */
        $eventManager = $this->get('events');

        foreach ($products as $product) {
            $eventManager->notify('Shopware_Plugins_HttpCache_InvalidateCacheId', ['cacheId' => 'a' . $product['id']]);
        }
    }
}
