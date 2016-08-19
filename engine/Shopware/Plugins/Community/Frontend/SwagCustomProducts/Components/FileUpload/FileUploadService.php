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

use Shopware\Models\Media\Media;
use Shopware_Components_Snippet_Manager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FileUploadService
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * @var FileSizeFormatter
     */
    private $fileSizeFormatter;
    /**
     * @var FileTypeWhitelist
     */
    private $fileTypeWhitelist;

    /**
     * @param ValidatorInterface $validatorInterface
     * @param Shopware_Components_Snippet_Manager $snippetManager
     * @param Uploader $uploader
     * @param FileSizeFormatter $fileSizeFormatter
     * @param FileTypeWhitelist $fileTypeWhitelist
     */
    public function __construct(
        ValidatorInterface $validatorInterface,
        Shopware_Components_Snippet_Manager $snippetManager,
        Uploader $uploader,
        FileSizeFormatter $fileSizeFormatter,
        FileTypeWhitelist $fileTypeWhitelist
    ) {
        $this->validator = $validatorInterface;
        $this->snippetManager = $snippetManager;
        $this->uploader = $uploader;
        $this->fileSizeFormatter = $fileSizeFormatter;
        $this->fileTypeWhitelist = $fileTypeWhitelist;
    }

    /**
     * @param FileBag $fileBag
     * @return \Shopware\Models\Media\Media[]
     */
    public function upload(FileBag $fileBag)
    {
        /** @var Media[] $uploadedMedia */
        $uploadedMedia = [];

        /** @var UploadedFile[] $uploadedFiles */
        foreach ($fileBag as $uploadedFiles) {
            foreach ($uploadedFiles as $uploadedFile) {
                $uploadedMedia[] = $this->uploader->upload($uploadedFile);
            }
        }

        return $uploadedMedia;
    }

    /**
     * Validates all given files and collects all error messages.
     *
     * @param FileBag $fileBag
     * @param FileConfigStruct $fileConfigStruct
     * @throws FileUploadException
     */
    public function validate(FileBag $fileBag, FileConfigStruct $fileConfigStruct)
    {
        $this->validateMaxFiles($fileBag, $fileConfigStruct);

        $errors = [];

        /** @var UploadedFile[] $uploadedFiles */
        foreach ($fileBag as $uploadedFiles) {
            foreach ($uploadedFiles as $uploadedFile) {
                $constraint = $this->createFileConstraint($fileConfigStruct, $uploadedFile);

                /** @var ConstraintViolationList $violations */
                $violations = $this->validator->validate($this->getFilePath($uploadedFile), $constraint);

                /** @var ConstraintViolationInterface $violation */
                foreach ($violations->getIterator() as $violation) {
                    $errors[] = new FileUploadError(
                        $violation->getMessage(),
                        $uploadedFile->getClientOriginalName()
                    );
                }
            }
        }

        if (!empty($errors)) {
            throw new FileUploadException($errors);
        }
    }

    /**
     * @param FileConfigStruct $fileConfigStruct
     * @param UploadedFile $uploadedFile
     * @return File
     */
    private function createFileConstraint(FileConfigStruct $fileConfigStruct, UploadedFile $uploadedFile)
    {
        return new File([
            'maxSize' => (int) $fileConfigStruct->getMaxSize(),
            'mimeTypes' => $this->fileTypeWhitelist->getMimeTypeWhitelist($fileConfigStruct->getType()),
            'maxSizeMessage' => sprintf(
                $this->getErrorMessage('detail/validate/file_size'),
                $uploadedFile->getClientOriginalName(),
                $this->fileSizeFormatter->formatBytes($fileConfigStruct->getMaxSize())
            ),
            'mimeTypesMessage' => sprintf(
                $this->getErrorMessage('detail/validate/file_type'),
                $uploadedFile->getClientOriginalName(),
                implode(', ', $this->fileTypeWhitelist->getExtensionWhitelist($fileConfigStruct->getType()))
            )
        ]);
    }

    /**
     * @param FileBag $fileBag
     * @param FileConfigStruct $fileConfigStruct
     * @throws MaxFilesException
     */
    private function validateMaxFiles(FileBag $fileBag, FileConfigStruct $fileConfigStruct)
    {
        $fileAmount = $this->countUploadedFiles($fileBag);

        if ($fileAmount > $fileConfigStruct->getMaxFiles()) {
            throw new MaxFilesException($this->getMaxFileErrorMessage($fileConfigStruct));
        }
    }

    /**
     * @param FileBag $fileBag
     * @return int
     */
    private function countUploadedFiles(FileBag $fileBag)
    {
        $fileCounter = 0;
        foreach ($fileBag as $uploadedFiles) {
            $fileCounter += count($uploadedFiles);
        }

        return $fileCounter;
    }

    /**
     * @param string $snippet
     * @return string
     */
    private function getErrorMessage($snippet)
    {
        return $this->snippetManager->getNamespace('frontend/detail/option')->get($snippet);
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string
     */
    private function getFilePath(UploadedFile $uploadedFile)
    {
        return $uploadedFile->getPath() . '/' . $uploadedFile->getFilename();
    }

    /**
     * @param FileConfigStruct $fileConfigStruct
     * @return string
     */
    private function getMaxFileErrorMessage(FileConfigStruct $fileConfigStruct)
    {
        $errorMessage = $this->getErrorMessage('detail/validate/max_files');

        return sprintf($errorMessage, $fileConfigStruct->getMaxFiles());
    }
}
