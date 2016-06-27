/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
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
 * @package    TicketSystem
 * @subpackage Employee
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */
//{namespace name=backend/ticket/main}
//{block name="backend/ticket/store/employee_combo"}
Ext.define('Shopware.apps.Ticket.store.EmployeeCombo', {

    /**
     * Extend for the standard ExtJS 4
     * @string
     */
    extend:'Ext.data.Store',
    /**
     * Define the used model for this store
     * @string
     */
    model:'Shopware.apps.Ticket.model.Employee',
    /**
     * Enable remote sort.
     * @boolean
     */
    remoteSort:true,
    /**
     * Enable remote filtering
     * @boolean
     */
    remoteFilter:true,
    /**
     * Amount of data loaded at once
     * @integer
     */
    pageSize:5,
    /**
     * to upload all selected items in one request
     * @boolean
     */
    batch:true,

    /**
     * A config object containing one or more event handlers to be added to this object during initialization
     * @object
     */
    listeners: {
        /**
         * Fires whenever records have been prefetched
         * used to add some default values to the combobox
         *
         * @event load
         * @param [object] store - Ext.data.Store
         * @return void
         */
        load: function(store) {
            store.insert(0, Ext.create('Shopware.apps.Ticket.model.Employee', {
                id: 0,
                name: '{s name=employee_store/no_assigment}No assignment{/s}'
            }));
            store.insert(0, Ext.create('Shopware.apps.Ticket.model.Employee', {
                id: -1,
                name: '{s name=employee_store/all_assigment}All{/s}'
            }));
        }
    },

    /**
     * Configure the data communication
     * @object
     */
    proxy:{
        /**
         * Set proxy type to ajax
         * @string
         */
        type:'ajax',

        api: {
            //read out all articles
            read: '{url controller="Ticket" action="getEmployeeList"}'
        },

        /**
         * Configure the data reader
         * @object
         */
        reader:{
            type:'json',
            root:'data',
            totalProperty:'total'
        }
    }
});
//{/block}