<?php

namespace Shopware\CustomModels\Order;
use Shopware\Components\Model\ModelEntity,
Doctrine\ORM\Mapping AS ORM,
Symfony\Component\Validator\Constraints as Assert,
Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="a_wlorder_senderaddress")
 */
class OrderSenderAddress extends  ModelEntity {


	/**
	 * The id property is an identifier property which means
	 * doctrine associations can be defined over this field
	 *
	 * @var integer $id
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;


	/**
	 * Contains the name of the shipping address company
	 * @var string $company
	 * @ORM\Column(name="company", type="string", length=255, nullable=false)
	 */
	private $company = '';

	/**
	 * Contains the department name of the shipping address company
	 * @var string $department
	 * @ORM\Column(name="department", type="string", length=35, nullable=false)
	 */
	private $department = '';

	/**
	 * Contains the customer salutation (Mr, Ms, Company)
	 * @var string $salutation
	 * @ORM\Column(name="salutation", type="string", length=30, nullable=false)
	 */
	private $salutation = '';

	/**
	 * Contains the first name of the shipping address
	 * @var string $firstName
	 * @ORM\Column(name="firstname", type="string", length=50, nullable=false)
	 */
	private $firstName = '';

	/**
	 * Contains the last name of the shipping address
	 * @var string $lastName
	 * @ORM\Column(name="lastname", type="string", length=60, nullable=false)
	 */
	private $lastName = '';

	/**
	 * Contains the street name of the shipping address
	 * @var string $street
	 * @ORM\Column(name="street", type="string", length=255, nullable=false)
	 */
	private $street = '';

	/**
	 * Contains the zip code of the shipping address
	 * @var string $zipCode
	 * @ORM\Column(name="zipcode", type="string", length=50, nullable=false)
	 */
	private $zipCode = '';

	/**
	 * Contains the city name of the shipping address
	 * @var string $city
	 * @ORM\Column(name="city", type="string", length=70, nullable=false)
	 */
	private $city = '';

	/**
	 * Contains the additional address line data
	 *
	 * @var string $additionalAddressLine1
	 * @ORM\Column(name="additional_address_line1", type="string", length=255, nullable=true)
	 */
	protected $additionalAddressLine1 = null;

	/**
	 * Contains the additional address line data 2
	 *
	 * @var string $additionalAddressLine2
	 * @ORM\Column(name="additional_address_line2", type="string", length=255, nullable=true)
	 */
	protected $additionalAddressLine2 = null;

	/**
	 * The customer property is the owning side of the association between customer and shipping.
	 * The association is joined over the shipping userID and the customer id
	 *
	 * @var \Shopware\Models\Customer\Customer $customer
	 * @ORM\ManyToOne(targetEntity="Shopware\Models\Customer\Customer")
	 * @ORM\JoinColumn(name="userID", referencedColumnName="id")
	 */
	protected $customer;

	/**
	 * @ORM\ManyToOne(targetEntity="\Shopware\Models\Country\Country")
	 * @ORM\JoinColumn(name="countryID", referencedColumnName="id")
	 * @var \Shopware\Models\Country\Country $country
	 */
	protected $country;

	/**
	 * @ORM\ManyToOne(targetEntity="\Shopware\Models\Country\State")
	 * @ORM\JoinColumn(name="stateID", referencedColumnName="id")
	 * @var \Shopware\Models\Country\State
	 */
	private $state;

	/**
	 * @ORM\OneToOne(targetEntity="Shopware\Models\Order\Order")
	 * @ORM\JoinColumn(name="orderID", referencedColumnName="id")
	 */
	protected $order;



	/**
	 * Getter function for the unique id identifier property
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Setter function for the company column property
	 *
	 * @param string $company
	 * @return Shipping
	 */
	public function setCompany($company)
	{
		$this->company = $company;
		return $this;
	}

	/**
	 * Getter function for the company column property.
	 *
	 * @return string
	 */
	public function getCompany()
	{
		return $this->company;
	}

	/**
	 * Setter function for the department column property.
	 *
	 * @param string $department
	 * @return Shipping
	 */
	public function setDepartment($department)
	{
		$this->department = $department;
		return $this;
	}

	/**
	 * Getter function for the department column property.
	 *
	 * @return string
	 */
	public function getDepartment()
	{
		return $this->department;
	}


	/**
	 * Setter function for the salutation column property.
	 *
	 * @param string $salutation
	 * @return Shipping
	 */
	public function setSalutation($salutation)
	{
		$this->salutation = $salutation;
		return $this;
	}

