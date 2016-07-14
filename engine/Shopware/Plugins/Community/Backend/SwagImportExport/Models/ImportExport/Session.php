<?php

/**
 * Shopware 4.2
 * Copyright © shopware AG
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
/**
 * Shopware ImportExport Plugin
 *
 * @category  Shopware
 * @package   Shopware\Components\Console\Command
 * @copyright Copyright (c) 2014, shopware AG (http://www.shopware.de)
 */

namespace Shopware\CustomModels\ImportExport;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Session Model
 *
 * @ORM\Table(name="s_import_export_session")
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\HasLifecycleCallbacks
 */
class Session extends ModelEntity
{
    /**
     * Primary Key - autoincrement value
     *
     * @var int $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Profile $profile
     *
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\ImportExport\Profile", cascade={"persist", "refresh"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $profile;

    /**
     * @var Logger $logger
     *
     * @ORM\OneToOne(targetEntity="Shopware\CustomModels\ImportExport\Logger", cascade={"persist", "refresh"})
     * @ORM\JoinColumn(name="log_id", referencedColumnName="id")
     */
    protected $logger;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=200)
     */
    protected $type;

    /**
     * @var string $ids
     *
     * @ORM\Column(name="ids", type="text", nullable=false)
     */
    protected $ids;

    /**
     * @var int $position
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    protected $position;

    /**
     * @var int $totalCount
     *
     * @ORM\Column(name="total_count", type="integer", nullable=false)
     */
    protected $totalCount;

    /**
     * @var string $userName
     *
     * @ORM\Column(name="username", type="string", length=200, nullable=true)
     */
    protected $userName;

    /**
     * @var string $fileName
     *
     * @ORM\Column(name="file_name", type="string", length=200)
     */
    protected $fileName;

    /**
     * @var string $format
     *
     * @ORM\Column(name="format", type="string", length=100)
     */
    protected $format;

    /**
     * Filesize of the file in bytes
     *
     * @var int $fileSize
     *
     * @ORM\Column(name="file_size", type="integer", nullable=true)
     */
    protected $fileSize;

    /**
     * @var string $state
     *
     * @ORM\Column(name="state", type="string", length=100)
     */
    protected $state = 'new';

    /**
     * @var \Datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Sets the profile object.
     *
     * @param Profile $profile
     * @return Session
     */
    public function setProfile(Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @param Logger $logger
     * @return Session
     */
    public function setLogger(Logger $logger = null)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param string $type
     * @return Session
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $ids
     * @return Session
     */
    public function setIds($ids)
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * @param int $position
     * @return Session
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @param int $totalCount
     * @return Session
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @param string $userName
     * @return Session
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @param string $fileName
     * @return Session
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @param string $format
     * @return Session
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @param int $fileSize
     * @return Session
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @param string $state
     * @return Session
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Session
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
