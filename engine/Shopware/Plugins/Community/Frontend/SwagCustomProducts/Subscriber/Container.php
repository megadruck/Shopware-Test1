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
use Shopware\Components\DependencyInjection\Container as DIContainer;
use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\Converter\DefaultConverter;
use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\Converter\FileUploadConverter;
use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\Converter\ImageUploadConverter;
use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\Converter\WysiwygConverter;
use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\Registry;
use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\FileSizeFormatter;
use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\FileTypeWhitelist;
use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\FileUploadService;
use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\Uploader;
use ShopwarePlugins\SwagCustomProducts\Components\Inquiry\InquiryService;
use ShopwarePlugins\SwagCustomProducts\Components\Inquiry\Strategy\SelectedValueStrategy;
use ShopwarePlugins\SwagCustomProducts\Components\Inquiry\Strategy\ValuesStrategy;
use ShopwarePlugins\SwagCustomProducts\Components\OrderNumberValidation\OrderNumberValidationService;
use ShopwarePlugins\SwagCustomProducts\Components\Services\BasketManager;
use ShopwarePlugins\SwagCustomProducts\Components\Services\DateTimeService;
use ShopwarePlugins\SwagCustomProducts\Components\Services\DocumentValueExtender;
use ShopwarePlugins\SwagCustomProducts\Components\Services\HashManager;
use ShopwarePlugins\SwagCustomProducts\Components\Services\Migration;
use ShopwarePlugins\SwagCustomProducts\Components\Services\PostDataValueConverter;
use ShopwarePlugins\SwagCustomProducts\Components\Services\PriceFactory;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TemplateService;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\FileUploadType;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\ImageUploadType;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\WysiwygType;

class Container implements SubscriberInterface
{
    /**
     * @var array $container
     */
    private static $container;

    /**
     * @var DIContainer $diContainer
     */
    private $diContainer;

    /**
     * Registers custom classes in the DIContainer.
     *
     * @param DIContainer $diContainer
     */
    public function __construct(DIContainer $diContainer)
    {
        $this->diContainer = $diContainer;

        self::$container = [
            // templateService
            'custom_products.template_service' => function (DIContainer $c) {
                return new TemplateService(
                    $c->get('shopware_storefront.media_service'),
                    $c->get('shopware_storefront.context_service'),
                    $c->get('dbal_connection'),
                    $c
                );
            },
            // basketManager
            'custom_products.basket_manager' => function (DIContainer $c) {
                return new BasketManager(
                    $c->get('session'),
                    $c->get('custom_products.date_time_service'),
                    $c->get('custom_products.post_data_value_converter'),
                    $c->get('custom_products.template_service'),
                    $c->get('dbal_connection'),
                    $c->get('custom_products.hash_manager'),
                    $c->get('custom_products.service'),
                    $c->get('shopware_storefront.context_service')
                );
            },
            // DateTimeService
            'custom_products.date_time_service' => function (DIContainer $c) {
                return new DateTimeService();
            },
            'custom_products.inquiry.values_strategy' => function (DIContainer $c) {
                return new ValuesStrategy(
                    Shopware()->Modules()->Articles(),
                    $c->get('snippets'),
                    $c->get('shopware_storefront.context_service')->getShopContext()->getCurrency()->getCurrency()
                );
            },
            'custom_products.inquiry.selected_value_strategy' => function (DIContainer $c) {
                return new SelectedValueStrategy(
                    Shopware()->Modules()->Articles(),
                    $c->get('snippets'),
                    $c->get('shopware_storefront.context_service')->getShopContext()->getCurrency()->getCurrency()
                );
            },
            'custom_products.inquiry.inquiry_service' => function (DIContainer $c) {
                return new InquiryService(
                    $c->get('custom_products.inquiry.selected_value_strategy'),
                    $c->get('custom_products.inquiry.values_strategy')
                );
            },
            'custom_products.data_converter.registry' => function (DIContainer $c) {
                return new Registry([
                    WysiwygType::TYPE => new WysiwygConverter(),
                    FileUploadType::TYPE => new FileUploadConverter(),
                    ImageUploadType::TYPE => new ImageUploadConverter(),
                    Registry::DEFAULT_CONVERTER => new DefaultConverter()
                ]);
            },
            // PostDataValueConverter
            'custom_products.post_data_value_converter' => function (DIContainer $c) {
                return new PostDataValueConverter(
                    $c->get('custom_products.data_converter.registry')
                );
            },
            // HashManager
            'custom_products.hash_manager' => function (DIContainer $c) {
                return new HashManager(
                    $c->get('dbal_connection'),
                    $c->get('custom_products.date_time_service')
                );
            },
            'custom_products.file_upload.uploader' => function (DIContainer $c) {
                return new Uploader(
                    $c->get('models'),
                    $c->get('shopware_media.media_service')
                );
            },
            'custom_products.file_upload.file_size_formatter' => function () {
                return new FileSizeFormatter();
            },
            'custom_products.file_upload.file_type_whitelist' => function () {
                return new FileTypeWhitelist();
            },
            'custom_products.file_upload.file_upload_service' => function (DIContainer $c) {
                return new FileUploadService(
                    $c->get('validator'),
                    $c->get('snippets'),
                    $c->get('custom_products.file_upload.uploader'),
                    $c->get('custom_products.file_upload.file_size_formatter'),
                    $c->get('custom_products.file_upload.file_type_whitelist')
                );
            },
            'custom_products.order_number.validation_service' => function (DIContainer $c) {
                return new OrderNumberValidationService(
                    $c->get('dbal_connection')
                );
            },
            'custom_products.migration' => function (DIContainer $c) {
                return new Migration(
                    $c
                );
            },
            'custom_products.price_factory' => function (DIContainer $c) {
                return new PriceFactory(
                    $c
                );
            },
            'custom_products.document_extender' => function (DIContainer $c) {
                return new DocumentValueExtender(
                    $c->get('dbal_connection'),
                    $c->get('custom_products.hash_manager')
                );
            }
        ];
    }

    /**
     * Generate the subscribedEvents array depending on the $container static property
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        $events = [];

        foreach (self::$container as $name => $function) {
            $events['Enlight_Bootstrap_InitResource_' . $name] = 'load';
        }

        return $events;
    }

    /**
     * Generic callback function for all registered subscribers in this class. Will dispatch the event to
     * the anonymous function of the corresponding service
     *
     * @param \Enlight_Event_EventArgs $args
     * @return mixed
     */
    public function load(\Enlight_Event_EventArgs $args)
    {
        // get registered service from event name
        $name = str_replace('Enlight_Bootstrap_InitResource_', '', $args->getName());

        // call anonymous function in order to register service
        $method = self::$container[$name];

        return $method($this->diContainer);
    }
}
