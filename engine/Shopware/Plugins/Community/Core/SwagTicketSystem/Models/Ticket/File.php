<?php
namespace Shopware\CustomModels\Ticket;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * File Model represent the s_ticket_support_files table
 *
 * @ORM\Entity
 * @ORM\Table(name="s_ticket_support_files")
 */
class File extends ModelEntity
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $answerId
     *
     * @ORM\Column(name="answer_id", type="integer", nullable=false)
     */
    private $answerId;

    /**
     * @var integer $ticketId
     *
     * @ORM\Column(name="ticket_id", type="integer", nullable=false)
     */
    private $ticketId;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string $hash
     *
     * @ORM\Column(name="hash", type="string", nullable=false)
     */
    private $hash;

    /**
     * @var string $location
     *
     * @ORM\Column(name="location", type="string")
     */
    private $location;

    /**
     * @var \DateTime $uploadDate
     *
     * @ORM\Column(name="uploadDate", type="datetime")
     */
    private $uploadDate;

    /**
     * Returns the id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set answer id
     *
     * @param integer $answerId
     */
    public function setAnswerId($answerId)
    {
        $this->answerId = $answerId;
    }

    /**
     * Return answer id
     *
     * @return string
     */
    public function getAnswerId()
    {
        return $this->answerId;
    }

    /**
     * Set ticket id
     *
     * @param integer $ticketId
     */
    public function setTicketId($ticketId)
    {
        $this->ticketId = $ticketId;
    }

    /**
     * Return ticket id
     *
     * @return integer
     */
    public function getTicketId()
    {
        return $this->ticketId;
    }

    /**
     * Set file name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return file name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set file hash
     *
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Return file hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set file hash
     *
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Return file hash
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set upload file date
     *
     * @param \DateTime|string $date
     * @return File
     */
    public function setUploadDate($date = 'now')
    {
        if (!($date instanceof \DateTime)) {
            $this->uploadDate = new \DateTime($date);
        } else {
            $this->uploadDate = $date;
        }

        return $this;
    }

    /**
     * Get file upload date
     *
     * @return \DateTime
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }
}
