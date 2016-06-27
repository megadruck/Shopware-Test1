//{namespace name=backend/order/customizing/main}

//{block name="backend/order/view/customizing/tab"}
Ext.define('Shopware.apps.Order.view.customizing.Tab', {
    extend: 'Ext.container.Container',
    alias: 'widget.customizing-order-tab',
    layout: 'border',
    autoScroll: false,
    title: '{s namespace="backend/customizing/view/main" name="window/title"}Custom Products{/s}',
    /**
     * initComponent
     */
    initComponent: function () {
        var me = this;

        Ext.applyIf(me, {
            items: me.getItems()
        });
        me.callParent(arguments);
    },
    /**
     * Creates the fields sets and the sidebar for the detail page.
     * @return Array
     */
    getItems: function () {
        var me = this;

        me.detail = Ext.create('Shopware.apps.Customizing.view.customizing.Detail', {
            region: 'center',
            store: me.store,
            tab: me,
            listeners: {
                'afterrender': function () {
                    me.doLayout();
                }
            }
        });
        me.grid = Ext.create('Shopware.apps.Customizing.view.customizing.Grid', {
            region: 'west',
            width: '60%',
            store: me.store,
            tab: me,
            detail: me.detail
        });

        return [me.grid, me.detail];
    }



});
//{/block}
