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

namespace ShopwarePlugins\SwagCustomProducts\Components\DataConverter\Converter;

use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\ConverterInterface;

class ImageUploadConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertRequestData($data)
    {
        return [$data];
    }

    /**
     * {@inheritdoc}
     */
    public function convertBasketData(array $optionData, array $data)
    {
        $optionData['values'] = json_decode($data[0], true);
        return $optionData;
    }
}