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

ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require __DIR__ . "/../../../../../../../tests/Functional/bootstrap.php";

/**
 * Register event subscribers, namespaces, etc
 */
/** @var Shopware_Plugins_Frontend_SwagCustomProducts_Bootstrap $customProductsBootstrap */
$customProductsBootstrap = Shopware()->Plugins()->Frontend()->SwagCustomProducts();
$customProductsBootstrap->onStartDispatch();

$helper = Shopware();
$loader = $helper->Loader();
