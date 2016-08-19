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

namespace ShopwarePlugins\SwagCustomProducts\Components\FileUpload;

use Shopware\Bundle\MediaBundle\MediaService;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Media\Album;
use Shopware\Models\Media\Media;
use ShopwarePlugins\SwagCustomProducts\Bootstrap\Installer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Shopware\Models\Attribute\Media as MediaAttribute;

class Uploader
{
    const FRONTEND_USER_UPLOAD_ALBUM = Installer::FRONTEND_UPLOAD_ALBUM;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     * @param ModelManager $modelManager
     * @param MediaService $mediaService
     */
    public function __construct(ModelManager $modelManager, MediaService $mediaService)
    {
        $this->modelManager = $modelManager;
        $this->mediaService = $mediaService;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return Media
     */
    public function upload(UploadedFile $uploadedFile)
    {
        $media = $this->createMediaModel($uploadedFile);

        $this->modelManager->persist($media);
        $this->modelManager->flush($media);

        $newFileName = $this->hashFileName($media);
        $newMedia = $this->moveFile($media, $newFileName);

        $this->modelManager->persist($newMedia);
        $this->modelManager->flush($newMedia);

        return $newMedia;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return Media
     */
    private function createMediaModel(UploadedFile $uploadedFile)
    {
        $media = new Media();

        $media->setAlbum($this->getAlbum());
        $media->setFile($uploadedFile);
        $media->setDescription($uploadedFile->getClientOriginalName());
        $media->setCreated(new \DateTime());
        $media->setUserId(0);

        $mediaAttributes = new MediaAttribute();
        $media->setAttribute($mediaAttributes);

        return $media;
    }

    /**
     * @param Media $media
     * @param string $newFileName
     * @return Media
     */
    private function moveFile(Media $media, $newFileName)
    {
        $oldPath = $media->getPath();
        $newPath = 'media/image/' . $newFileName;

        $media->setPath($newPath);
        $media->setName($newFileName);
        $this->mediaService->rename($oldPath, $newPath);

        return $media;
    }

    /**
     * @return Album|null
     */
    private function getAlbum()
    {
        /** @var \Shopware\Models\Media\Repository $mediaRepository */
        $mediaRepository = $this->modelManager->getRepository(Album::class);
        return $mediaRepository->findOneBy([ 'name' => self::FRONTEND_USER_UPLOAD_ALBUM ]);
    }

    /**
     * @param Media $media
     * @return string
     */
    private function hashFileName(Media $media)
    {
        return uniqid($media->getName()) . '.' . $media->getExtension();
    }
}
