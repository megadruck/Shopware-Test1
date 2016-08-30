<?php


namespace MDPlugin;

use Shopware\Components\Plugin;
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

class MDPlugin extends Plugin
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
    
     public static function getSubscribedEvents()
     {
         return [
            'Enlight_Controller_Action_PostDispatch_Backend_Order' => 'BackendOrderPostDispatch'
        ];

             
        //    config (Einstellungen, Templates und Textbausteine)
        //    frontend (HttpProxy + Query-Cache - Artikel, Kategorien)
        //    backend (Backend-Cache)
        //    router (SEO-URL-Cache)
        //    search (Intelligente Suche Index/Keywords)
        //    proxy (Proxy/Model-Cache Nur für Entwicklungszwecke)

    }
    
    


	public function BackendOrderPostDispatch(Enlight_Event_EventArgs $args)
	{
		/** @var \Enlight_Controller_Action $controller */
		$controller = $args->getSubject();
		$view = $controller->View();
		$request = $controller->Request();

		$view->addTemplateDir(__DIR__ . '/Views');

		if ($request->getActionName() === 'load') {
			$view->extendsTemplate('backend/order/view/detail/fadeout.js');
            $view->extendsTemplate('backend/order/view/detail/mdcommunication.js');
		}

		

	}

}
