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
 * @package    SwagLiveShopping
 * @subpackage ExtJs
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

//{block name="backend/customer/view/detail/window" append}
//{namespace name="backend/customer/view/main"}
Ext.define('Shopware.apps.Customer.view.detail.TicketSystemWindow', {

    override: 'Shopware.apps.Customer.view.detail.Window',

    /**
     * @Override
     * Creates the tab panel which displays the tickets of a customer.
     *
     * @return Ext.tab.Panel
     */
    getTabs: function() {
        var me = this, result;
        result = me.callParent(arguments);
        if ( me.record.get('id') ) {
            result.push(me.createTicketsTab());
        }
        me.subApplication.getController('TicketSystem');
        return result;
    },

    createTicketsTab:function () {

        var me = this,
            ticketsGridStore = Ext.create('Shopware.apps.Customer.store.ticket_system.List'),
            ticketEmployeeStore = Ext.create('Shopware.apps.Customer.store.ticket_system.Employee');
            ticketsGridStore.getProxy().extraParams = { customerID:me.record.data.id };

        me.ticketsGrid = Ext.create('Shopware.apps.Customer.view.ticket_system.List', {
            flex: 1,
            gridStore: ticketsGridStore.load(),
            employeeStore:ticketEmployeeStore.load()
        });

        return Ext.create('Ext.container.Container', {
            layout: {
                type: 'vbox',
                align : 'stretch'
            },
            defaults: { flex: 1 },
            title: '{s name=window/tab_customer_tickets_tab_title}Tickets{/s}',
            items: [ me.ticketsGrid ]
        });
    }

});
//{/block}