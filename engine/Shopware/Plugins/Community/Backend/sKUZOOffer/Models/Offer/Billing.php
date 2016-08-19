<?php



namespace   Shopware\CustomModels\Offer;
use         Shopware\Components\Model\ModelEntity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * Shopware offer billing model represents a single billing address of an offer.
 *
 * The Shopware offer billing model represents a row of the s_offer_billingaddress table.
 * The billing model data set from the Shopware\CustomModels\Offer\Repository.
 * One billing address has the follows associations:
 * <code>
 *   - Offer    =>  Shopware\CustomModels\Offer\Offer [1:1] [s_offer]
 *   - Customer =>  Shopware\Models\Customer\Customer [1:1] [s_user]
 * </code>
 * The s_offer_billingaddress table has the follows indices:
 * <code>
 *   - PRIMARY KEY (`id`)
 *   - UNIQUE KEY `FOREIGN` (`userID`)
 *   - UNIQUE KEY `FOREIGN` (`offerID`)
 * </code>
 *
 * @ORM\Entity
 * @ORM\Table(name="s_offer_billingaddress")
 * @ORM\HasLifecycleCallbacks
 */
class Billing extends ModelEntity
{
    /**
     * The id property is an identifier property which means
     * doctrine associations can be defined over this field
     *
     * @var integer $id
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * If of the associated offer. Used as foreign key for the
     * offer - billing association.
     *
     * @var integer $offerId
     * @ORM\Column(name="offerID", type="integer", nullable=false)
     */
    private $offerId;

    /**
     * If of the associated customer. Used as foreign key for the
     * customer - billing association.
     *
     * @var integer $customerId
     * @ORM\Column(name="userID", type="integer", nullable=true)
     */
    private $customerId = null;


    /**
     * Contains the id of the country. Used for the billing - country association.
     * @var integer $country
     * @ORM\Column(name="countryID", type="integer", nullable=false)
     */
    private $countryId = 0;


    /**
     * Contains the id of the country. Used for the billing - country association.
     * @var integer $stateId
     * @ORM\Column(name="stateID", type="integer", nullable=false)
     */
    private $stateId;

    /**
     * Contains the name of the billing address company
     * @var string $company
     * @ORM\Column(name="company", type="string", length=255, nullable=false)
     */
    private $company = '';

    /**
     * Contains the department name of the billing address company
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
     * Contains the unique customer number
     * @var string $number
     * @ORM\Column(name="customernumber", type="string", length=30, nullable=true)
     */
    private $number = '';

    /**
     * Contains the first name of the billing address
     * @var string $firstName
     * @ORM\Column(name="firstname", type="string", length=50, nullable=false)
     */
    private $firstName = '';

    /**
     * Contains the last name of the billing address
     * @var string $lastName
     * @ORM\Column(name="lastname", type="string", length=60, nullable=false)
     */
    private $lastName = '';

    /**
     * Contains the street name of the billing address
     * @var string $street
     * @ORM\Column(name="street", type="string", length=100, nullable=false)
     */
    private $street = '';



    /**
     * Contains the zip code of the billing address
     * @var string $zipCode
     * @ORM\Column(name="zipcode", type="string", length=50, nullable=false)
     */
    private $zipCode = '';

    /**
     * Contains the city name of the billing address
     * @var string $city
     * @ORM\Column(name="city", type="string", length=70, nullable=false)
     */
    private $city = '';

    /**
     * Contains the phone number of the billing address
     * @var string $phone
     * @ORM\Column(name="phone", type="string", length=40, nullable=false)
     */
    private $phone = '';

    /**
     * Contains the fax of the billing address
     * @var string $fax
     * @ORM\Column(name="fax", type="string", length=40, nullable=false)
     */
    private $fax = '';

    /**
     * Contains the vat id of the billing address
     * @var string $vatId
     * @ORM\Column(name="ustid", type="string", length=50, nullable=false)
     */
    private $vatId = '';


    /**
     * @var float $shippingTax
     *
     * @ORM\Column(name="shipping_tax", type="float", nullable=true)
     */
    private $shippingTax;

    /**
     * The customer property is the owning side of the association between customer and billing.
     * The association is joined over the billing userID and the customer id
     *
     * @var \Shopware\Models\Customer\Customer $customer
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Customer\Customer")
     * @ORM\JoinColumn(name="userID", referencedColumnName="id")
     */
    private $customer;

    /**
     * The offer property is the owning side of the association between offer and billing.
     * The association is joined over the billing offerID and the offer id
     *
     * @var \Shopware\CustomModels\Offer\Offer $offer
     * @ORM\OneToOne(targetEntity="Offer", inversedBy="offerBilling")
     * @ORM\JoinColumn(name="offerID", referencedColumnName="id")
     */
    private $offer;

    /**
     * @ORM\ManyToOne(targetEntity="\Shopware\Models\Country\Country")
     * @ORM\JoinColumn(name="countryID", referencedColumnName="id")
     * @var \Shopware\Models\Country\Country
     */
    private $country;



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
     * @return Billing
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
     * @return Billing
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
     * @return Billing
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
     * Setter function for the customer number column property.
     *
     * @param string $number
     * @return Billing
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Getter function for the customer number column property.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Setter function for the firstName column property.
     *
     * @param string $firstName
     * @return Billing
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
     * @return Billing
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
     * @return Billing
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
     * @return Billing
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
     * @return Billing
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
     * Setter function for the phone column property.
     *
     * @param string $phone
     * @return Billing
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Getter function for the phone column property.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Setter function for the fax column property.
     *
     * @param string $fax
     * @return Billing
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    /**
     * Getter function for the fax column property.
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Setter function for the vatId column property.
     * The vatId will be saved in the ustId table field.
     *
     * @param string $vatId
     * @return Billing
     */
    public function setVatId($vatId)
    {
        $this->vatId = $vatId;
        return $this;
    }

    /**
     * Getter function for the vatId column property.
     * The vatId is saved in the ustId table field.
     *
     * @return string
     */
    public function getVatId()
    {
        return $this->vatId;
    }


    /**
     * Setter shippingTax
     *
     * @param float $shippingTax
     * @return Billing
     */
    public function setShippingTax($shippingTax)
    {
        $this->shippingTax = $shippingTax;
        return $this;
    }

    /**
     * Getter shippingTax
     *
     * @return float
     */
    public function getShippingTax()
    {
        return $this->shippingTax;
    }



    /**
     * Returns the instance of the Shopware\Models\Customer\Customer model which
     * contains all data about the customer. The association is defined over
     * the Customer.billing property (INVERSE SIDE) and the Billing.customer (OWNING SIDE) property.
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
     * the Customer.billing property (INVERSE SIDE) and the Billing.customer (OWNING SIDE) property.
     * The customer data is joined over the s_user.id field.
     *
     * @param \Shopware\Models\Customer\Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return \Shopware\CustomModels\Offer\Offer
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param \Shopware\CustomModels\Offer\Offer $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }

    /**
     * Getter function for country
     *
     * @return country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Setter function shippingTax
     *
     * @param  $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    

}
