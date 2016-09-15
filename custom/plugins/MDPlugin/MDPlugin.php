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

namespace MDPlugin;

use Enlight_Event_EventArgs;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;


    class MDPlugin extends Plugin
    {


    /**
     * @param InstallContext $context
     */

    public function install(InstallContext $context)
    {
        $this->AddAttributes();
        parent::install($context);
    }

    /**
     * @return array
     */
    public function getCapabilities()
    {
        return array(
            'install' => true,
            'update' => true,
            'enable' => true);
    }


    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Backend_Order'                      => 'BackendOrderPostDispatch',
            'Shopware_Modules_Basket_GetBasket_FilterItemStart'                         => 'GetBasketAttribute',
            'Shopware_Modules_Order_SaveOrder_ProcessDetails'                           => 'SaveBasketAttribute',
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_CheckoutRequests'    => 'onGetFrontendController'
        ];

        //    config (Einstellungen, Templates und Textbausteine)
        //    frontend (HttpProxy + Query-Cache - Artikel, Kategorien)
        //    backend (Backend-Cache)
        //    router (SEO-URL-Cache)
        //    search (Intelligente Suche Index/Keywords)
        //    proxy (Proxy/Model-Cache Nur für Entwicklungszwecke)

    }

        /**
         * @param Enlight_Event_EventArgs $args
         * @return string
         */
    public function onGetFrontendController(Enlight_Event_EventArgs $args)
    {
        $this->container->get('template')->addTemplateDir($this->getPath() . '/Resources/Views');
        return $this->getPath() . '/Controllers/Frontend/CheckoutRequests.php';
    }

    /**
     * @param Enlight_Event_EventArgs $arg
     */
    public function BackendOrderPostDispatch(Enlight_Event_EventArgs $args)
	{
		/** @var \Enlight_Controller_Action $controller */
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
                $this->container->get('models')->addAttribute(
                's_order_details_attributes',
                'md',
                'variation',
                'VARCHAR(255)');


               $this->container->get('models')->addAttribute(
                   's_order_attributes',
                   'md',
                   'reference',
                   'VARCHAR(255)');
            }
            catch (Exception $e)
            {
            }

             $this->container->get('models')->generateAttributeModels(array('s_order_details_attributes'));
    }


    /**
     * @param Enlight_Event_EventArgs $arguments
     * reading variation of basket products
     */
    public function GetBasketAttribute(\Enlight_Event_EventArgs $arguments)
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
        $db =  Shopware()->Db();
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


                $db->query($sql, array($BasketArticle[$key]['orderDetailId']));
            }
        }

        return $BasketArticle;
    }



}
