<?php

namespace Shopware\Components\SwagImportExport\FileIO;

use Shopware\Components\SwagImportExport\Utils\FileHelper;

class ExcelFileWriter implements FileWriter
{
    /**
     * @var FileHelper $fileHelper
     */
    protected $fileHelper;

    /**
     * @param FileHelper $fileHelper
     */
    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * @param $fileName
     * @param $headerDara
     */
    public function writeHeader($fileName, $headerDara)
    {
    }

    /**
     * @param $fileName
     * @param $headerDara
     */
    public function writeRecords($fileName, $headerDara)
    {
    }

    /**
     * @param $fileName
     * @param $headerDara
     */
    public function writeFooter($fileName, $headerDara)
    {
    }
}
