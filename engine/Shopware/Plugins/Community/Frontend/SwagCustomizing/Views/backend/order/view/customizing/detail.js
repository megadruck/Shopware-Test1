//{namespace name=backend/order/customizing/main}

//{block name="backend/order/view/customizing/detail"}
Ext.define('Shopware.apps.Customizing.view.customizing.Detail', {

    extend: 'Ext.container.Container',
    alias: 'widget.customizing-detail-panel',
    title: '{s namespace="backend/customizing/view/main" name="window/detail/title"}{/s}',
    cls: Ext.baseCSSPrefix + 'order-list-navigation',
    bodyPadding: 10,
    autoScroll: true,
    autoscroll: false,
    /**
     * initComponent
     */
    initComponent: function () {
        var me = this;
        me.items = Ext.create('Shopware.apps.Customizing.view.customizing.Form', {
            store: me.store
        });


        me.callParent(arguments);
    }

});

//{/block}