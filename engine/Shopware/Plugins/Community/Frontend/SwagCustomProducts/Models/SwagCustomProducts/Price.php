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

namespace Shopware\CustomModels\SwagCustomProducts;

use JsonSerializable;
use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="s_plugin_custom_products_price", indexes={@ORM\Index(name="search_idx", columns={"tax_id", "option_id", "value_id"})})
 * @ORM\Entity()
 */
class Price extends ModelEntity implements JsonSerializable
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var float $id
     * @ORM\Column(name="surcharge", type="float", scale=5, nullable=true)
     */
    protected $surcharge;

    /**
     * @var integer $taxId
     * @ORM\Column(name="tax_id", type="integer")
     */
    protected $taxId;

    /**
     * @var string $customerGroupName
     * @ORM\Column(name="customer_group_name", type="string", nullable=false)
     */
    protected $customerGroupName;

    /**
     * @var integer $customerGroupId
     * @ORM\Column(name="customer_group_id", type="integer")
     */
    protected $customerGroupId;

    /**
     * @var integer|null $optionId
     * @ORM\Column(name="option_id", type="integer", nullable=true)
     */
    protected $optionId;

    /**
     * @var \Shopware\CustomModels\SwagCustomProducts\Option
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\SwagCustomProducts\Option")
     * @ORM\JoinColumn(name="option_id", referencedColumnName="id")
     */
    protected $option;

    /**
     * @var integer|null $valueId
     * @ORM\Column(name="value_id", type="integer", nullable=true)
     */
    protected $valueId;

    /**
     * @var \Shopware\CustomModels\SwagCustomProducts\Value
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\SwagCustomProducts\Value")
     * @ORM\JoinColumn(name="value_id", referencedColumnName="id")
     */
    protected $value;

    /**
     * Price Clone
     */
    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return float
     */
    public function getSurcharge()
    {
        return $this->surcharge;
    }

    /**
     * @param float $surcharge
     * @return $this
     */
    public function setSurcharge($surcharge)
    {
        $this->surcharge = $surcharge;
        return $this;
    }

    /**
     * @return int
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * @param int $taxId
     * @return $this
     */
    public function setTaxId($taxId)
    {
        $this->taxId = $taxId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerGroupName()
    {
        return $this->customerGroupName;
    }

    /**
     * @param string $customerGroupName
     * @return $this
     */
    public function setCustomerGroupName($customerGroupName)
    {
        $this->customerGroupName = $customerGroupName;
        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    /**
     * @param int $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->customerGroupId = $customerGroupId;
        return $this;
    }

    /**
     * @return int
     */
    public function getOptionId()
    {
        return $this->optionId;
    }

    /**
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;
        return $this;
    }

    /**
     * @return Option
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param Option|array $option
     * @return $this
     */
    public function setOption($option)
    {
        return $this->setManyToOne($option, '\Shopware\CustomModels\SwagCustomProducts\Option', 'option');
    }

    /**
     * @return int|null
     */
    public function getValueId()
    {
        return $this->valueId;
    }

    /**
     * @param int|null $valueId
     * @return $this
     */
    public function setValueId($valueId)
    {
        $this->valueId = $valueId;
        return $this;
    }

    /**
     * @return Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Value|array $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setManyToOne($value, '\Shopware\CustomModels\SwagCustomProducts\Value', 'value');
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
