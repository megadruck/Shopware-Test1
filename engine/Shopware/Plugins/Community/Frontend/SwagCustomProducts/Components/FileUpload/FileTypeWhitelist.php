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

use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\FileUploadType;
use ShopwarePlugins\SwagCustomProducts\Components\Types\Types\ImageUploadType;

class FileTypeWhitelist
{
    /**
     * @var array
     */
    public static $extensionWhitelist = [
        'image' => [
            'png',
            'jpg',
            'jpeg',
            'tiff',
            'svg',
            'gif'
        ],
        'file' => [
            'png',
            'jpg',
            'jpeg',
            'tiff',
            'svg',
            'gif',
            'txt',
            'ai',
            'eps',
            'pdf'
        ]
    ];

    /**
     * @var array
     */
    public static $mimeTypeWhitelist = [
        'image' => [
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/tiff',
            'image/svg+xml',
            'image/gif'
        ],
        'file' => [
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/tiff',
            'image/svg+xml',
            'image/gif',
            'text/plain',
            'application/postscript',
            'application/pdf'
        ]
    ];

    /**
     * @param string $type
     * @return string|void
     */
    public function getMimeTypeWhitelist($type)
    {
        switch ($type) {
            case FileUploadType::TYPE:
                return self::$mimeTypeWhitelist['file'];
            case ImageUploadType::TYPE:
                return self::$mimeTypeWhitelist['image'];
            default:
                return;
        }
    }

    /**
     * @param string $type
     * @return string|void
     */
    public function getExtensionWhitelist($type)
    {
        switch ($type) {
            case FileUploadType::TYPE:
                return self::$extensionWhitelist['file'];
            case ImageUploadType::TYPE:
                return self::$extensionWhitelist['image'];
            default:
                return;
        }
    }
}
