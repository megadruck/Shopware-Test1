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

class OptionStruct
{
    /** @var int $id */
    public $id;

    /** @var string $name */
    public $name;

    /** @var string $ordernumber */
    public $ordernumber;

    /** @var boolean $required */
    public $required;

    /** @var string $type */
    public $type;

    /** @var int $position */
    public $position;

    /** @var string $defaultValue */
    public $defaultValue;

    /** @var string $placeholder */
    public $placeholder;

    /** @var float $surcharge */
    public $surcharge;

    /** @var float $netPrice */
    public $netPrice;

    /** @var boolean $isOnceSurcharge */
    public $isOnceSurcharge;

    /** @var float */
    public $surchargeTaxRate;

    /** @var int $maxTextLength */
    public $maxTextLength;

    /** @var int $minValue */
    public $minValue;

    /** @var int $maxValue */
    public $maxValue;

    /** @var int $interval */
    public $interval;

    /** @var int $templateId */
    public $templateId;

    /** @var int $taxId */
    public $taxId;

    /** @var OptionValueStruct[] $values */
    public $values;

    /** @var CamelCaseConverter $camelCaseConverter */
    private $camelCaseConverter;

    /**
     * @param null|integer $id
     * @param null|string $name
     * @param null|integer $ordernumber
     * @param null|boolean $required
     * @param null|string $type
     * @param null|integer $position
     * @param null|string $defaultValue
     * @param null|string $placeholder
     * @param null|float $surcharge
     * @param null|float $netPrice
     * @param null|boolean $isOnceSurcharge
     * @param null|integer $surchargeTaxRate
     * @param null|integer $maxTextLength
     * @param null|integer $minValue
     * @param null|integer $maxValue
     * @param null|integer $interval
     * @param null|integer $templateId
     * @param null|integer $taxId
     * @param null|OptionValueStruct[] $values
     */
    public function __construct(
        $id = null,
        $name = null,
        $ordernumber = null,
        $required = null,
        $type = null,
        $position = null,
        $defaultValue = null,
        $placeholder = null,
        $surcharge = null,
        $netPrice = null,
        $isOnceSurcharge = null,
        $surchargeTaxRate = null,
        $maxTextLength = null,
        $minValue = null,
        $maxValue = null,
        $interval = null,
        $templateId = null,
        $taxId = null,
        array $values = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->ordernumber = $ordernumber;
        $this->required = $required;
        $this->type = $type;
        $this->position = $position;
        $this->defaultValue = $defaultValue;
        $this->placeholder = $placeholder;
        $this->surcharge = $surcharge;
        $this->netPrice = $surcharge;
        $this->isOnceSurcharge = $isOnceSurcharge;
        $this->surchargeTaxRate = $surchargeTaxRate;
        $this->maxTextLength = $maxTextLength;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
        $this->interval = $interval;
        $this->templateId = $templateId;
        $this->values = $values;
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

        $this->values = $this->valuesFromArray($optionArray['values']);

        return $this;
    }

    /**
     * @param array $values
     * @return OptionValueStruct[]
     */
    private function valuesFromArray(array $values)
    {
        $valueArray = [];

        foreach ($values as $value) {
            $newValue = new OptionValueStruct();
            $valueArray[] = $newValue->fromArray($value);
        }

        return $valueArray;
    }
}
