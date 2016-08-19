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

namespace ShopwarePlugins\SwagCustomProducts\Structs;

use ShopwarePlugins\SwagCustomProducts\Components\CamelCaseConverter;

class OptionValueStruct
{
    /** @var int $id */
    public $id;

    /** @var string $name */
    public $name;

    /** @var string $ordernumber */
    public $ordernumber;

    /** @var string $value */
    public $value;

    /** @var boolean $isDefaultValue */
    public $isDefaultValue;

    /** @var int $position */
    public $position;

    /** @var float $surcharge */
    public $surcharge;

    /** @var float $netPrice */
    public $netPrice;

    /** @var boolean $isOnceSurcharge */
    public $isOnceSurcharge;

    /** @var int $surchargeTaxRate*/
    public $surchargeTaxRate;

    /** @var int $taxId */
    public $taxId;

    /** @var  CamelCaseConverter */
    private $camelCaseConverter;

    /**
     * @param null|int $id
     * @param null|string $name
     * @param null|string $ordernumber
     * @param null|string $value
     * @param null|boolean $isDefaultValue
     * @param null|int $position
     * @param null|float $surcharge
     * @param null $netPrice
     * @param null|boolean $isOnceSurcharge
     * @param null|int $surchargeTaxRate
     * @param null $taxId
     */
    public function __construct(
        $id = null,
        $name = null,
        $ordernumber = null,
        $value = null,
        $isDefaultValue = null,
        $position = null,
        $surcharge = null,
        $netPrice = null,
        $isOnceSurcharge = null,
        $surchargeTaxRate = null,
        $taxId = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->ordernumber = $ordernumber;
        $this->value = $value;
        $this->isDefaultValue = $isDefaultValue;
        $this->position = $position;
        $this->surcharge = $surcharge;
        $this->netPrice = $netPrice;
        $this->isOnceSurcharge = $isOnceSurcharge;
        $this->surchargeTaxRate = $surchargeTaxRate;
        $this->taxId = $taxId;

        $this->camelCaseConverter = new CamelCaseConverter();
    }

    /**
     * @param array $optionArray
     * @return $this
     */
    public function fromArray(array $optionArray)
    {
        foreach ($optionArray as $key => $value) {
            $newKey = $this->camelCaseConverter->convert($key);
            $this->$newKey = $value;
        }

        return $this;
    }
}
