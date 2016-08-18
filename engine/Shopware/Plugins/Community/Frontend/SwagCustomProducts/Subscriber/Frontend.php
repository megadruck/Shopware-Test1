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

use Doctrine\ORM\Query;
use Enlight_Event_EventArgs;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container as DIContainer;
use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\FileSizeFormatter;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TemplateService;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TranslationService;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\FileUploadType;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\ImageUploadType;

class Frontend implements SubscriberInterface
{
    /** @var DIContainer $container */
    private $container;

    /** @var string $bootstrapPath */
    private $bootstrapPath;

    /**
     * @param DIContainer $container
     * @param string $bootstrapPath
     */
    public function __construct(DIContainer $container, $bootstrapPath)
    {
        $this->container = $container;
        $this->bootstrapPath = $bootstrapPath;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onPostDispatchFrontendDetail',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Compare' => 'onLoadCompare'
        ];
    }

    /**
     * Assigns the custom product template to the product detail page
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchFrontendDetail(Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Detail $controller */
        $controller = $args->get('subject');
        $view = $controller->View();
        $assignedProduct = $view->getAssign('sArticle');

        /** @var TemplateService $templateService */
        $templateService = $this->container->get('custom_products.template_service');
        $customProductTemplate = $templateService->getTemplateByProductId($assignedProduct['articleID']);

        if (!$customProductTemplate) {
            return;
        }

        if (!$customProductTemplate['active']) {
            return;
        }

        $customProductTemplate['options'] = $this->formatMaxFileSizes($customProductTemplate['options']);

        /** @var TranslationService $translationService */
        $translationService = $this->container->get('custom_products.translation_service');
        $customProductTemplate = $translationService->translateTemplate($customProductTemplate);
        $customProductNeedsConfig = $controller->Request()->getParam('customProductNeedsConfig');

        $view->assign('swagCustomProductsTemplate', $customProductTemplate);
        $view->assign('customProductNeedsConfig', $customProductNeedsConfig);
    }

    /**
     * Checks if the compared products are having a custom products preset
     * assigned and if it's available it will display a new row in compare overlay.
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function onLoadCompare(Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Compare $controller */
        $controller = $args->get('subject');
        $request = $controller->Request();
        $view = $controller->View();

        if ($request->getActionName() !== 'overlay') {
            return;
        }

        $comparisonsList = $view->getAssign('sComparisonsList');

        /** @var TemplateService $templateService */
        $templateService = $this->container->get('custom_products.template_service');

        foreach ($comparisonsList['articles'] as &$comparison) {
            $template = $templateService->getTemplateByProductId($comparison['articleID']);
            if ($template !== null && $template['active']) {
                $comparison['swagCustomProducts'] = true;
            }
        }

        $view->assign('sComparisonsList', $comparisonsList);
    }

    /**
     * @param array $options
     * @return array
     */
    private function formatMaxFileSizes(array $options)
    {
        foreach ($options as &$option) {
            if (!$this->checkUploadOptionTypes($option['type'])) {
                continue;
            }

            $option['max_file_size_formatted'] = $this->formatFileSize($option['max_file_size']);
        }

        return $options;
    }

    /**
     * @param string $type
     * @return boolean
     */
    private function checkUploadOptionTypes($type)
    {
        switch ($type) {
            case FileUploadType::TYPE:
            case ImageUploadType::TYPE:
                return true;
            default:
                return false;
        }
    }

    /**
     * @param integer $bytes
     * @return string
     */
    private function formatFileSize($bytes)
    {
        /** @var FileSizeFormatter $fileSizeFormatter */
        $fileSizeFormatter = $this->container->get('custom_products.file_upload.file_size_formatter');

        return $fileSizeFormatter->formatBytes($bytes);
    }
}
