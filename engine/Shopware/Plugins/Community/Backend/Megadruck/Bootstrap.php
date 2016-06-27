<?php

/* 
 * The MIT License
 *
 * Copyright 2016 MegaDruck.de Produktions- und Vertriebs GmbH
 * Joerg Frintrop - j.frintrop@megadruck.de
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

class Shopware_Plugins_Backend_Megadruck_Bootstrap
extends Shopware_Components_Plugin_Bootstrap
{
    public function getCapabilities() {
        return array(
        'install' => true,
        'update' => false,
        'enable' => true
        );
    }
    
    public function getLabel() {
        return 'Megadruck Erweiterung';
    }
    
    public function getInfo() {
        return array(
        'version'       => $this->getVersion(),
        'label'         => $this->getLabel(),
        'author'        => 'Megadruck.de',
        'description'   => 'Anpassungen für das Shopsystem von Megadruck.de',
        'support'       => 'Joerg Frintrop',
        'link'          => 'http://www.megadruck.de',
        'copyright'     => 'Copyright (c) 2016, MegaDruck.de Produktions- und Vertriebs GmbH',
        );
    }
    
    public function install() {
        $this->subscribeEvents();
        $this->createDatabaseTables();
        
        return true;
    }
    
    public function getVersion(){
        return '0.0.1';
    }
    
    
    
    private function subscribeEvents()
    {
        $this->subscribeEvent('Enlight_Controller_Dispatcher_ControllerPath_Frontend_Shopware_Plugins_Backend_Megadruck',
                'onGetFrontendController');
    }

    
    public function onGetFrontendController()
    {
        $this->registerCustomModels();
 
        $this->createDatabaseTables();
        $this->Application()->Snippets()->addConfigDir(
            $this->Path() . 'Snippets/'
        );
 
        $this->Application()->Template()->addTemplateDir(
            $this->Path() . 'Views/'
        );
 
        return $this->Path(). 'Controllers/Frontend/SwagAddressAdministration.php';
    }
}