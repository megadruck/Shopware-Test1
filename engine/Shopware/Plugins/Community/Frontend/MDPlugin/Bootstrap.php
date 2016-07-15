<?php

/* 
 * The MIT License
 *
 * Copyright 2016 Megadruck.de
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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
        return array('success' => true, 'invalidateCache' => array('frontend','proxy'));
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
        $logger->addInfo($result = \Doctrine\Common\Util\Debug::dump($arguments));          // Object dumping

        //$sql = $arguments->getReturn();

        $sql ="SELECT  * FROM s_order_basket WHERE sessionID=? ORDER BY id ASC, datum DESC";
        //    Demo query. result is not important
        
        return $sql;
    }


}
