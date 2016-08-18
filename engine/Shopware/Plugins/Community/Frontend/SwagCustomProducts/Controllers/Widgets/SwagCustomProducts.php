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

use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\FileConfigStruct;
use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\FileUploadException;
use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\FileUploadService;
use ShopwarePlugins\SwagCustomProducts\Components\FileUpload\MaxFilesException;
use ShopwarePlugins\SwagCustomProducts\Components\Services\HashManager;
use ShopwarePlugins\SwagCustomProducts\Components\Services\PostDataValueConverter;
use ShopwarePlugins\SwagCustomProducts\Components\Services\TemplateService;
use ShopwarePlugins\SwagCustomProducts\Components\Calculator;
use Shopware\Models\Media\Media;
use Symfony\Component\HttpFoundation\FileBag;


class Shopware_Controllers_Widgets_SwagCustomProducts extends Enlight_Controller_Action
{
    /**
     * the pre dispatch
     */
    public function preDispatch()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $this->Front()->Plugins()->Json()->setRenderer();
    }

    /**
     * this is a method to get the configuration by hash.
     * Its necessary to set the request parameter "hash" and call this action by ajax,
     * because there is no Smarty renderer.
     *
     * For example a URI "http://../SwagCustomProducts/getConfiguration?hash=f3ad06108c0a61e11bcef3c0a500a671"
     */
    public function getConfigurationAction()
    {
        $hash = $this->request->get('hash');

        if (!$hash) {
            $this->View()->assign(['success' => false, 'error' => 'No hash']);
            return;
        }

        /** @var HashManager $hashManager */
        $hashManager = $this->get('custom_products.hash_manager');
        $configuration = $hashManager->findConfigurationByHash($hash);

        if (!$configuration) {
            $this->View()->assign(['success' => false, 'error' => 'no configuration found']);
            return;
        }

        /** @var PostDataValueConverter $postDataConverter */
        $postDataConverter = $this->get('custom_products.post_data_value_converter');
        $configuration = $postDataConverter->convertPostDataBackward($configuration);

        $this->View()->assign(['success' => true, 'configuration' => $configuration]);
    }

    /**
     * Saves the configuration as a json string and with a md5 hash.
     * This method returns the hash as an identifier for passing the configuration into the basket.
     */
    public function saveConfigurationAction()
    {
        /** @var HashManager $hashManager */
        $hashManager = $this->get('custom_products.hash_manager');

        /** @var TemplateService $templateService */
        $templateService = $this->get('custom_products.template_service');
        $options = $templateService->getOptionsByTemplateId($this->Request()->getParam('templateId'));

        // Save the Configuration not Permanent...And add the hash to the view
        $configurationHash = $hashManager->manageHashByConfiguration($this->getPostData($options), false, $options);
        $this->View()->assign('hash', $configurationHash);
    }

    /**
     * Action for the frontend uploads, i.e. file- and image upload types. Further a new hash will be generated.
     */
    public function uploadAction()
    {
        $fileBag = new FileBag($_FILES);

        /** @var FileUploadService $fileUploadService */
        $fileUploadService = $this->get('custom_products.file_upload.file_upload_service');

        /** @var PostDataValueConverter $postDataConverter */
        $postDataConverter = $this->get('custom_products.post_data_value_converter');

        $key = $this->getFirstArrayKey($_FILES);
        $optionId = (int)$postDataConverter->getIdFromKey($key);
        $fileConfigStruct = $this->getFileConfigStructByOptionId($optionId);

        try {
            $fileUploadService->validate($fileBag, $fileConfigStruct);
            $mediaFiles = $fileUploadService->upload($fileBag);
        } catch (FileUploadException $e) {
            $messages = [];
            $files = [];

            foreach ($e->getErrors() as $error) {
                $messages[] = $error->getMessage();
                $files[] = $error->getFileName();
            }

            $this->View()->assign([
                'success' => false,
                'message' => implode('<br />', $messages),
                'errorFiles' => $files
            ]);
            return;
        } catch (MaxFilesException $e) {
            $this->View()->assign([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            return;
        }

        /** @var TemplateService $templateService */
        $templateService = $this->get('custom_products.template_service');
        $options = $templateService->getOptionsByTemplateId($this->Request()->getPost('templateId'));

        $mediaMetaData = $this->prepareMediaMetaData($mediaFiles);
        $postData = $this->addMediaToPostData($options, $optionId, $mediaMetaData);

        /** @var HashManager $hashManager */
        $hashManager = $this->get('custom_products.hash_manager');
        $hash = $hashManager->manageHashByConfiguration($postData, false, $options);

        $this->View()->assign(['success' => true, 'hash' => $hash, 'files' => $mediaMetaData]);
    }

    /**
     * calculate the surcharge Overview
     */
    public function overviewCalculationAction()
    {
        $productId = $this->request->getParam('sArticle');
        $number = $this->request->getParam('number');

        if (!$productId || !$number) {
            $this->View()->assign(['success' => false, 'error' => 'Product id or number not found.']);
            return;
        }

        /** @var TemplateService $templateService */
        $templateService = $this->get('custom_products.template_service');
        $template = $templateService->getTemplateByProductId($productId);

        if ($template === null) {
            $this->View()->assign(['success' => false, 'error' => 'Template struct not found.']);
            return;
        }

        /** @var \ShopwarePlugins\SwagCustomProducts\Components\Services\TranslationService $translationService */
        $translationService = $this->get('custom_products.translation_service');
        $template = $translationService->translateTemplate($template);

        /** @var HashManager $hashManager */
        $hashManager = $this->get('custom_products.hash_manager');

        // Save the Configuration not Permanent...And add the hash to the view
        $configurationHash = $hashManager->manageHashByConfiguration($this->getPostData($template['options']), false, $template['options']);
        $this->View()->assign('hash', $configurationHash);

        $configuration = $hashManager->findConfigurationByHash($configurationHash);

        $context = Shopware()->Container()->get('shopware_storefront.context_service')->getShopContext();
        $product = Shopware()->Container()->get('shopware_storefront.list_product_service')->get($number, $context);

        $default = 1;
        if ($product->getUnit()) {
            $default = $product->getUnit()->getMinPurchase();
        }
        $quantity = (int)$this->Request()->getParam('sQuantity', $default);

        if ($quantity <= 0) {
            $quantity = 1;
        }

        $calculator = new Calculator($this->container, $templateService);
        $result = $calculator->calculate($template['options'], $configuration, $number, $quantity);
        $this->View()->assign('data', $result);
    }


    /**
     * @param Media[] $mediaFiles
     * @return array
     */
    protected function prepareMediaMetaData($mediaFiles)
    {
        $mediaService = $this->container->get('shopware_media.media_service');

        $media = [];
        foreach ($mediaFiles as $mediaFile) {
            $media[] = [
                'id' => $mediaFile->getId(),
                'path' => $mediaService->getUrl($mediaFile->getPath()),
                'name' => $mediaFile->getDescription(),
                'size' => $mediaFile->getFileSize()
            ];
        }
        return $media;
    }

    /**
     * Adds the media to the generated post data.
     * @see \Shopware_Controllers_Frontend_SwagCustomProducts::getPostData
     *
     * @param array $options
     * @param int $optionId
     * @param array $mediaMetaData
     * @return array
     */
    protected function addMediaToPostData(array $options, $optionId, array $mediaMetaData)
    {
        $postData = $this->getPostData($options);

        $files = $this->addMediaToConfiguration($postData[$optionId], $mediaMetaData);
        $postData[$optionId] = json_encode($files);

        return $postData;
    }

    /**
     * Adds media files to the configured files.
     *
     * @param string $optionMedia
     * @param array $newMedia
     * @return array
     */
    private function addMediaToConfiguration($optionMedia, array $newMedia)
    {
        $optionMedia = json_decode($optionMedia[0], true);

        if (empty($optionMedia)) {
            $optionMedia = [];
        }

        foreach ($newMedia as $newFile) {
            $optionMedia[] = $newFile;
        }

        return $optionMedia;
    }

    /**
     * @param int $optionId
     * @return FileConfigStruct
     */
    private function getFileConfigStructByOptionId($optionId)
    {
        /** @var \Doctrine\DBAL\Connection $dbalConnection */
        $dbalConnection = $this->get('dbal_connection');

        $queryBuilder = $dbalConnection->createQueryBuilder();
        $queryBuilder->select(['*'])
            ->from('s_plugin_custom_products_option', 'o')
            ->where('id = :id')
            ->setParameter('id', $optionId);

        $result = $queryBuilder->execute()->fetch();

        $fileConfigStruct = new FileConfigStruct();
        $fileConfigStruct->setMaxFiles($result['max_files']);
        $fileConfigStruct->setMaxSize($result['max_file_size']);
        $fileConfigStruct->setType($result['type']);

        return $fileConfigStruct;
    }

    /**
     * @param array $array
     * @return string
     */
    private function getFirstArrayKey(array $array)
    {
        reset($array);
        return key($array);
    }

    /**
     * Get the converted postData
     *
     * @param array $options
     * @return array
     */
    private function getPostData(array $options)
    {
        $postData = $this->request->getParams();

        /** @var PostDataValueConverter $postDataValueConverter */
        $postDataValueConverter = $this->get('custom_products.post_data_value_converter');
        return $postDataValueConverter->convertPostData($postData, $options);
    }
}