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
 * @ORM\Table(name="s_plugin_customizing_options")
 * @ORM\Entity
 */
class Option extends ModelEntity
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
     * @var string $number
     * @ORM\Column(name="number", type="string", nullable=true)
     */
    private $number;

    /**
     * @var boolean $required
     * @ORM\Column(name="required", type="boolean")
     */
    private $required = false;

    /**
     * @var boolean $active
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = true;

    /**
     * @var string $position
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = 0;

    /**
     * @var int $groupId
     * @ORM\Column(name="group_id", type="integer", nullable=false)
     */
    private $groupId;

    /**
     * @var Group $group
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="options")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $group;

    /**
     * @var int $typeId
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     */
    private $typeId;

    /**
     * @var mixed $validators
     * @ORM\Column(name="validators", type="array", nullable=true)
     */
    private $validators;

    /**
     * @var mixed $defaultValue
     * @ORM\Column(name="default_value", type="array", nullable=true)
     */
    private $defaultValue;

    /**
     * @var mixed $emptyText
     * @ORM\Column(name="empty_text", type="string", nullable=true)
     */
    private $emptyText;

    /**
     * @var Type $type
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $type;

    /**
     * @var Value[] $values
     * @ORM\OneToMany(targetEntity="Value", mappedBy="option", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     */
    private $values;

    /**
     * @var int $maxUploads
     * @ORM\Column(name="max_uploads", type="integer", nullable=true)
     */
    private $maxUploads;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    /**
     * @return integer
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

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return \Shopware\CustomModels\Customizing\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return mixed
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @param mixed $validators
     */
    public function setValidators($validators)
    {
        $this->validators = $validators;
    }

    /**
     * @return ArrayCollection|Option[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param ArrayCollection|Option[] $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Type $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getEmptyText()
    {
        return $this->emptyText;
    }

    /**
     * @param mixed $emptyText
     */
    public function setEmptyText($emptyText)
    {
        $this->emptyText = $emptyText;
    }

    /**
     * @return int
     */
    public function getMaxUploads()
    {
        return $this->maxUploads;
    }

    /**
     * @param int $maxUploads
     */
    public function setMaxUploads($maxUploads)
    {
        $this->maxUploads = $maxUploads;
    }
}
