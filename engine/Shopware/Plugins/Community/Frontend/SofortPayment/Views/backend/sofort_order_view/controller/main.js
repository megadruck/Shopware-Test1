Ext.define( 'Shopware.apps.SofortOrderView.controller.Main', {
    extend:     'Ext.app.Controller',
    mainWindow: null,
    init:       function ()
    {
        var me = this;
        var listStore = Ext.create('Shopware.apps.Order.store.ListBatch');
        listStore.load({
            callback:function (records) {
                var record = records[0];
                var stores = me.getAssociationStores(record);
                me.mainWindow = me.getView('main.Window').create({
                    orderStatusStore: stores['orderStatusStore'],
                    paymentStatusStore: stores['paymentStatusStore'],
                    taxStore: Ext.create('Shopware.apps.Order.store.Tax').load(),
                    statusStore: stores['statusStore'],
                    listStore: Ext.create('Shopware.apps.SofortOrderView.store.Order').load()
                });
            }
        });
        me.callParent( arguments );
    },
    getAssociationStores: function(record) {
        var orderStatusStore = Ext.create('Shopware.apps.Base.store.OrderStatus');
        var paymentStatusStore = Ext.create('Shopware.apps.Base.store.PaymentStatus');
        var statusStore = Ext.create('Shopware.apps.Base.store.PositionStatus');

        orderStatusStore.add(record.raw.orderStatus);
        paymentStatusStore.add(record.raw.paymentStatus);
        statusStore.add(record.raw.positionStatus);

        var stores = [];
        stores['orderStatusStore'] = orderStatusStore;
        stores['statusStore'] = statusStore;
        stores['paymentStatusStore'] = paymentStatusStore;

        return stores;
    }
} );