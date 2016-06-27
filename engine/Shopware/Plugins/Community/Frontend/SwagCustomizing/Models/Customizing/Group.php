<?php
/**
 * Shopware 4.0
 * Copyright © 2013 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\CustomModels\Customizing;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="s_plugin_customizing_groups")
 * @ORM\Entity(repositoryClass="Repository")
 */
class Group extends ModelEntity
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var boolean $showName
     * @ORM\Column(name="show_name", type="boolean")
     */
    private $showName = true;

    /**
     * @var string $description
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var boolean $showDescription
     * @ORM\Column(name="show_description", type="boolean")
     */
    private $showDescription = true;

    /**
     * @var boolean $active
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @var \DateTime $added
     * @ORM\Column(name="added", type="datetime", nullable=false)
     */
    private $added;

    /**
     * @var \DateTime $changed
     * @ORM\Column(name="changed", type="datetime", nullable=false)
     */
    private $changed;

    /**
     * @var Option[] $options
     * @ORM\OneToMany(targetEntity="Option", mappedBy="group", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     */
    private $options;

    /**
     * @var \Shopware\Models\Article\Article[]
     * @ORM\ManyToMany(targetEntity="Shopware\Models\Article\Article")
     * @ORM\JoinTable(name="s_plugin_customizing_articles",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")}
     *      )
     */
    private $articles;

    /**
     * @var string $imagePath
     * @ORM\Column(name="image_path", type="string", nullable=true)
     */
    private $imagePath;

    /**
     * @var boolean $showGroupImage
     * @ORM\Column(name="show_group_image", type="boolean")
     */
    private $showGroupImage;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->added = new \DateTime();
        $this->changed = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return $this Group
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return $this Group
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
     * @return $this Group
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param \DateTime $added
     * @return $this Group
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param \DateTime $changed
     * @return $this Group
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;

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
     * @param Option[] $options
     * @return $this Group
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Shopware\Models\Article\Article[]
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|\Shopware\Models\Article\Article[] $articles
     * @return $this Group
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowName()
    {
        return $this->showName;
    }

    /**
     * @param boolean $showName
     * @return $this Group
     */
    public function setShowName($showName)
    {
        $this->showName = $showName;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowDescription()
    {
        return $this->showDescription;
    }

    /**
     * @param boolean $showDescription
     * @return $this Group
     */
    public function setShowDescription($showDescription)
    {
        $this->showDescription = $showDescription;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowGroupImage()
    {
        return $this->showGroupImage;
    }

    /**
     * @param boolean $showGroupImage
     * @return $this Group
     */
    public function setShowGroupImage($showGroupImage)
    {
        $this->showGroupImage = $showGroupImage;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @param mixed $imagePath
     * @return $this Group
     */
    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }
}
