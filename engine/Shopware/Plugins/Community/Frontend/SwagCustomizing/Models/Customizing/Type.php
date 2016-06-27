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
 * @ORM\Table(name="s_plugin_customizing_types")
 * @ORM\Entity
 */
class Type extends ModelEntity
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
     * @var string $type
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string $parentId
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var Type $parent
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", nullable=true, referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @var Type[] $children
     * @ORM\OneToMany(targetEntity="Type", mappedBy="parent", cascade={"all"}))
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $children;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Type
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Type $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }
}
