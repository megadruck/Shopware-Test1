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

namespace ShopwarePlugins\SwagCustomProducts\Components\Inquiry;

use ShopwarePlugins\SwagCustomProducts\Components\Inquiry\Strategy\SelectedValueStrategy;
use ShopwarePlugins\SwagCustomProducts\Components\Inquiry\Strategy\ValuesStrategy;

class InquiryService
{
    /**
     * @var SelectedValueStrategy
     */
    private $selectedValueStrategy;

    /**
     * @var ValuesStrategy
     */
    private $valuesStrategy;

    /**
     * @param SelectedValueStrategy $selectedValueStrategy
     * @param ValuesStrategy $valuesStrategy
     */
    public function __construct(SelectedValueStrategy $selectedValueStrategy, ValuesStrategy $valuesStrategy)
    {
        $this->selectedValueStrategy = $selectedValueStrategy;
        $this->valuesStrategy = $valuesStrategy;
    }

    /**
     * @param array $data
     * @param bool $couldContainValues
     * @return string
     */
    public function getMessage(array $data, $couldContainValues = false)
    {
        if ($couldContainValues) {
            return $this->valuesStrategy->generateMessage($data);
        }
        return $this->selectedValueStrategy->generateMessage($data);
    }
}
