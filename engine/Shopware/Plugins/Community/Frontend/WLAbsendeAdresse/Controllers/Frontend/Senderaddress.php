<?php

use Doctrine\ORM\Query;
use Shopware\Bundle\AccountBundle\Form\Account\AddressFormType;
use Shopware\Bundle\AccountBundle\Service\AddressServiceInterface;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\AddressRepository;
use Shopware\Models\Customer\Customer;
use Symfony\Component\Form\FormInterface;


class Shopware_Controllers_Frontend_Senderaddress extends Enlight_Controller_Action
{
    /**
     * @var sAdmin
     */
    protected $admin;

    /**
     * @var AddressRepository
     */
    protected $addressRepository;

    /**
     * @var AddressServiceInterface
     */
    protected $addressService;

    /**
     * Pre dispatch method
     */
    public function preDispatch()
    {
        $this->admin = Shopware()->Modules()->Admin();
        $this->addressRepository = $this->get('models')->getRepository(Address::class);
        $this->addressService = $this->get('shopware_account.address_service');

        $this->View()->assign('sUserLoggedIn', $this->admin->sCheckUser());

        if (!$this->View()->getAssign('sUserLoggedIn')) {
            $this->forward('index', 'register');
            return;
        }

        $this->View()->assign('sUserData', $this->admin->sGetUserData());
        $this->View()->assign('sAction', $this->Request()->getActionName());
    }

    /**
     * Address listing
     */
    public function indexAction()
    {
        $addresses = $this->addressRepository->getListArray($this->get('session')->get('sUserId'));

        $this->View()->assign('error', $this->Request()->getParam('error'));
        $this->View()->assign('success', $this->Request()->getParam('success'));
        $this->View()->assign('addresses', $addresses);
    }

    /**
     * Shortcut action for more fluent urls
     */
    public function createAction()
    {
        $address = new Address();
        $form = $this->createForm(AddressFormType::class, $address);
        $form->handleRequest($this->Request());

        if ($form->isValid()) {
            $userId = $this->get('session')->get('sUserId');
            $customer = $this->get('models')->find(Customer::class, $userId);

            $this->addressService->create($address, $customer);

            if (!empty($address->getAdditional()['setDefaultBillingAddress'])) {
                $this->addressService->setDefaultBillingAddress($address);
            }

            if (!empty($address->getAdditional()['setDefaultShippingAddress'])) {
                $this->addressService->setDefaultShippingAddress($address);
            }

            if ($this->Request()->getParam('sTarget', null)) {
                $action = $this->Request()->getParam('sTargetAction', 'index') ? : 'index';
                $this->redirect([
                    'controller' => $this->Request()->getParam('sTarget'),
                    'action' => $action,
                    'success' => 'address'
                ]);
                return;
            }

            $this->redirect(['action' => 'index', 'success' => 'create']);
            return;
        }

        $this->View()->assign($this->getFormViewData($form));
    }

    /**
     * Shortcut action for more fluent urls
     */
    public function editAction()
    {
        $userId = $this->get('session')->get('sUserId');
        $addressId = $this->Request()->getParam('id', null);
        $address = $this->addressRepository->getOneByUser($addressId, $userId);

        $form = $this->createForm(AddressFormType::class, $address);
        $form->handleRequest($this->Request());

        if ($form->isValid()) {
            $this->addressService->update($address);

            if (!empty($address->getAdditional()['setDefaultBillingAddress'])) {
                $this->addressService->setDefaultBillingAddress($address);
            }

            if (!empty($address->getAdditional()['setDefaultShippingAddress'])) {
                $this->addressService->setDefaultShippingAddress($address);
            }

            if ($this->Request()->getParam('sTarget')) {
                $action = $this->Request()->getParam('sTargetAction', 'index') ? : 'index';
                $this->redirect([
                    'controller' => $this->Request()->getParam('sTarget'),
                    'action' => $action,
                    'success' => 'address'
                ]);
                return;
            }

            $this->redirect(['action' => 'index', 'success' => 'update']);
            return;
        }

        $this->View()->assign($this->getFormViewData($form));
    }

