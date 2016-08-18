<?php
 
namespace Shopware\CustomModels\Order;
use Shopware\Components\Model\ModelEntity,
Doctrine\ORM\Mapping AS ORM,
Symfony\Component\Validator\Constraints as Assert,
Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="a_wluser_senderaddress")
 */
class UserSenderAddress extends  ModelEntity {
	
	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(name="userID", type="integer", nullable=false)
	 */
    protected $userID;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="senderAdressID", type="integer", nullable=false)
	 */
	protected $senderAdressID;
	

	
	public function getUserID() {
		return $this->userID;
	}
	public function setUserID($userID) {
		$this->userID = $userID;
	}

	public function getSenderAdressID() {
		return $this->senderAdressID;
	}
	public function setSenderAdressID($senderAdressID) {
		$this->senderAdressID = $senderAdressID;
	}
}
