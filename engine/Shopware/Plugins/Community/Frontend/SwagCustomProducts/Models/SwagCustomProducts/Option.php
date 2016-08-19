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
use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="s_plugin_custom_products_option")
 * @ORM\Entity()
 */
class Option extends ModelEntity
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
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string $ordernumber
     *
     * @ORM\Column(name="ordernumber", type="string", nullable=true)
     */
    protected $ordernumber;

    /**
     * @var boolean $required
     *
     * @ORM\Column(name="required", type="boolean", nullable=true)
     */
    protected $required;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    protected $type;

    /**
     * @var integer $position
     *
     * @ORM\Column(name="`position`", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var string $defaultValue
     *
     * @ORM\Column(name="default_value", type="string", nullable=true)
     */
    protected $defaultValue;

    /**
     * @var string $placeholder
     *
     * @ORM\Column(name="placeholder", type="string", nullable=true)
     */
    protected $placeholder;

    /**
     * @var boolean $isOnceSurcharge
     *
     * @ORM\Column(name="is_once_surcharge", type="boolean", nullable=true)
     */
    protected $isOnceSurcharge;

    /**
     * @var integer $maxTextLength
     *
     * @ORM\Column(name="max_text_length", type="integer", nullable=true)
     */
    protected $maxTextLength;

    /**
     * @var integer $minValue
     *
     * @ORM\Column(name="min_value", type="integer", nullable=true)
     */
    protected $minValue;

    /**
     * @var integer $maxValue
     *
     * @ORM\Column(name="max_value", type="integer", nullable=true)
     */
    protected $maxValue;

    /**
     * @var integer $maxFileSize
     *
     * @ORM\Column(name="max_file_size", type="integer", nullable=true)
     */
    protected $maxFileSize;

    /**
     * @var \DateTime $minDate
     *
     * @ORM\Column(name="min_date", type="datetime", nullable=true)
     */
    protected $minDate;

    /**
     * @var \DateTime $maxDate
     *
     * @ORM\Column(name="max_date", type="datetime", nullable=true)
     */
    protected $maxDate;

    /**
     * @var integer $maxFiles
     *
     * @ORM\Column(name="max_files", type="integer", nullable=true)
     */
    protected $maxFiles;

    /**
     * @var integer $interval
     *
     * @ORM\Column(name="`interval`", type="integer", nullable=true)
     */
    protected $interval;

    /**
     * @var boolean $couldContainValues
     *
     * @ORM\Column(name="could_contain_values", type="boolean", nullable=false)
     */
    protected $couldContainValues;

    /**
     * @var integer $templateId
     *
     * @ORM\Column(name="template_id", type="integer", nullable=false)
     */
    protected $templateId;

    /**
     * @var Template $template
     *
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\SwagCustomProducts\Template", inversedBy="options")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    protected $template;

    /**
     * @var Value[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Shopware\CustomModels\SwagCustomProducts\Value",
     *     mappedBy="option",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $values;

    /**
     * @var boolean $allowsMultipleSelection
     *
     * @ORM\Column(name="allows_multiple_selection", type="boolean", nullable=true)
     */
    protected $allowsMultipleSelection;

    /**
     * @var Price[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Shopware\CustomModels\SwagCustomProducts\Price",
     *     mappedBy="option",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     */
    protected $prices;

    public function __construct()
    {
        $this->values = new ArrayCollection();
        $this->prices = new ArrayCollection();
    }

    /**
     * Option Clone
     */
    public function __clone()
    {
        $this->id = null;
        $this->ordernumber = '';

        $values = [];
        foreach ($this->values as $value) {
            /** @var Value $newValue */
            $newValue = clone $value;
            $newValue->setOption($this);
            $values[] = $newValue;
        }

        $prices = [];
        foreach ($this->prices as $price) {
            $newPrice = clone $price;
            $newPrice->setOption($this);
            $prices[] = $newPrice;
        }

        $this->values = new ArrayCollection($values);
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Template|array $template
     */
    public function setTemplate($template)
    {
        $this->setManyToOne(
            $template,
            '\Shopware\CustomModels\SwagCustomProducts\Template',
            'template'
        );
    }

    /**
     * @return integer
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param integer $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * @return Value[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param Value[] $values
     */
    public function setValues($values)
    {
        $this->setOneToMany(
            $values,
            '\Shopware\CustomModels\SwagCustomProducts\Value',
            'values',
            'option'
        );
    }

    /**
     * @return Value[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param Price[] $prices
     * @return ModelEntity
     */
    public function setPrices($prices)
    {
        return $this->setOneToMany(
            $prices,
            '\Shopware\CustomModels\SwagCustomProducts\Price',
            'prices',
            'option'
        );
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
    public function getMaxTextLength()
    {
        return $this->maxTextLength;
    }

    /**
     * @param integer $maxTextLength
     */
    public function setMaxTextLength($maxTextLength)
    {
        $this->maxTextLength = $maxTextLength;
    }

    /**
     * @return integer
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * @param integer $minValue
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * @return integer
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * @return \DateTime
     */
    public function getMinDate()
    {
        return $this->minDate;
    }

    /**
     * @param \DateTime $minDate
     */
    public function setMinDate($minDate)
    {
        $this->minDate = $minDate;
    }

    /**
     * @return \DateTime
     */
    public function getMaxDate()
    {
        return $this->maxDate;
    }

    /**
     * @param \DateTime $maxDate
     */
    public function setMaxDate($maxDate)
    {
        $this->maxDate = $maxDate;
    }

    /**
     * @param integer $maxValue
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * @return integer
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param integer $interval
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
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
     * @return boolean
     */
    public function getCouldContainValues()
    {
        return $this->couldContainValues;
    }

    /**
     * @param boolean $couldContainValues
     */
    public function setCouldContainValues($couldContainValues)
    {
        $this->couldContainValues = $couldContainValues;
    }

    /**
     * @return integer
     */
    public function getMaxFiles()
    {
        return $this->maxFiles;
    }

    /**
     * @param integer $maxFiles
     */
    public function setMaxFiles($maxFiles)
    {
        $this->maxFiles = $maxFiles;
    }

    /**
     * @return boolean
     */
    public function getAllowsMultipleSelection()
    {
        return $this->allowsMultipleSelection;
    }

    /**
     * @param boolean $allowsMultipleSelection
     */
    public function setAllowsMultipleSelection($allowsMultipleSelection)
    {
        $this->allowsMultipleSelection = $allowsMultipleSelection;
    }

    /**
     * @return integer
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * @param integer $maxFileSize
     */
    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }
}
