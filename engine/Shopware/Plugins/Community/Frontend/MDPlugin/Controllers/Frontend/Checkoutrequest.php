<?php
/**
 * Copyright (c) 2016
 * Megadruck.de Produktions- und Vertriebs GmbH
 * Joerg Frintrop
 * j.frintrop@megadruck.de
 *
 * Eichendorffstrasse 34b
 * 26655 Westerstede
 * Tel. 04488 52540-25
 * Fax. 04488 52540-14
 *
 * www.megadruck.de
 */



class Shopware_Controllers_Frontend_Checkoutrequest extends Shopware_Controllers_Frontend_Checkout
{
    /**
     * /checkout
     */
    public function indexAction()
    {

       $rawBody = $this->Request()->getRawBody();
       $params = json_decode($rawBody, true); // jsonbody decodieren als array}
       mail('j.frintrop@megadruck.de', 'Debug_FORM', print_r($params , true),'FROM:edv@frintrop.com');
       parent:indexAction();

    }
}