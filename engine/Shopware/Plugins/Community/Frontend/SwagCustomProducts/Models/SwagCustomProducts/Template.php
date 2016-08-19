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
use Shopware\Models\Article\Article;
use Shopware\Models\Media\Media;

/**
 * @ORM\Table(name="s_plugin_custom_products_template")
 * @ORM\Entity()
 */
class Template extends ModelEntity
{
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $internalName
     * @ORM\Column(name="internal_name", type="string", nullable=false, length=255, unique=true)
     */
    private $internalName;

    /**
     * @var string $displayName
     * @ORM\Column(name="display_name", type="string", nullable=true)
     */
    private $displayName;

    /**
     * @var string $description
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int $mediaId
     * @ORM\Column(name="media_id", type="integer", nullable=true)
     */
    private $mediaId;

    /**
     * @var boolean $stepByStepConfigurator
     * @ORM\Column(name="step_by_step_configurator", type="boolean")
     */
    private $stepByStepConfigurator = false;

    /**
     * @var boolean $active
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active = false;

    /**
     * @var boolean $confirmInput
     * @ORM\Column(name="confirm_input", type="boolean")
     */
    private $confirmInput = false;

    /**
     * @var $options
     *
     * @ORM\OneToMany(
     *     targetEntity="Shopware\CustomModels\SwagCustomProducts\Option",
     *     orphanRemoval=true,
     *     mappedBy="template",
     *     cascade={"persist"}
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $options;

    /**
     * @var \Shopware\Models\Article\Article[] $articles
     *
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Article\Article")
     * @ORM\JoinTable(name="s_plugin_custom_products_template_product_relation",
     *      joinColumns={
     *          @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="article_id", referencedColumnName="id", unique=true)
     *      }
     * )
     */
    protected $articles;

    /**
     * @var Media $media
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Media\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=true)
     */
    private $media;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    /**
     * Template Clone
     */
    public function __clone()
    {
        $this->id = null;

        $options = [];
        foreach ($this->options as $option) {
            /** @var Option $option */
            $newOption = clone $option;
            $newOption->setTemplate($this);

            $options[] = $newOption;
        }

        $this->options = new ArrayCollection($options);
        $this->setArticles([]);
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
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * @param string $internalName
     * @return $this
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
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
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param Media $media
     * @return $this
     */
    public function setMedia($media)
    {
        $this->media = $media;
        return $this;
    }

    /**
     * @return int
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }

    /**
     * @param int $mediaId
     * @return $this
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getStepByStepConfigurator()
    {
        return $this->stepByStepConfigurator;
    }

    /**
     * @param boolean $stepByStepConfigurator
     * @return $this
     */
    public function setStepByStepConfigurator($stepByStepConfigurator)
    {
        $this->stepByStepConfigurator = $stepByStepConfigurator;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getConfirmInput()
    {
        return $this->confirmInput;
    }

    /**
     * @param boolean $confirmInput
     * @return $this
     */
    public function setConfirmInput($confirmInput)
    {
        $this->confirmInput = $confirmInput;
        return $this;
    }

    /**
     * @return \Shopware\Models\Article\Article[]
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @param \Shopware\Models\Article\Article[] $articles
     * @return $this
     */
    public function setArticles(array $articles)
    {
        $this->articles = $articles;
        return $this;
    }

    /**
     * @param Article $article
     * @return $this
     */
    public function addArticle(Article $article)
    {
        $this->articles->add($article);
        return $this;
    }

    /**
     * @return Option[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        return $this->setOneToMany(
            $options,
            '\Shopware\CustomModels\SwagCustomProducts\Option',
            'options',
            'template'
        );
    }

    /**
     * @param Option $option
     * @return $this
     */
    public function addOption(Option $option)
    {
        $this->options->add($option);
        return $this;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
