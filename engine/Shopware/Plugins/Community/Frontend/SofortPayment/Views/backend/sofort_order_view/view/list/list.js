Ext.define('Shopware.apps.SofortOrderView.view.list.List', {
    /**
     * Extend from the standard ExtJS 4
     * @string
     */
    extend: 'Shopware.apps.Order.view.list.List',
    initComponent: function() {
        me = this;
        me.columns = me.getColumns();
        me.dockedItems = [me.pagingbar];
        me.callParent(arguments);
    },
    getToolbar: function() {
        return {
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                xtype: 'button',
                iconCls: 'sprite-minus-circle',
                text: 'Delete all orders',
                handler: function(){
                    Ext.MessageBox.confirm('{s name=deleteOrder/title}Are you sure?{/s}', '{s name=4003 namespace=sofort}All orders for which a SOFORT Banking transaction could not be completed will be deleted from Shopware (i.e. SOFORT could not confirm the transaction after a timeout of 4,500 seconds).{/s}', function(response) {
                        if (response === 'yes') {
                            me.ajaxClearAllOrders(response);
                        }
                    });
                }
            }]
        };
    },
    createPlugins: function() {
        return null;
    },
    /**
     * Creates the grid columns
     *
     * @return [array] grid columns
     */
    getColumns: function() {
        var me = this;
        var columns = [
            {
                header: me.snippets.columns.orderTime,
                dataIndex: 'orderTime',
                flex: 1,
                renderer: me.dateColumn
            },
            {
                header: me.snippets.columns.number,
                dataIndex: 'number',
                flex: 1
            },
            {
                header: me.snippets.columns.invoiceAmount,
                dataIndex: 'invoiceAmount',
                flex: 1,
                renderer: me.amountColumn
            },
            {
                header: me.snippets.columns.transactionId,
                dataIndex: 'transactionId',
                flex: 1
            },
            {
                header: me.snippets.columns.paymentName,
                dataIndex: 'paymentId',
                flex: 1,
                renderer: me.paymentColumn
            },
            {
                header: me.snippets.columns.dispatchName,
                dataIndex: 'dispatchId',
                flex: 1,
                renderer: me.dispatchColumn
            },
            {
                header: me.snippets.columns.shopName,
                dataIndex: 'shopId',
                flex: 1,
                renderer: me.shopColumn
            },
            {
                header: me.snippets.columns.customer,
                dataIndex: 'customerId',
                flex: 2
            },
            {
                header: me.snippets.columns.orderStatus,
                dataIndex: 'status',
                flex: 2
            },
            {
                header: me.snippets.columns.paymentStatus,
                dataIndex: 'cleared',
                flex: 2
            },
            me.createActionColumn()
        ];

        return columns;
    },
    createActionColumn: function() {
        var me = this;

        return Ext.create('Ext.grid.column.Action', {
            width: 90,
            items: [
                me.createDeleteOrderColumn()
            ]
        });
    },
    createDeleteOrderColumn: function() {
        var me = this;
        return {
            iconCls: 'sprite-minus-circle-frame',
            tooltip : '{s name=order_delete_tooltip}Delete{/s}',
            handler: function(view, rowIndex, colIndex, item) {
                var row = view.getStore().data.items[rowIndex];
                Ext.MessageBox.confirm('{s name=deleteOrder/title}Are you sure?{/s}', '{s name=deleteOrder/message}Do you want to delete these order(s)?{/s}', function(response) {
                    if (response === 'yes') {
                        if(me.ajaxRestoreStock(row.data.id, row.data.number)){
                            me.ajaxDeleteOrder(row.data.id);
                        }
                    }
                });
            }
        };
    },
    ajaxRestoreStock: function(id, number){
        var result = false;
        Ext.Ajax.request({
            url: '{url controller=SofortOrderView action=restoreArticleStock}',
            method: 'POST',
            async: false,
            params: {
                orderId: id,
                orderNumber: number
            },
            success: function(rawResponse) {
                var response = Ext.JSON.decode(rawResponse.responseText);
                result = response.success;
            }
        });
        return result;
    },
    ajaxDeleteOrder: function(id){
        var me = this;
        Ext.Ajax.request({
            url: '{url controller=CanceledOrder action=deleteOrder}',
            method: 'POST',
            async: true,
            params: {
                id: id
            },
            success: function() {
                Shopware.Notification.createGrowlMessage('{s name=delete_position/success_title}Order position has been removed{/s}', '{s name=delete_position/success_title}Order position has been removed{/s}', '{s name=growlMessage}Order{/s}');
                me.refreshView();
            },
            failure: function() {
                Shopware.Notification.createGrowlMessage('{s name=delete_position/error_title}Error{/s}', '{s name=delete_position/error_message}An error has occurred while deleting:{/s}', '{s name=growlMessage}Order{/s}');
                me.refreshView();
            }
        });
    },
    ajaxClearAllOrders: function(userResponse){
        var me = this;
        Ext.Ajax.request({
            url: '{url controller=SofortOrderView action=clearAllOrders}',
            method: 'POST',
            async: false,
            params:{
                userResponse: userResponse
            },
            success: function() {
                Shopware.Notification.createGrowlMessage('{s name=delete_position/success_title}Order position has been removed{/s}', '{s name=delete_position/success_title}Order position has been removed{/s}', '{s name=growlMessage}Order{/s}');
                me.refreshView();
            },
            failure: function() {
                Shopware.Notification.createGrowlMessage('{s name=delete_position/error_title}Error{/s}', '{s name=delete_position/error_message}An error has occurred while deleting:{/s}', '{s name=growlMessage}Order{/s}');
                me.refreshView();
            }
        });
    },
    refreshView: function(){
        var me = this;
        me.reconfigure(Ext.create('Shopware.apps.SofortOrderView.store.Order').load());
        me.getView().refresh();
    }

});