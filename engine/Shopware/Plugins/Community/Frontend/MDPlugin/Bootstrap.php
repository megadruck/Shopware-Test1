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
    
    
    /**
     * @return \Shopware\Components\Model\ModelManager
     */
    protected function getEntityManager()
    {
        return Shopware()->Models();
    }
    
    
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
        'author'=>'Megadruck.de'
    );
    }
 
    public function install()
    {
        $this->registerEvents();
        return array('success' => true, 'invalidateCache' => array('frontend','proxy'));
             
        //    config (Einstellungen, Templates und Textbausteine)
        //    frontend (HttpProxy + Query-Cache - Artikel, Kategorien)
        //    backend (Backend-Cache)
        //    router (SEO-URL-Cache)
        //    search (Intelligente Suche Index/Keywords)
        //    proxy (Proxy/Model-Cache Nur für Entwicklungszwecke)

    }
    
    
    
    private function registerEvents()
    {

       
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_Order',
            'BackendOrderPostDispatch'
        );
    }


	public function BackendOrderPostDispatch(Enlight_Event_EventArgs $args)
	{
		/** @var \Enlight_Controller_Action $controller */
		$controller = $args->getSubject();
		$view = $controller->View();
		$request = $controller->Request();

		$view->addTemplateDir(__DIR__ . '/Views');

		if ($request->getActionName() === 'load') {
			$view->extendsTemplate('backend/order/view/fadeout.js');
		}

		

	}

}