	/**
	 * Getter function for the salutation column property.
	 *
	 * @return string
	 */
	public function getSalutation()
	{
		return $this->salutation;
	}

	/**
	 * Setter function for the firstName column property.
	 *
	 * @param string $firstName
	 * @return Shipping
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
		return $this;
	}

	/**
	 * Getter function for the firstName column property.
	 *
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * Setter function for the lastName column property.
	 *
	 * @param string $lastName
	 * @return Shipping
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
		return $this;
	}

	/**
	 * Getter function for the lastName column property.
	 *
	 * @return string
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * Setter function for the street column property.
	 *
	 * @param string $street
	 * @return Shipping
	 */
	public function setStreet($street)
	{
		$this->street = $street;
		return $this;
	}

	/**
	 * Getter function for the street column property.
	 *
	 * @return string
	 */
	public function getStreet()
	{
		return $this->street;
	}

	/**
	 * Setter function for the zipCode column property.
	 *
	 * @param string $zipCode
	 * @return Shipping
	 */
	public function setZipCode($zipCode)
	{
		$this->zipCode = $zipCode;
		return $this;
	}

	/**
	 * Getter function for the zipCode column property.
	 *
	 * @return string
	 */
	public function getZipCode()
	{
		return $this->zipCode;
	}

	/**
	 * Setter function for the city column property.
	 *
	 * @param string $city
	 * @return Shipping
	 */
	public function setCity($city)
	{
		$this->city = $city;
		return $this;
	}

	/**
	 * Getter function for the city column property.
	 *
	 * @return string
	 */
	public function getCity()
	{
		return $this->city;
	}


	/**
	 * Returns the instance of the Shopware\Models\Customer\Customer model which
	 * contains all data about the customer. The association is defined over
	 * the Customer.shipping property (INVERSE SIDE) and the Shipping.customer (OWNING SIDE) property.
	 * The customer data is joined over the s_user.id field.
	 *
	 * @return \Shopware\Models\Customer\Customer
	 */
	public function getCustomer()
	{
		return $this->customer;
	}

	/**
	 * Setter function for the customer association property which contains an instance of the Shopware\Models\Customer\Customer model which
	 * contains all data about the customer. The association is defined over
	 * the Customer.shipping property (INVERSE SIDE) and the Shipping.customer (OWNING SIDE) property.
	 * The customer data is joined over the s_user.id field.
	 *
	 * @param \Shopware\Models\Customer\Customer $customer
	 */
	public function setCustomer($customer)
	{
		$this->customer = $customer;
	}

	/**
	 * @return \Shopware\Models\Order\Order
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @param \Shopware\Models\Order\Order $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}

	/**
	 * @return int
	 */
	public function getCountryId()
	{
		return $this->countryId;
	}

	/**
	 * @param int $countryId
	 */
	public function setCountryId($countryId)
	{
		$this->countryId = $countryId;
	}

	/**
	 * @return int
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}

	/**
	 * @param int $orderId
	 */
	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
	}

	/**
	 * @return int
	 */
	public function getStateId()
	{
		return $this->stateId;
	}

	/**
	 * @param int $stateId
	 */
	public function setStateId($stateId)
	{
		$this->stateId = $stateId;
	}

	/**
	 * @return int
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}

	/**
	 * @param int $customerId
	 */
	public function setCustomerId($customerId)
	{
		$this->customerId = $customerId;
	}

	/**
	 * @return \Shopware\Models\Country\Country
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * @param \Shopware\Models\Country\Country $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}

	/**
	 * @return \Shopware\Models\Country\State
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param \Shopware\Models\Country\State $state
	 */
	public function setState($state)
	{
		$this->state = $state;
	}




	/**
	 * Setter function for the setAdditionalAddressLine2 column property.
	 *
	 * @param string $additionalAddressLine2
	 */
	public function setAdditionalAddressLine2($additionalAddressLine2)
	{
		$this->additionalAddressLine2 = $additionalAddressLine2;
	}

	/**
	 * Getter function for the getAdditionalAddressLine2 column property.
	 *
	 * @return string
	 */
	public function getAdditionalAddressLine2()
	{
		return $this->additionalAddressLine2;
	}

	/**
	 * Setter function for the setAdditionalAddressLine1 column property.
	 *
	 * @param string $additionalAddressLine1
	 */
	public function setAdditionalAddressLine1($additionalAddressLine1)
	{
		$this->additionalAddressLine1 = $additionalAddressLine1;
	}

	/**
	 * Getter function for the getAdditionalAddressLine1 column property.
	 *
	 * @return string
	 */
	public function getAdditionalAddressLine1()
	{
		return $this->additionalAddressLine1;
	}
}
