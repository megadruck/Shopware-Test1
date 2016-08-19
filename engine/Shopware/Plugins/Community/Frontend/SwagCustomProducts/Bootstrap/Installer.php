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

use Shopware\Models\Media\Album;
use Shopware\Models\Media\Settings as MediaSettings;
use Shopware_Plugins_Frontend_SwagCustomProducts_Bootstrap as PluginBootstrap;
use Doctrine\ORM\Tools\SchemaTool;
use Shopware\Components\Model\ModelManager;

class Installer
{
    /**
     * All thumbnail sizes which will be used for the custom products media album
     */
    const THUMBNAIL_SIZES = '50x50;80x80;300x300;400x400';

    const FRONTEND_UPLOAD_ALBUM = 'Frontend uploads';

    /**
     * @var PluginBootstrap $pluginBootstrap
     */
    private $pluginBootstrap;

    /**
     * @var ModelManager $em
     */
    private $em;

    /**
     * @param PluginBootstrap $pluginBootstrap
     * @param ModelManager $em
     */
    public function __construct(PluginBootstrap $pluginBootstrap, ModelManager $em)
    {
        $this->pluginBootstrap = $pluginBootstrap;
        $this->em = $em;
    }

    /**
     * @return bool
     */
    public function install()
    {
        $this->renameOldSwagCustomizing();

        $this->updateSchema();

        // register controller
        $this->pluginBootstrap->registerController('Backend', 'SwagCustomProducts');
        $this->pluginBootstrap->registerController('Backend', 'SwagCustomProductsExtensions');
        $this->pluginBootstrap->registerController('Widgets', 'SwagCustomProducts');

        $this->registerEvents();
        $this->createMenu();
        $this->createCronJob();

        $mediaAlbum = $this->createMediaAlbum();
        $this->createFrontendMediaAlbum($mediaAlbum);

        $this->createForm();
        $this->addACLResource();

        return true;
    }

    /**
     * Creates the menu-entry for the backend
     */
    private function createMenu()
    {
        $this->pluginBootstrap->createMenuItem(
            [
                'label' => 'Custom Products',
                'controller' => 'SwagCustomProducts',
                'action' => 'Index',
                'class' => 'sprite-custom-products',
                'active' => 1,
                'parent' => $this->pluginBootstrap->Menu()->findOneBy(['label' => 'Artikel'])
            ]
        );
    }

    /**
     * Registers events for this plugin
     */
    private function registerEvents()
    {
        $this->pluginBootstrap->subscribeEvent('Enlight_Controller_Front_StartDispatch', 'onStartDispatch');

        //Can't be outsourced in a subscriber because grunt can't detect them
        $this->pluginBootstrap->subscribeEvent('Theme_Compiler_Collect_Plugin_Less', 'addLessFiles');
        $this->pluginBootstrap->subscribeEvent('Theme_Compiler_Collect_Plugin_Javascript', 'addJsFiles');

        // Services needed earlier than the StartDispatch Event
        $this->pluginBootstrap->subscribeEvent(
            'Enlight_Bootstrap_InitResource_custom_products.service',
            'initCustomProductsService'
        );
        $this->pluginBootstrap->subscribeEvent(
            'Enlight_Bootstrap_InitResource_custom_products.type_factory',
            'initTypeFactory'
        );
        $this->pluginBootstrap->subscribeEvent(
            'Enlight_Bootstrap_InitResource_custom_products.translation_service',
            'initTranslationService'
        );
        $this->pluginBootstrap->subscribeEvent(
            'Enlight_Bootstrap_InitResource_custom_products.garbage_collection.garbage_collector_service',
            'initGarbageCollectorService'
        );

        $this->pluginBootstrap->subscribeEvent(
            'Shopware_Console_Add_Command',
            'addHashGarbageCollectorCommand'
        );

        $this->pluginBootstrap->subscribeEvent(
            'Shopware_Components_Document::assignValues::after',
            'onBeforeRenderDocument'
        );
    }

