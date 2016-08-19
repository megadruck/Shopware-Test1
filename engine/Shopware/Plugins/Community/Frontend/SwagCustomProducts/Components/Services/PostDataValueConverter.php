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

namespace ShopwarePlugins\SwagCustomProducts\Components\Services;

use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\ConverterInterface;
use ShopwarePlugins\SwagCustomProducts\Components\DataConverter\Registry;

class PostDataValueConverter
{
    const REPLACE_STRING = "custom-option-id--";

    /**
     * @var Registry
     */
    private $converterRegistry;

    /**
     * @param Registry $converterRegistry
     */
    public function __construct(Registry $converterRegistry)
    {
        $this->converterRegistry = $converterRegistry;
    }

    /**
     * @param array $postData
     * @param array $options
     * @return array
     */
    public function convertPostData(array $postData, array $options)
    {
        $returnData = [];
        foreach ($postData as $key => $value) {
            if (strpos($key, self::REPLACE_STRING) !== false) {
                $id = $this->getIdFromKey($key);
                $option = $this->getOptionById($options, $id);

                /** @var ConverterInterface $converter */
                $converter = $this->converterRegistry->get($option['type']);
                $returnData[$id] = $converter->convertRequestData($value);
            }
        }

        return $returnData;
    }

    /**
     * @param array $data
     * @return array
     */
    public function convertPostDataBackward(array $data)
    {
        $responseData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                //Implode non media files
                if (!isset($value[0]['id'])) {
                    $value = implode(',', $value);
                }
            }

            $responseData[self::REPLACE_STRING . $key] = $value;
        }

        return $responseData;
    }

    /**
     * @param int
     * @return string
     */
    public function getIdFromKey($key)
    {
        return str_replace(self::REPLACE_STRING, '', $key);
    }

    /**
     * @param array $options
     * @param int $optionId
     * @return bool|array
     */
    private function getOptionById($options, $optionId)
    {
        foreach ($options as $option) {
            if ($option['id'] == $optionId) {
                return $option;
            }
        }

        return false;
    }
}
