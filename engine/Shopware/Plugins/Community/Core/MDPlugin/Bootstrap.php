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


class Shopware_Plugins_Core_MDPlugin_Bootstrap 
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
        return '0.0.1a';
    }
 
    public function getInfo()
    {
        return array(
        'version'       => $this->getVersion(),
        'label'         => $this->getLabel(),
        'supplier'      => 'Megadruck.de',
        'description'   => 'Core Anpassungen Megadruck',
        'support'       => 'Megadruck.de',
        'author'        => 'Megadruck.de',
        'link'          => 'http://www.megadruck.de'
    );
    }
 
    public function install()
    {
        $this->registerEvents();
        return array('success' => true, 'invalidateCache' => array('proxy'));
    }
    
    
    
    private function registerEvents()
            
    {
        
        $this->subscribeEvent(
		'Shopware_Modules_Basket_UpdateArticle_FilterSqlDefault',
		'UpdateArticleFilterSqlDefault'
	);
        
        $this->subscribeEvent(
            'Shopware_Modules_Basket_GetBasket_FilterSQL',
            'getBasketFilter'
        );
    }


public function UpdateArticleFilterSqlDefault(Enlight_Event_EventArgs $arguments)
{
    
    
}
   
    public function getBasketFilter(Enlight_Event_EventArgs $arguments)
    {     

//       $logger = Shopware()->Container()->get('debuglogger');
//        Shopware()->Debuglogger()->info($result = \Doctrine\Common\Util\Debug::dump($arguments));          // Object dumping

//        $sql = $arguments->getReturn();
        
         

        $sql ="SELECT
            s_order_basket.*,
            COALESCE (NULLIF(ad.packunit, ''), mad.packunit) AS packunit,
            a.main_detail_id AS mainDetailId,
            ad.id AS articleDetailId,
            ad.minpurchase,
            a.taxID,
            ad.instock AS instock,
            ad.suppliernumber,
            ad.maxpurchase,
            ad.purchasesteps,
            ad.purchaseunit,
            COALESCE (ad.unitID, mad.unitID) AS unitID,
            a.laststock,
            ad.shippingtime,
            ad.releasedate,
            ad.releasedate AS sReleaseDate,
            COALESCE (ad.ean, mad.ean) AS ean,
            ad.stockmin,
            s_order_basket_attributes.attribute1 as ob_attr1,
            s_order_basket_attributes.attribute2 as ob_attr2,
            s_order_basket_attributes.attribute3 as ob_attr3,
            s_order_basket_attributes.attribute4 as ob_attr4,
            s_order_basket_attributes.attribute5 as ob_attr5,
            s_order_basket_attributes.attribute6 as ob_attr6
        FROM s_order_basket
        LEFT JOIN s_articles_details AS ad ON ad.ordernumber = s_order_basket.ordernumber
        LEFT JOIN s_articles a ON (a.id = ad.articleID)
        LEFT JOIN s_articles_details AS mad ON mad.id = a.main_detail_id
        LEFT JOIN s_order_basket_attributes ON s_order_basket.id = s_order_basket_attributes.basketID
        WHERE sessionID=?
        ORDER BY id ASC, datum DESC";

         
        Shopware()->Debuglogger()->info($sql);
        
        return $sql;
    }


}