    /**
     * Creates the existing database tables and creates the option types.
     */
    private function updateSchema()
    {
        $this->pluginBootstrap->registerCustomModels();

        /** @var SchemaManager $schemaManager */
        $schemaManager = new SchemaManager();
        $tableModelNames = $schemaManager->getModelClassNames();
        $this->createTables($tableModelNames);

        $attributes = $schemaManager->getCustomProductsAttributes();
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
    }

    /**
     * Creates the media album inclusive thumbnail settings etc
     *
     * @return Album
     */
    private function createMediaAlbum()
    {
        /** @var \Shopware\Models\Media\Repository $mediaRepository */
        $mediaRepository = $this->em->getRepository('Shopware\Models\Media\Album');

        if ($mediaAlbum = $mediaRepository->findOneBy(['name' => 'CustomProducts'])) {
            return $mediaAlbum;
        }

        $mediaAlbum = new Album();
        $mediaAlbum->setName('CustomProducts');
        $mediaAlbum->setPosition(9);

        $mediaAlbumSettings = new MediaSettings();
        $mediaAlbumSettings->setCreateThumbnails(1);
        $mediaAlbumSettings->setIcon('sprite-box');
        $mediaAlbumSettings->setThumbnailHighDpi(false);
        $mediaAlbumSettings->setThumbnailSize(self::THUMBNAIL_SIZES);
        $mediaAlbumSettings->setThumbnailQuality(90);
        $mediaAlbumSettings->setThumbnailHighDpiQuality(60);

        $mediaAlbum->setSettings($mediaAlbumSettings);
        $mediaAlbumSettings->setAlbum($mediaAlbum);

        $this->em->persist($mediaAlbum);
        $this->em->persist($mediaAlbumSettings);
        $this->em->flush([$mediaAlbum, $mediaAlbumSettings]);

        return $mediaAlbum;
    }

    /**
     * Creates an album for frontend user uploads.
     *
     * @param Album $parentAlbum
     */
    private function createFrontendMediaAlbum(Album $parentAlbum)
    {
        /** @var \Shopware\Models\Media\Repository $mediaRepository */
        $mediaRepository = $this->em->getRepository(Album::class);

        if ($mediaRepository->findOneBy(['name' => self::FRONTEND_UPLOAD_ALBUM])) {
            return;
        }

        $mediaAlbum = new Album();
        $mediaAlbum->setName(self::FRONTEND_UPLOAD_ALBUM);
        $mediaAlbum->setPosition(10);

        $mediaAlbumSettings = new MediaSettings();
        $mediaAlbumSettings->setIcon('sprite-sd-memory-card');
        $mediaAlbumSettings->setAlbum($mediaAlbum);
        $mediaAlbumSettings->setThumbnailQuality(90);
        $mediaAlbumSettings->setThumbnailHighDpiQuality(90);
        $mediaAlbumSettings->setCreateThumbnails(0);
        $mediaAlbumSettings->setThumbnailSize([]);

        $mediaAlbum->setParent($parentAlbum);

        $this->em->persist($mediaAlbum);
        $this->em->persist($mediaAlbumSettings);
        $this->em->flush([$mediaAlbum, $mediaAlbumSettings]);
    }

    /**
     * Creates the plugin configuration form
     *
     */
    private function createForm()
    {
        $form = $this->pluginBootstrap->Form();

        $form->setElement(
            'number',
            'configurationAvailabilityDays',
            [
                'label' => 'Durability of the Custom Products configurations',
                'description' => 'The amount of days after the Custom Products hashes should expire.',
                'value' => 30,
                'minValue' => 0
            ]
        );

        $form->setElement(
            'checkbox',
            'doNotShowMigrationButton',
            [
                'label' => 'Hide the migration button',
                'description' => 'If the old Customizing plugin is installed you can migrate the content. Only in this case, this setting is taken into account.',
                'value' => false
            ]
        );

        $this->pluginBootstrap->addFormTranslations($this->getFormTranslations());
    }

    /**
     * Creates the album store for the plugin configuration form
     *
     * @return array
     */
    private function getAlbumStore()
    {
        /** @var \Shopware\Models\Media\Repository $albumRepository */
        $albumRepository = $this->em->getRepository('Shopware\Models\Media\Album');
        $albums = $albumRepository->findAll();

        return array_map(function ($album) {
            /** @var \Shopware\Models\Media\Album $album */
            return [
                $album->getId(),
                $album->getName()
            ];
        }, $albums);
    }

