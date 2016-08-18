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

use Doctrine\DBAL\Query\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use ShopwarePlugins\SwagCustomProducts\Bootstrap\Uninstaller;
use ShopwarePlugins\SwagCustomProducts\Commands\HashGarbageCollectorCommand;
use ShopwarePlugins\SwagCustomProducts\Components\Services\CustomProductsService;
use ShopwarePlugins\SwagCustomProducts\Components\GarbageCollection\GarbageCollector;
use ShopwarePlugins\SwagCustomProducts\Components\Services\DocumentValueExtender;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TranslationService;
use ShopwarePlugins\SwagCustomProducts\Components\Types\TypeFactory;
use ShopwarePlugins\SwagCustomProducts\Subscriber\Account;
use ShopwarePlugins\SwagCustomProducts\Subscriber\Basket;
use ShopwarePlugins\SwagCustomProducts\Subscriber\Backend;
use ShopwarePlugins\SwagCustomProducts\Subscriber\Checkout;
use ShopwarePlugins\SwagCustomProducts\Subscriber\Container;
use ShopwarePlugins\SwagCustomProducts\Subscriber\Cron;
use ShopwarePlugins\SwagCustomProducts\Subscriber\InquiryBasket;
use ShopwarePlugins\SwagCustomProducts\Subscriber\Template;
use ShopwarePlugins\SwagCustomProducts\Bootstrap\Installer;
use Shopware\Components\Model\ModelManager;
use ShopwarePlugins\SwagCustomProducts\Subscriber\Frontend;
use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Theme\LessDefinition;

