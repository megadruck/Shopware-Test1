<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace ShopwarePlugins\SwagCustomProducts\Subscriber;

use Doctrine\DBAL\Connection;
use Enlight_Controller_ActionEventArgs as ActionEventArgs;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\DependencyInjection\Container as DIContainer;

class Backend implements SubscriberInterface
{
    /** @var DIContainer $container */
    private $container;

    /** @var string $bootstrapPath */
    private $bootstrapPath;

    /**
     * @param DIContainer $container
     * @param string $bootstrapPath
     */
    public function __construct(DIContainer $container, $bootstrapPath)
    {
        $this->container = $container;
        $this->bootstrapPath = $bootstrapPath;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Backend_Article' => 'extendArticleModule',
            'Enlight_Controller_Action_PostDispatch_Backend_Order' => [ 'extendOrderModule', 100 ],
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Index' => 'extendMenu'
        ];
    }

    /**
     * @param ActionEventArgs $args
     */
    public function extendOrderModule(ActionEventArgs $args)
    {
        $this->registerTemplateDir();

        /** @var \Enlight_View_Default $view */
        $view = $args->getSubject()->View();

        if ($args->getRequest()->getActionName() === 'getList') {
            $data = $this->assignAttributes($view->getAssign('data'));
            $view->assign('data', $data);
        }

        if ($args->getRequest()->getActionName() === 'index') {
            $view->extendsTemplate('backend/order/swag_custom_products/app.js');
        }

        if ($args->getRequest()->getActionName() === 'load') {
            $view->extendsTemplate('backend/order/swag_custom_products_position.js');
        }
    }

    /**
     * Event handler which provides the necessary snippet and views directories for the plugin.
     *
     * @param ActionEventArgs $args
     */
    public function extendArticleModule(ActionEventArgs $args)
    {
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $actionName = $args->getRequest()->getActionName();
        $view = $controller->View();

        // Add template directory
        $this->registerTemplateDir();

        if ($actionName === 'index') {
            $view->extendsTemplate('backend/article/swag_custom_products/app.js');
        }

        if ($actionName === 'load') {
            $view->extendsTemplate('backend/article/swag_custom_products/views/window.js');
        }
    }

    /**
     * Loads the menu icon
     *
     * @param ActionEventArgs $args
     */
    public function extendMenu(ActionEventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Index $subject */
        $view = $args->getSubject()->View();

        $this->registerTemplateDir();
        $view->extendsTemplate('backend/swag_custom_products/menu_item.tpl');
    }

    /**
     * registers the Views/ directory as template directory
     */
    private function registerTemplateDir()
    {
        /** @var \Enlight_Template_Manager $template */
        $template = $this->container->get('template');
        $template->addTemplateDir($this->bootstrapPath . 'Views/');
    }

    /**
     * @param array[] $data
     * @return array[]
     */
    private function assignAttributes($data)
    {
        $ids = [];
        foreach ($data as $order) {
            $ids = array_merge(array_column($order['details'], 'id'), $ids);
        }

        $attributes = $this->loadAttributes($ids);

        foreach ($data as &$order) {
            foreach ($order['details'] as &$detail) {
                $id = $detail['id'];
                if (!isset($attributes[$id])) {
                    continue;
                }
                $attribute = array_shift($attributes[$id]);
                $detail = array_merge($detail, $attribute);
            }
        }
        return $data;
    }

    /**
     * @param int[] $ids
     * @return array[]
     */
    private function loadAttributes($ids)
    {
        $query = $this->container->get('dbal_connection')->createQueryBuilder();
        $query->select([
            'detailID',
            'swag_custom_products_configuration_hash',
            'swag_custom_products_mode'
        ]);
        $query->from('s_order_details_attributes', 'attributes');
        $query->where('detailID IN (:ids)');
        $query->setParameter(':ids', $ids, Connection::PARAM_INT_ARRAY);

        return $query->execute()->fetchAll(\PDO::FETCH_GROUP);
    }
}
