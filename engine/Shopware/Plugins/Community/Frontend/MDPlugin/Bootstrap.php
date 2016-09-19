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




class Shopware_Plugins_Frontend_MDPlugin_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{

    public function install()
    {
        $this->registerController('Frontend', 'Checkoutrequest');
        $this->AddAttributes();
        $this->RegisterEvents();
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function getLabel()
    {
        return 'Megadruck Anpassungen';
    }

    public function getVersion()
    {
        return '1.5.0';
    }

    public function getInfo() {
        return array(
            // Die Plugin-Version.
            'version' => $this->getVersion(),
            // Copyright-Hinweis
            'copyright' => 'Copyright (c) 2016, Megadruck.de',
            // Hersteller-Seite
            'supplier' => 'Megadruck.de',
            // Hersteller-Seite
            'author' => 'Megadruck.de',
            // Lesbarer Name des Plugins
            'label' => $this->getLabel()
        );
    }
    public function getCapabilities()
    {
        return array(
            'install' => true,
            'update' => true,
            'enable' => true);
    }

    public function afterInit()
    {
        return true;
    }

    /**
     * @return array
     */
    public function RegisterEvents()
    {
        $this->subscribeEvent(
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_Checkoutrequest',
            'onGetControllerPathFrontend'
        );

        $this->subscribeEvent(
            'Enlight_Controller_Action_PostDispatch_Backend_Order',
            'BackendOrderPostDispatch'
        );

        $this->subscribeEvent(
            'Shopware_Modules_Basket_GetBasket_FilterItemStart',
            'GetBasketAttribute'
        );

        $this->subscribeEvent(
            'hopware_Modules_Order_SaveOrder_ProcessDetails',
            'SaveBasketAttribute'
        );

         return array('success' => true, 'invalidateCache' => array('frontend', 'backend'));

        //    config (Einstellungen, Templates und Textbausteine)
        //    frontend (HttpProxy + Query-Cache - Artikel, Kategorien)
        //    backend (Backend-Cache)
        //    router (SEO-URL-Cache)
        //    search (Intelligente Suche Index/Keywords)
        //    proxy (Proxy/Model-Cache Nur für Entwicklungszwecke)
    }

    public function onGetControllerPathFrontend(Enlight_Event_EventArgs $args)
    {
//        Shopware()->PluginLogger()->info("test");
        mail('j.frintrop@megadruck.de', 'Debug_FORM', 'TEST','FROM:edv@frintrop.com');
        return $this->Path() . 'Controllers/Frontend/Checkoutrequest.php';
    }

    /**
     * @param Enlight_Event_EventArgs $arg
     */
    public function BackendOrderPostDispatch(Enlight_Event_EventArgs $args)
	{
        $controller = $args->getSubject();
		$view = $controller->View();
		$request = $controller->Request();

		$view->addTemplateDir(__DIR__ . '/Views');

		if ($request->getActionName() === 'load')
		{
			$view->extendsTemplate('backend/order/view/detail/fadeout.js');
            $view->extendsTemplate('backend/order/view/detail/mdcommunication.js');
//          $view->extendsTemplate('backend/order/view/detail/mdposition.js');
		}

	}

	public function AddAttributes()
    {
           try {
               $this->get('models')->addAttribute(
                's_order_details_attributes',
                'md',
                'variation',
                'VARCHAR(255)');

//               $this->get('models')->addAttribute(
//                   's_order_basket_attributes',
//                   'md',
//                   'variation',
//                   'VARCHAR(255)');


               $this->get('models')->addAttribute(
                   's_order_attributes',
                   'md',
                   'reference',
                   'VARCHAR(255)');
            }
            catch (Exception $e)
            {
            }

            $this->get('models')->generateAttributeModels(array('s_order_details_attributes'));
//          $this->get('models')->generateAttributeModels(array('s_order_basket_attributes'));
            $this->get('models')->generateAttributeModels(array('s_order_attributes'));
    }


    /**
     * @param Enlight_Event_EventArgs $arguments
     * @return mixed|void
     * reading variation of basket products
     */
    public function GetBasketAttribute(Enlight_Event_EventArgs $arguments)
    {
        $s = $arguments->getReturn();
        if ($s['articleDetailId'] == '')
        {
                // articleDetailId ist leer
                return;
        }

        try {
           $db = Shopware()->Db();
        } catch (Exception $e) { }


        $sql = "SELECT
                  s_article_configurator_groups.name as name,
                  s_article_configurator_options.name as value
                FROM
                  s_article_configurator_option_relations,
                  s_article_configurator_options,
                  s_article_configurator_groups
                WHERE
                  option_id =s_article_configurator_options.id 
                AND s_article_configurator_groups.id = s_article_configurator_options.group_id 
                AND s_article_configurator_option_relations.article_id = ? ";

        $variation = $db->fetchAll($sql, array($s['articleDetailId']));


        $s['variation'] = $variation;
        return $s;
    }

    /**
     * @param Enlight_Event_EventArgs $arguments
     * @return mixed
     * saving product variations to db (s_order_details_attributes)
     */
    public function SaveBasketAttribute(Enlight_Event_EventArgs $arguments)
    {

        $BasketArticle = $arguments->getDetails();
        foreach ($BasketArticle  as $key => $value)
        {
            if($BasketArticle[$key]['variation'] != '') {
                $BasketArticle[$key]['attributes']['md_variation'] = json_encode(
                    $BasketArticle[$key]['variation'],
                    true
                );

                $sql = "UPDATE 
                    s_order_details_attributes
                 SET 
                    md_variation = '".json_encode($BasketArticle[$key]['variation'], true)."' 
                 WHERE 
                    detailID=?";

                $db =  Shopware()->Db();
                $db->query($sql, array($BasketArticle[$key]['orderDetailId']));
            }
        }

        return $BasketArticle;
    }



}
