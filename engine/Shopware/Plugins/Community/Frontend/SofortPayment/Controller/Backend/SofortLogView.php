<?php

    /**
     * Shopware 4.0
     * Copyright © 2012 shopware AG
     *
     * According to our dual licensing model, this program can be used either
     * under the terms of the GNU LESSER GENERAL PUBLIC LICENSE, version 3,
     * or under a proprietary license.
     *
     * The texts of the GNU LESSER GENERAL PUBLIC LICENSE with an additional
     * permission and of our proprietary license can be found at and
     * in the LICENSE file you have received along with this program.
     *
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU Affero General Public License for more details.
     *
     * "Shopware" is a registered trademark of shopware AG.
     * The licensing of the program under the AGPLv3 does not imply a
     * trademark license. Therefore any rights, title and interest in
     * our trademarks remain entirely with us.
     *
     * @category   Shopware
     * @package    Shopware_Plugins
     * @subpackage Sofort
     * @author     PayIntelligent
     */
    /**
     * Controller for the Backend view
     */
    class Shopware_Controllers_Backend_SofortLogView
        extends Shopware_Controllers_Backend_ExtJs
    {
        /**
         * index action is called if no other action is triggered
         */
        public function indexAction()
        {
            $this->View()->loadTemplate( "backend/sofort_log_view/app.js" );
            $this->View()->assign( "title", "Sofort AG Log" );
        }

        /**
         * This Action loads the loggingdata from the datebase into the backendview
         */
        public function loadStoreAction()
        {
            $start  = intval( $this->Request()->getParam( "start" ) );
            $limit  = intval( $this->Request()->getParam( "limit" ) );
            $helper = new Shopware_Plugins_Frontend_SofortPayment_Components_Helpers_Helper();
            $store  = $helper->database()->getLog( $start, $limit );
            $total  = $helper->database()->getLogCount();
            $this->View()->assign(
                array(
                     "data"    => $store,
                     "total"   => $total,
                     "success" => true
                )
            );
        }
    }
