<?php
namespace Shopware\CustomModels\Ticket;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * History Model represent the s_ticket_support_history table
 *
 * @ORM\Entity
 * @ORM\Table(name="s_ticket_support_history")
 */
class History extends ModelEntity
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $ticketId
     *
     * @ORM\Column(name="ticketID", type="integer", nullable=false)
     */
    private $ticketId;

    /**
     * @var string $swUser
     *
     * @ORM\Column(name="swUser", type="string", nullable=false)
     */
    private $swUser = '';

    /**
     * @var string $subject
     *
     * @ORM\Column(name="subject", type="string", nullable=false)
     */
    private $subject;

    /**
     * @var string $message
     *
     * @ORM\Column(name="message", type="string", nullable=false)
     */
    private $message;

    /**
     * @var \DateTime $receipt
     *
     * @ORM\Column(name="receipt", type="datetime", nullable=false)
     */
    private $receipt;

    /**
     * @var string $supportType
     *
     * @ORM\Column(name="support_type", type="string", nullable=false)
     */
    private $supportType;

    /**
     * @var string $receiver
     *
     * @ORM\Column(name="receiver", type="string", nullable=false)
     */
    private $receiver = '';

    /**
     * @var string $direction
     *
     * @ORM\Column(name="direction", type="string", nullable=false)
     */
    private $direction;

    /**
     * @var integer $statusId
     *
     * @ORM\Column(name="statusId", type="integer", nullable=false)
     */
    private $statusId;

    /**
     * OWNING SIDE
     *
     * @var Support $ticket
     *
     * @ORM\ManyToOne(targetEntity="Shopware\CustomModels\Ticket\Support", inversedBy="history")
     * @ORM\JoinColumn(name="ticketID", referencedColumnName="id")
     */
    protected $ticket;

    /**
     * Returns the id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param \DateTime $receipt
     */
    public function setReceipt($receipt)
    {
        if (!$receipt instanceof \DateTime && strlen($receipt) > 0) {
            $receipt = new \DateTime($receipt);
        }
        $this->receipt = $receipt;
    }

    /**
     * @return \DateTime
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return string
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param string $supportType
     */
    public function setSupportType($supportType)
    {
        $this->supportType = $supportType;
    }

    /**
     * @return string
     */
    public function getSupportType()
    {
        return $this->supportType;
    }

    /**
     * @param string $swUser
     */
    public function setSwUser($swUser)
    {
        $this->swUser = $swUser;
    }

    /**
     * @return string
     */
    public function getSwUser()
    {
        return $this->swUser;
    }

    /**
     * @param Support $ticket
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * @return Support
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @return int
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param int $statusId
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    }
}