class Shopware_Plugins_Frontend_SwagCustomProducts_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * @var ModelManager $em
     */
    private $em;

    /**
     * Initialises model manager
     * registers namespace of the plugin
     */
    public function afterInit()
    {
        $this->em = $this->get('models');
        $this->registerPluginNamespaces();
    }

    /**
     * Returns capabilities
     */
    public function getCapabilities()
    {
        return [
            'install' => true,
            'update' => true,
            'enable' => true,
            'secureUninstall' => true
        ];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Custom Products (v2)';
    }

    /**
     * Returns the version of the plugin
     *
     * @return string
     * @throws Exception
     */
    public function getVersion()
    {
        $info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.json'), true);
        if ($info) {
            return $info['currentVersion'];
        } else {
            throw new Exception('The plugin has an invalid version file.');
        }
    }

    /**
     * @return boolean
     * @throws RuntimeException
     */
    public function install()
    {
        if (!$this->assertMinimumVersion('5.2.0')) {
            throw new RuntimeException('At least Shopware 5.2.0 is required');
        }

        /** @var Installer $installer */
        $installer = new Installer($this, $this->em);

        return $installer->install();
    }

    /**
     * @param string $oldVersion
     * @return array
     * @throws RuntimeException
     */
    public function update($oldVersion)
    {
        if (!$this->assertMinimumVersion('5.2.0')) {
            throw new RuntimeException('At least Shopware 5.2.0 is required');
        }

        $this->registerController('Widgets', 'SwagCustomProducts');

        return $this->getInvalidateCache();
    }

    /**
     * @return array
     */
    public function uninstall()
    {
        $uninstaller = new Uninstaller($this, $this->em);

        return $uninstaller->uninstall();
    }

    /**
     * @return array
     */
    public function secureUninstall()
    {
        $uninstaller = new Uninstaller($this, $this->em);

        return $uninstaller->secureUninstall();
    }

    /**
     * @return array
     */
    public function enable()
    {
        $this->clearCacheForCustomProducts();

        return $this->getInvalidateCache();
    }

    /**
     * @return array
     */
    public function disable()
    {
        $this->clearCacheForCustomProducts();

        return $this->getInvalidateCache();
    }

    /**
     * Extend the Document with optionValues
     *
     * @param Enlight_Hook_HookArgs $args
     */
    public function onBeforeRenderDocument(Enlight_Hook_HookArgs $args)
    {
        /** @var DocumentValueExtender $documentExtender */
        $documentExtender = $this->get('custom_products.document_extender');
        $documentExtender->extendWithValues($args);
    }

    /**
     * registers events via subscribers
     */
    public function onStartDispatch()
    {
        $container = $this->get('service_container');
        $this->registerCustomModels();

        $subscribers = [
            new Container($container),
            new Frontend($container, $this->Path()),
            new Backend($container, $this->Path()),
            new Basket($container),
            new Template(),
            new Checkout($container),
            new Cron($container),
            new Account($container),
            new InquiryBasket($container)
        ];

        foreach ($subscribers as $subscriber) {
            $this->get('events')->addSubscriber($subscriber);
        }
    }

    /**
     * @return CustomProductsService
     */
    public function initCustomProductsService()
    {
        return new CustomProductsService(
            Shopware()->Container()
        );
    }

    /**
     * @return TypeFactory
     */
    public function initTypeFactory()
    {
        return new TypeFactory($this->get('events'));
    }

    /**
     * @return TranslationService
     */
    public function initTranslationService()
    {
        return new TranslationService(
            $this->get('shopware_storefront.context_service'),
            $this->get('dbal_connection')
        );
    }

    /**
     * @return GarbageCollector
     */
    public function initGarbageCollectorService()
    {
        return new GarbageCollector(
            $this->Config()->get('configurationAvailabilityDays'),
            $this->get('models')
        );
    }

    /**
     * @return HashGarbageCollectorCommand
     */
    public function addHashGarbageCollectorCommand()
    {
        return new HashGarbageCollectorCommand();
    }

    /**
     * Provide the needed less files
     *
     * @return ArrayCollection
     */
    public function addLessFiles()
    {
        $less = new LessDefinition(
            [],
            [$this->Path() . 'Views/frontend/_public/src/less/all.less'],
            $this->Path()
        );

        return new ArrayCollection([$less]);
    }

    /**
     * register the plugin namespace
     */
    private function registerPluginNamespaces()
    {
        $this->get('loader')->registerNamespace('ShopwarePlugins\SwagCustomProducts', $this->Path());
    }

    /**
     * Method is now accessible from the Installer
     */
    public function registerCustomModels()
    {
        parent::registerCustomModels();
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     * @return ArrayCollection
     */
    public function addJsFiles(\Enlight_Event_EventArgs $args)
    {
        $jsFiles = [
            $this->Path() . 'Views/frontend/_public/vendors/js/trumbowyg/trumbowyg.min.js',
            $this->Path() . 'Views/frontend/_public/vendors/js/pickadate/picker.js',
            $this->Path() . 'Views/frontend/_public/vendors/js/pickadate/picker.date.js',
            $this->Path() . 'Views/frontend/_public/vendors/js/pickadate/picker.time.js',
            $this->Path() . 'Views/frontend/_public/vendors/js/handlebars/handlebars-v4.0.5.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-collapse-panel.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-auto-size-textarea.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-wysiwyg.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-numberfield.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-datepicker.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-timepicker.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-validation.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-wizard.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-description.js',
            $this->Path() . 'Views/frontend/_public/src/js/jquery.swag-custom-products-upload.js'
        ];

        /** @var ContextServiceInterface $shopContextService */
        $langCode = strtolower($args->get('shop')->getLocale()->getLocale());
        $shortLangCode = strtolower(substr($args->get('shop')->getLocale()->getLocale(), 0, 2));

        $wysiwygLangPath = sprintf(
            '%sViews/frontend/_public/vendors/js/trumbowyg/langs/%s.min.js',
            $this->Path(),
            $shortLangCode
        );
        $datePickerLangPath = sprintf(
            '%sViews/frontend/_public/vendors/js/pickadate/translations/%s.js',
            $this->Path(),
            $langCode
        );

        // Add language files for the wysiwyg
        if (file_exists($wysiwygLangPath)) {
            array_push($jsFiles, $wysiwygLangPath);
        }

        // Add date picker language files
        if (file_exists($datePickerLangPath)) {
            array_push($jsFiles, $datePickerLangPath);
        }

        return new ArrayCollection($jsFiles);
    }

    /**
     * @return array
     */
    public function getInvalidateCache()
    {
        return ['success' => true, 'invalidateCache' => ['frontend', 'backend', 'proxy']];
    }

    /**
     * Clears the cache for products which have a Custom Products template assigned
     */
    public function clearCacheForCustomProducts()
    {
        /** @var QueryBuilder $builder */
        $builder = $this->get('dbal_connection')->createQueryBuilder();
        $builder->select('article_id')
            ->from('s_plugin_custom_products_template_product_relation');

        $products = $builder->execute()->fetchAll(PDO::FETCH_COLUMN);

        /** @var Enlight_Event_EventManager $eventManager */
        $eventManager = $this->get('events');

        foreach ($products as $product) {
            $eventManager->notify('Shopware_Plugins_HttpCache_InvalidateCacheId', ['cacheId' => 'a' . $product]);
        }
    }
}