    /**
     * Shortcut action for more fluent urls
     */
    public function clearSenderAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $userId =  Shopware()->Session()->sUserId;
        $db = Shopware()->Db();
        $db->query("DELETE FROM a_wluser_senderaddress WHERE userID = " . $userId);
    }

    /**
     * Delete confirm action
     */
    public function deleteAction()
    {
        $userId = $this->get('session')->get('sUserId');
        $addressId = $this->Request()->getParam('id', null);

        $address = $this->addressRepository->getOneByUser($addressId, $userId);

        if ($this->Request()->isPost()) {
            $this->addressService->delete($address);

            $this->redirect(['action' => 'index', 'success' => 'delete']);
            return;
        }

        $addressView = $this->get('models')->toArray($address);
        $addressView['country'] = $this->get('models')->toArray($address->getCountry());
        $addressView['state'] = $this->get('models')->toArray($address->getState());
        $addressView['attribute'] = $this->get('models')->toArray($address->getAttribute());

        $this->View()->assign('address', $addressView);
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getFormViewData(FormInterface $form)
    {
        $errorFlags = [];
        $errorMessages = [];
        $viewData = [];

        foreach ($form->getErrors(true) as $error) {
            $errorFlags[$error->getOrigin()->getName()] = true;
            $errorMessages[] = $this->get('snippets')->getNamespace('frontend/account/internalMessages')
                ->get('ErrorFillIn', 'Please fill in all red fields');
        }

        $errorMessages = array_unique($errorMessages);

        /** @var Address $address */
        $address = $form->getViewData();

        $formData = array_merge(
            $this->get('models')->toArray($address),
            ['attribute' => $this->get('models')->toArray($address->getAttribute())],
            ['additional' => $address->getAdditional()],
            $form->getExtraData()
        );

        $viewData['error_flags'] = $errorFlags;
        $viewData['error_messages'] = $errorMessages;
        $viewData['countryList'] = $this->admin->sGetCountryList();
        $viewData['formData'] = $formData;
        $viewData['sTarget'] = $this->Request()->getParam('sTarget', null);
        $viewData['sTargetAction'] = $this->Request()->getParam('sTargetAction', null);
        $viewData['extraData'] = $this->Request()->getParam('extraData', []);

        return $viewData;
    }

    /**
     * Sets the default shipping address
     */
    public function setDefaultShippingAddressAction()
    {
        $userId = $this->get('session')->get('sUserId');
        $addressId = $this->Request()->getParam('addressId', null);

        $address = $this->addressRepository->getOneByUser($addressId, $userId);

        if (!$this->Request()->isPost()) {
            $this->redirect(['action' => 'index']);
            return;
        }

        $this->addressService->setDefaultShippingAddress($address);

        $this->redirect(['action' => 'index', 'success' => 'default_shipping']);
    }

    /**
     * Sets the default shipping address
     */
    public function setDefaultBillingAddressAction()
    {
        $userId = $this->get('session')->get('sUserId');
        $addressId = $this->Request()->getParam('addressId', null);

        $address = $this->addressRepository->getOneByUser($addressId, $userId);

        if (!$this->Request()->isPost()) {
            $this->redirect(['action' => 'index']);
            return;
        }

        $this->addressService->setDefaultBillingAddress($address);

        $this->redirect(['action' => 'index', 'success' => 'default_billing']);
    }

    /**
     * Selection of addresses for the current logged-in customer
     */
    public function ajaxSelectionAction()
    {
        $addressRepository = Shopware()->Models()->getRepository(Address::class);
        $addresses = $addressRepository->getListArray($this->get('session')->get('sUserId'));
        $activeAddressId = $this->Request()->getParam('id', null);
        $extraData = $this->Request()->getParam('extraData', []);

        if (!empty($activeAddressId)) {
            foreach ($addresses as $key => $address) {
                if ($address['id'] == $activeAddressId) {
                    unset($addresses[$key]);
                }
            }
        }

        $this->View()->assign('addresses', $addresses);
        $this->View()->assign('activeAddressId', $activeAddressId);
        $this->View()->assign('extraData', $extraData);
    }

    /**
     * Show address form
     */
    public function ajaxEditorAction()
    {
        $userId = $this->get('session')->get('sUserId');
        $addressId = $this->Request()->getParam('id', null);

        if ($addressId) {
            $address = $this->addressRepository->getOneByUser($addressId, $userId);
        } else {
            $address = new Address();
        }

        $form = $this->createForm(AddressFormType::class, $address);
        $this->View()->assign($this->getFormViewData($form));
    }

    /**
     * Saves a new address and returns an envelope containing success and error indicators
     *
     * In addition, extraData[] can be send to do various actions after saving. See handleExtraData() for more
     * information.
     */
    public function ajaxSaveAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $response = ['success' => true, 'errors' => [], 'data' => []];

        $userId = $this->get('session')->get('sUserId');
        $addressId = $this->Request()->getPost('id', null);
        $extraData = $this->Request()->getParam('extraData', []);

        if ($this->Request()->getParam('saveAction') === 'update') {
            $address = $this->addressRepository->getOneByUser($addressId, $userId);
        } else {
            $address = new Address();
        }

        $form = $this->createForm(AddressFormType::class, $address);
        $form->handleRequest($this->Request());

        if ($form->isValid()) {
            if ($address->getId()) {
                $this->addressService->update($address);
            } else {
                $customer = $this->get('models')->find(Customer::class, $userId);
                $this->addressService->create($address, $customer);
            }

            $this->handleExtraData($extraData, $address);

            $addressView = $this->get('models')->toArray($address);
            $addressView['country'] = $this->get('models')->toArray($address->getCountry());
            $addressView['state'] = $this->get('models')->toArray($address->getState());
            $addressView['attribute'] = $this->get('models')->toArray($address->getAttribute());
            $response['data'] = $addressView;
        } else {
            foreach ($form->getErrors(true) as $error) {
                $response['errors'][$error->getOrigin()->getName()] = $error->getMessage();
            }
        }

        $response['success'] = empty($response['errors']);

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody(json_encode($response));
    }

    /**
     * Do various actions based on extraData[] parameters
     */
    public function handleExtraAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $address = $this->addressRepository->getOneByUser(
            $this->Request()->getPost('id'),
            $this->get('session')->get('sUserId')
        );

        $data = $this->Request()->getParam('extraData', []);
        $this->handleExtraData($data, $address);
    }

    /**
     * Handle extra data, sent by the api request to do various actions afterwards
     *
     * - sessionKey, set a session variable named the value of the submitted sessionKey containing the address id.
     * - setDefaultBillingAddress, sets the address as new default billing address
     * - setDefaultShippingAddress, sets the address as new default shipping address
     *
     * @param array $extraData
     * @param Address $address
     */
    private function handleExtraData(array $extraData, Address $address)
    {

        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        if (!empty($extraData['sessionKey'])) {

            if ($extraData['addresstype'] == 'lieferadresse') {

                $keys = explode(",", $extraData['sessionKey']);
                foreach ($keys as $key) {
                    if (!$key) {
                        continue;
                    }

                    if ($key == 'checkoutShippingAddressId') {
                        $this->refreshSession($address);
                    }

                    $this->get('session')->offsetSet($key, $address->getId());
                }
            }



        } else {
            if ($extraData['addresstype'] == 'lieferadresse') {

                $db =  Shopware()->Db();
                $userID = Shopware()->Session()->sUserId;
                $db->query('UPDATE s_user SET default_shipping_address_id = ' .  $address->getId() . ' WHERE id = ' . $userID);

            }
        }

        if ($extraData['addresstype'] == 'absendeadresse') {
            $db =  Shopware()->Db();
            $userID = Shopware()->Session()->sUserId;
            $adressID = $this->getSenderAddressId($db, $userID);
            if($adressID != 0){
                $db->query('UPDATE a_wluser_senderaddress SET senderAdressID = ' .  $address->getId() . ' WHERE userID = ' . $userID);
            } else {
                $db->query("INSERT INTO `a_wluser_senderaddress` (`userID`, `senderAdressID`) VALUES ('" . $userID . "', '" . $address->getId() . "')");
            }
        }


        if (!empty($extraData['setDefaultBillingAddress'])) {
            $this->addressService->setDefaultBillingAddress($address);
        }

        if (!empty($extraData['setDefaultShippingAddress'])) {
            $this->addressService->setDefaultShippingAddress($address);
        }
    }




    /**
     * @param Address $address
     */
    private function refreshSession(Address $address)
    {
        $countryId = $address->getCountry()->getId();
        $stateId = $address->getState() ? $address->getState()->getId() : null;
        $areaId = $address->getCountry()->getArea() ? $address->getCountry()->getArea()->getId() : null;

        $this->get('session')->offsetSet('sCountry', $countryId);
        $this->get('session')->offsetSet('sState', $stateId);
        $this->get('session')->offsetSet('sArea', $areaId);

        $this->get('shopware_storefront.context_service')->initializeShopContext();
    }

    public function getSenderAddressId($db, $customerId){
        $sql = "SELECT senderAdressID FROM a_wluser_senderaddress WHERE userID=?";
        $userSenderAddresses = $db->fetchOne($sql, array($customerId));

        // keine vorhanden
        if(empty($userSenderAddresses) || $userSenderAddresses == 0 || $userSenderAddresses == null){
            return 0;
        }

        return number_format($userSenderAddresses);
    }
}