    /**
     * Returns all translations for the plugin configuration
     *
     * @return array
     */
    private function getFormTranslations()
    {
        return [
            'en_GB' => [
                'configurationAvailabilityDays' => [
                    'label' => 'Validity of the Custom Products configurations',
                    'description' => 'The number of days until the Custom Products configurations expire.'
                ],
                'doNotShowMigrationButton' => [
                    'label' => 'Hide the migration button',
                    'description' => 'If the old Customizing plugin is installed, you can migrate the content. This setting is only taken into account when the plugin is installed.'
                ]
            ],
            'de_DE' => [
                'configurationAvailabilityDays' => [
                    'label' => 'Gültigkeit der Custom Products Konfigurationen',
                    'description' => 'Die Anzahl der Tage, nach dem Custom Products Konfigurationen ungültig werden.'
                ],
                'doNotShowMigrationButton' => [
                    'label' => 'Migrations - Schaltfläche ausblenden',
                    'description' => 'Wenn das alte Customizing Plugin installiert ist, ist es möglich, den Content zu migrieren. Nur in diesem Fall wird diese Einstellung berücksichtigt.'
                ]
            ]
        ];
    }

    /**
     * Creates all tables and checks if the table exists
     *
     * @param array $tableModelNames
     */
    private function createTables(array $tableModelNames)
    {
        $schemaTool = new SchemaTool($this->em);
        $doctrineSchemaManager = $this->em->getConnection()->getSchemaManager();

        /** @var \Doctrine\ORM\Mapping\ClassMetadata[] $createClasses */
        $createClasses = [];
        /** @var \Doctrine\ORM\Mapping\ClassMetadata[] $updateClasses */
        $updateClasses = [];

        /** @var string $modelName */
        foreach ($tableModelNames as $modelName) {
            $classMetaData = $this->em->getClassMetadata($modelName);

            if ($doctrineSchemaManager->tablesExist([$classMetaData->getTableName()])) {
                $updateClasses[] = $classMetaData;
                continue;
            }

            $createClasses[] = $classMetaData;
        }

        if (!empty($createClasses)) {
            $schemaTool->createSchema($createClasses);
        }

        if (!empty($updateClasses)) {
            $schemaTool->updateSchema($updateClasses, true);
        }
    }

    /**
     * Adds custom columns to attributes tables
     *
     * @param array $attribute
     */
    private function addAttribute(array $attribute)
    {
        $this->em->addAttribute(
            $attribute['table'],
            $attribute['prefix'],
            $attribute['column'],
            $attribute['type'],
            $attribute['nullable'],
            $attribute['default']
        );

        $this->em->generateAttributeModels([$attribute['table']]);
    }

    /**
     * Creates the cron job for hash garbage collection.
     */
    private function createCronJob()
    {
        $this->pluginBootstrap->createCronJob(
            'CustomProducts Hash GarbageCollector',
            'Shopware_CronJob_SwagCustomProductsHashGarbageCollectorCron'
        );
    }

    /**
     * Adds the CustomProducts ACL resource with the read-privilege. Additionally updates the menu-entry.
     */
    protected function addACLResource()
    {
        $this->pluginBootstrap->get('acl')->createResource(
            'swagcustomproducts',
            ['read'],
            'SwagCustomProducts',
            $this->pluginBootstrap->getId()
        );
    }

    /**
     * rename the old SwagCustomizing plugin menu label
     */
    private function renameOldSwagCustomizing()
    {
        $sql = "UPDATE s_core_menu
                SET name = 'Custom Products (v1)'
                WHERE name = 'Custom Products'
                  AND controller = 'Customizing'";
        $this->em->getConnection()->executeUpdate($sql);

        $sql = "UPDATE s_core_snippets
                SET value = 'Custom Products (v1)'
                WHERE name = 'Customizing' AND dirty = 0
                  AND value = 'Custom Products'";
        $this->em->getConnection()->executeUpdate($sql);
    }
}
