 <?php

 class Shopware_Plugins_Frontend_StcomCopyrightPremium_Bootstrap extends Shopware_Components_Plugin_Bootstrap
 {
	public function getVersion()
    {
        return '1.0.1';
    }

    public function getLabel()
    {
        return 'Copyright Premium';
    }

    public function install()
    {
        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatchSecure_Frontend',
            'onFrontendPostDispatch'
        );

        $this->createConfig();

        return true;
    }
	/**
     * Embedds the css file + blocks
     *
     * @public
	 * @param Enlight_Event_EventArgs $arguments
     * @return void
     */	
	public function onFrontendPostDispatch(Enlight_Event_EventArgs $args){
		/**@var $controller Shopware_Controllers_Frontend_Index*/
        $controller = $args->getSubject();
        $view = $controller->View();
		
		$view->addTemplateDir($this->Path() . 'Views/');
 
        $view->extendsTemplate('frontend/plugins/copyright/footer.tpl');
		$view->extendsTemplate('frontend/plugins/copyright/footer_minimal.tpl');
		
		$config = $this->config();
		$text = $config->get('text');
		$title = $config->get('title');
		$logo = $config->get('logo');
		$link = $config->get('shoplink');
		$alt = $config->get('alt');
		$view->assign('stcopyright',$config);
		$view->assign('text', $text);
		$view->assign('title', $title);
		$view->assign('logo', $logo);
		$view->assign('alt', $alt);
		$view->assign('link', $link);
	}
	private function createConfig() {
		$form = $this->Form();
		$form->setElement('mediaselection', 'logo', 
	        array(
	            'label' => 'Logo', 
	            'value' => NULL
	        )
	    );
		$form->setElement('text', 'text', 
	        array(
	            'label' => 'Copyright-Text', 
	            'value' => 'Copyright &copy; Ihr Online-Shop - Alle Rechte vorbehalten',
	            'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
	        )
	    );
		$form->setElement('text', 'title', 
	        array(
	            'label' => 'Image-Title', 
	            'value' => 'Ihr Logo Titel',
	            'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
	        )
	    );
		$form->setElement('text', 'alt', 
	        array(
	            'label' => 'Alternativ Text Logo', 
	            'value' => 'Ihr Online-Shop.de',
	            'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
	        )
	    );
	    $form->setElement('text', 'shoplink', 
	        array(
	            'label' => 'Shop-Link', 
	            'value' => 'http://www.shop-templates.com',
	            'scope' => Shopware\Models\Config\Element::SCOPE_SHOP
	        )
	    );
	}
	
	/**
     * Gets the plugin's info 
     *
     * @public
     * @return array
     */
    public function getInfo()
	{        
	    return array(
         'autor' => 'shop-templates.com',
         'copyright' => '© 2015, shop-templates.com',
         'label' => $this->getLabel(),
         'source' => "Local",
         'description' => ' <img src="http://images.shop-templates.com/shop-templateslogo.png" alt="shop-templates.com" style="float: right; margin: 0px 0px 10px 15px;">
                           <h1>Copyright Premium</h1>
                           <p><table>
                           <tr><td style="width: 55px;"><strong>E-Mail: </strong></td><td> <a href="mailto:support@shop-templates.com" target="_blank">support@shop-templates.com</a></td></tr>
                           <tr><td><strong>Website: </strong></td><td> <a href="http://www.shop-templates.com" target="_blank"> www.shop-templates.com</a></td></tr>
                           </table></p><br>
							<p><b>shop-templates.com</b> ist das Portal für hochwertige Designer Templates und Plugins. Ganz einfach lässt sich auf diese Art und Weise Ihr Shopware Shop auf Ihre Wünsche erweitern.<br />
							<br />
							Jedes unserer Templates & Plugins kann nach Ihren Wünschen individualisiert werden.<br />
							<br />
							Bei Fragen zur Individualisierung oder Erweiterung schreiben Sie uns einfach eine Mail an:<br />
							<a href=”mailto:individual@shop-templates.com”>individual@shop-templates.com</a><br />
							<br />
							Haben Sie Fragen zu unseren Produkten, dann besuchen Sie unsere <a href=”http://www.shop-templates.com/faq-soforthilfe”>FAQ & Soforthilfe</a> Seite. Dort finden Sie Antworten auf die häufigsten Fragen zu unseren Produkten. </p> 
							',
         'license' => 'shop-templates.com',
         'support' => 'http://www.shop-templates.com',
         'link' => 'http://www.shop-templates.com'
	    );        
	}
	/**
     * Enable function. Fired when the plugin is enabled 
     *
     * @public
     * @return boolean
     */
	public function enable(){

		return true;
		
	}
	/**
     * Uninstall function. Restores all changed storefront sizes
     *
     * @public
     * @return boolean
     */
	public function uninstall() {

		return true;
	} 
 }