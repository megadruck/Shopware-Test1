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

/**
 * @ORM\Table(name="s_plugin_customizing_values")
 * @ORM\Entity
 */
class Value extends ModelEntity
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $value
     * @ORM\Column(name="value", type="string", nullable=false)
     */
    private $value;

    /**
     * @var string $description
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var boolean $number
     * @ORM\Column(name="number", type="string", nullable=true)
     */
    private $number;

    /**
     * @var string $position
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position = 0;

    /**
     * @var integer $cloneId
     * @ORM\Column(name="clone_id", type="integer", nullable=true)
     */
    private $cloneId;

    /**
     * @var Option $option
     * @ORM\ManyToOne(targetEntity="Option", inversedBy="values")
     * @ORM\JoinColumn(name="option_id", referencedColumnName="id")
     */
    private $option;

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
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param boolean $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
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
     * @return Option
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @param Option $option
     */
    public function setOption($option)
    {
        $this->option = $option;
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
     * @return int|null
     */
    public function getCloneId()
    {
        return $this->cloneId;
    }

    /**
     * @param int $cloneId
     */
    public function setCloneId($cloneId)
    {
        $this->cloneId = $cloneId;
    }
}
