//{block name="backend/order/view/detail/window" append}

Ext.define('Shopware.apps.Order.view.detail.Window-Customizing', {

    /**
     * Defines an override applied to a class.
     * @string
     */
    override: 'Shopware.apps.Order.view.detail.Window',

    createTabPanel: function () {
        var me = this,
            tabPanel = me.callParent(arguments);
        me.store = Ext.create('Shopware.apps.Order.store.CustomizingDetails');
        me.store.getProxy().extraParams = { orderId: me.record.get('id') };
        me.store.load({
            callback: function (data, operation) {
                // If order details don't carry the "revison = 2" marker to indicate the plugin version (>= 1.1.9) ,
                // loading of the store is unsuccesful. In that case we disable the customizing-order-tab.
                tabPanel.add({
                    xtype: 'customizing-order-tab',
                    record: me.record,
                    store: me.store,
                    disabled: !operation.success
                });
            }
        });
        return tabPanel;
    }
});


//{/block}