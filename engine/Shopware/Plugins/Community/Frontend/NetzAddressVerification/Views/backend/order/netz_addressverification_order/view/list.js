//{block name="backend/order/view/list/list" append}

Ext.define('Shopware.apps.Order.NetzAddressverification.view.List', {
    override: 'Shopware.apps.Order.view.list.List',
    getColumns: function () {
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
                flex: 2,
                renderer: me.customerColumn
            },
            {
                header: 'Valide',
                //dataIndex: 'customerId',
                flex: 1,
                renderer: me.validColumn
            },
            {
                header: me.snippets.columns.orderStatus,
                dataIndex: 'status',
                flex: 2,
                renderer: me.orderStatusColumn,
                editor: {
                    xtype: 'combobox',
                    queryMode: 'local',
                    allowBlank: false,
                    valueField: 'id',
                    displayField: 'description',
                    store: me.orderStatusStore,
                    editable: false

                }
            },
            {
                header: me.snippets.columns.paymentStatus,
                dataIndex: 'cleared',
                flex: 2,
                renderer: me.paymentStatusColumn,
                editor: {
                    xtype: 'combobox',
                    queryMode: 'local',
                    allowBlank: false,
                    valueField: 'id',
                    displayField: 'description',
                    store: me.paymentStatusStore,
                    editable: false
                }
            },
            me.createActionColumn()
        ];

        return columns;
    },
    validColumn: function (value, metaData, record, colIndex, store, view) {
        var me = this,
            valid = '',
            attributes = record.getAttributes();
        if (attributes.data.items[0] && attributes.data.items[0].data.netzValid == 1) {
            return true;
        }
        return false;
    }

});
//{/block}

