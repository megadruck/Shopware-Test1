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

namespace Shopware\CustomModels\Customizing\Charge;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="s_plugin_customizing_charge_values")
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
     * @var integer $from
     * @ORM\Column(name="`from`", type="integer", nullable=false)
     */
    private $from = 0;

    /**
     * @var float $value
     * @ORM\Column(name="value", type="float", nullable=false)
     */
    private $value;

    /**
     * @var Item $item
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="values")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

    /**
     * @var \Shopware\Models\Customer\Group $customerGroup
     *
     * @ORM\OneToOne(targetEntity="\Shopware\Models\Customer\Group")
     * @ORM\JoinColumn(name="customer_group_id", referencedColumnName="groupkey")
     */
    private $customerGroup;

    /**
     * @var integer $from
     * @ORM\Column(name="customer_group_id", type="integer", nullable=false)
     */
    private $customerGroupId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param int $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return \Shopware\Models\Customer\Group
     */
    public function getCustomerGroup()
    {
        return $this->customerGroup;
    }

    /**
     * @param \Shopware\Models\Customer\Group $customerGroup
     */
    public function setCustomerGroup($customerGroup)
    {
        $this->customerGroup = $customerGroup;
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
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->customerGroupId = $customerGroupId;
    }
}
