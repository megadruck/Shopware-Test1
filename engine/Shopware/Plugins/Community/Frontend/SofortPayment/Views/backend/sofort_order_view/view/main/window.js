//{namespace name=backend/order/main}
Ext.require( [
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*'
] );
Ext.define( 'Shopware.apps.SofortOrderView.view.main.Window', {
    extend:          'Enlight.app.Window',
    title:           'Sofort AG Orderoverview',
    alias:           'widget.sofort_order_view-main-window',
    border:          false,
    autoShow:        true,
    resizable:       true,
    layout:          {
        type: 'fit'
    },
    height:          520,
    width:           800,
    initComponent:   function ()
    {
        var me = this;
        me.items = [
            {
                xtype: 'container',
                layout: 'fit',
                items: [
                    Ext.create('Shopware.apps.SofortOrderView.view.list.List', {
                        listStore: me.listStore,
                        orderStatusStore: me.orderStatusStore,
                        paymentStatusStore: me.paymentStatusStore,
                        statusStore: me.statusStore,
                        taxStore: me.taxStore
                    })
                ]
            }
        ];

        me.callParent( arguments );
    }

} );