<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Shopware_Plugins_Frontend_MDPlugin_Bootstrap 
    extends Shopware_Components_Plugin_Bootstrap
{
    public function getCapabilities()
    {
         return array(
        'install' => true,
        'update' => true,
        'enable' => true
    );
    }
 
    public function getLabel()
    {
        return 'Megadruck Anpassungen';
    }
 
    public function getVersion()
    {
        return '1.0.0';
    }
 
    public function getInfo()
    {
        return array(
        'version' => $this->getVersion(),
        'label' => $this->getLabel(),
        'supplier' => 'Megadruck.de',
        'description' => 'Core Anpassungen Megadruck',
        'support' => 'Megadruck.de',
        'link' => 'http://www.megadruck.de'
    );
    }
 
    public function install()
    {
        $this->registerEvents();
        return true;
    }
    
    
    
    private function registerEvents()
{
    $this->subscribeEvent(
        'Shopware_Modules_Basket_GetBasket_FilterSQL',
        'getBasketFilter'
    );
}


public function getBasketFilter(Enlight_Event_EventArgs $arguments)
{     
    
    /**@var $articleClass sArticles*/
    $articleClass = $arguments->getSubject();
 
    $categoryId = $arguments->getId();
 
    $sql = $arguments->getReturn();
 

    $sql = "
		SELECT 
                s_order_basket.*, 
                a.packunit, 
                minpurchase,
                taxID,
                IF (ad.instock,ad.instock,av.instock) AS `instock`,
                suppliernumber,
                maxpurchase,
                purchasesteps,
                purchaseunit,
                unitID,laststock,
                shippingtime,
                releasedate, 
                releasedate AS sReleaseDate,
                stockmin,esd, 
                su.description AS itemUnit, 
                ob_attr1,ob_attr2,ob_attr3,ob_attr4,ob_attr5,ob_attr6, attr1,attr2,attr3,attr4,attr5,attr6,attr7,attr8,attr9,attr10,attr11,attr12,attr13,attr14,attr15,attr16,attr17,attr18,attr19,attr20 
                
                FROM 
                s_order_basket	LEFT JOIN s_articles_details AS ad ON ad.ordernumber = s_order_basket.ordernumber
                LEFT JOIN s_articles_attributes AS at ON at.articledetailsID = ad.id
		LEFT JOIN s_articles_groups_value AS av ON av.ordernumber = s_order_basket.ordernumber
		LEFT JOIN s_articles a ON (a.id = ad.articleID OR a.id = av.articleID)
		LEFT JOIN s_core_units su ON su.id = a.unitID
		WHERE sessionID=?
		ORDER BY id ASC, datum DESC";
    
                
    $sql = Enlight()->Events()->filter(
    'Shopware_Modules_Basket_GetBasket_FilterSQL', 
    $sql, 
    array(
        'subject' => $this, 
        'id' => $categoryId
    )
);
        return true;
}


}
