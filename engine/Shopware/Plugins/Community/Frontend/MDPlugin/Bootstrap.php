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
        return array('success' => true, 'invalidateCache' => array('frontend','proxy'));;
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
    $logger = Shopware()->Container()->get('debuglogger');
$logger->addInfo($result = \Doctrine\Common\Util\Debug::dump($arguments));


    /**@var $articleClass sArticles*/
 
    //$sql = $arguments->getReturn();
  


    
    $sql ="SELECT  * FROM s_order_basket WHERE sessionID=? ORDER BY id ASC, datum DESC";

                
    
        return $sql;
}


}
