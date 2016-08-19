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

namespace ShopwarePlugins\SwagCustomProducts\Bootstrap;

use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\Model\ModelManager;
use Shopware_Plugins_Frontend_SwagCustomProducts_Bootstrap as PluginBootstrap;

class Uninstaller
{
    /** @var PluginBootstrap $pluginBootstrap */
    private $pluginBootstrap;

    /** @var ModelManager $em */
    private $em;

    /** @var SchemaManager $schemaManager */
    private $schemaManager;

    /**
     * @param PluginBootstrap $pluginBootstrap
     * @param ModelManager $em
     */
    public function __construct(PluginBootstrap $pluginBootstrap, ModelManager $em)
    {
        $this->pluginBootstrap = $pluginBootstrap;
        $this->em = $em;
        $this->schemaManager = new SchemaManager();
    }

    /**
     * Deletes all custom database tables which are related to the plugin
     *
     * @return array
     */
    public function uninstall()
    {
        $this->secureUninstall();

        $this->pluginBootstrap->registerCustomModels();

        $modelNames = $this->schemaManager->getModelClassNames();
        $this->removeTables($modelNames);

        $attributes = $this->schemaManager->getCustomProductsAttributes();

        foreach ($attributes as $attribute) {
            $this->removeAttribute($attribute);
        }

        return $this->pluginBootstrap->getInvalidateCache();
    }

    /**
     * deletes all custom basket attribute columns which are related to the plugin
     *
     * @return array
     */
    public function secureUninstall()
    {
        $this->pluginBootstrap->clearCacheForCustomProducts();
        $this->deleteACLResource();
        $this->deleteCronJob();

        return $this->pluginBootstrap->getInvalidateCache();
    }

    /**
     * Removes all tables by their model class names
     *
     * @example:
     * [
     *  'Shopware\CustomModels\SwagCustomProducts\Template\Template',
     *  'Shopware\CustomModels\SwagCustomProducts\Example\ModelName'
     * ]
     *
     * @param array $modelNames
     */
    private function removeTables(array $modelNames)
    {
        $tool = new SchemaTool($this->em);

        /** @var \Doctrine\ORM\Mapping\ClassMetadata[] $classes */
        $classes = [];
        foreach ($modelNames as $modelName) {
            $classes[] = $this->em->getClassMetadata($modelName);
        }

        $tool->dropSchema($classes);
    }

    /**
     * @param array $attribute
     */
    private function removeAttribute(array $attribute)
    {
        $this->em->removeAttribute(
            $attribute['table'],
            $attribute['prefix'],
            $attribute['column']
        );

        $this->em->generateAttributeModels([$attribute['table']]);
    }

    /**
     * deletes the cron job, because Shopware does not do that automatically
     */
    private function deleteCronJob()
    {
        $builder = $this->em->getConnection()->createQueryBuilder();

        $builder->delete('s_crontab')
            ->where('action = "Shopware_CronJob_SwagCustomProductsHashGarbageCollectorCron"')
            ->execute();
    }

    /**
     * Deletes the CustomProducts ACL resource and all of its privileges.
     */
    public function deleteACLResource()
    {
        /** @var \Shopware_Components_Acl $acl */
        $acl = $this->pluginBootstrap->get('acl');
        $acl->deleteResource('swagcustomproducts');
    }
}
