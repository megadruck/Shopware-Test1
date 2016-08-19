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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Table(name="s_plugin_custom_products_value")
 * @ORM\Entity()
 */
class Value extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name;

    /**
     * @var string $ordernumber
     *
     * @ORM\Column(name="ordernumber", type="string", nullable=true)
     */
    protected $ordernumber;

    /**
     * @var string $value
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    protected $value;

    /**
     * @var boolean $defaultValue
     *
     * @ORM\Column(name="is_default_value", type="boolean", nullable=true)
     */
    protected $isDefaultValue;

    /**
     * @var integer $position
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var boolean $isOnceSurcharge
     *
     * @ORM\Column(name="is_once_surcharge", type="boolean", nullable=true)
     */
    protected $isOnceSurcharge;

    /**
     * @var integer $optionId
     *
     * @ORM\Column(name="option_id", type="integer")
     */
    protected $optionId;

    /**
     * @var Option
     *
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\SwagCustomProducts\Option")
     * @ORM\JoinColumn(name="option_id", referencedColumnName="id")
     */
    protected $option;

    /**
     * @var integer $mediaId
     *
     * @ORM\Column(name="media_id", type="integer", nullable=true)
     */
    private $mediaId;

    /**
     * @var string $seoTitle
     *
     * @ORM\Column(name="seo_title", type="string", nullable=true)
     */
    protected $seoTitle;

    /**
     * @var Price[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Shopware\CustomModels\SwagCustomProducts\Price",
     *     mappedBy="value",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    protected $prices;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    /**
     * Value Clone
     */
    public function __clone()
    {
        $this->id = null;
        $this->ordernumber = '';

        $prices = [];
        foreach ($this->prices as $price) {
            $new = clone $price;
            $new->setValue($this);
            $prices[] = $new;
        }
        $this->prices = new ArrayCollection($prices);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getOrdernumber()
    {
        return $this->ordernumber;
    }

    /**
     * @param string $ordernumber
     */
    public function setOrdernumber($ordernumber)
    {
        $this->ordernumber = $ordernumber;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return boolean
     */
    public function getIsDefaultValue()
    {
        return $this->isDefaultValue;
    }

    /**
     * @param boolean $isDefaultValue
     */
    public function setIsDefaultValue($isDefaultValue)
    {
        $this->isDefaultValue = $isDefaultValue;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return boolean
     */
    public function getIsOnceSurcharge()
    {
        return $this->isOnceSurcharge;
    }

    /**
     * @param boolean $isOnceSurcharge
     */
    public function setIsOnceSurcharge($isOnceSurcharge)
    {
        $this->isOnceSurcharge = $isOnceSurcharge;
    }

    /**
     * @return integer
     */
    public function getOptionId()
    {
        return $this->optionId;
    }

    /**
     * @param integer $optionId
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;
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
     */
    public function setOption($option)
    {
        $this->setManyToOne($option, '\Shopware\CustomModels\SwagCustomProducts\Option', 'option');
    }

    /**
     * @return integer
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * @param integer $mediaId
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
    }

    /**
     * @return string
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * @param string $seoTitle
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;
    }

    /**
     * @return Price[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param Price[] $prices
     * @return $this
     */
    public function setPrices($prices)
    {
        return $this->setOneToMany(
            $prices,
            '\Shopware\CustomModels\SwagCustomProducts\Price',
            'prices',
            'value'
        );
    }
}
