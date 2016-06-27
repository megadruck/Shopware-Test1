//{namespace name=backend/customizing/view/charge}

//{block name="backend/customizing/view/charge/panel"}
Ext.define('Shopware.apps.Customizing.view.charge.Panel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.customizing-charge',
    layout:'border',
    title: '{s name=panel/title}Charges{/s}',

    /**
     *
     */
    initComponent: function() {
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
    getItems: function() {
        var me = this;
        return [{
            xtype: 'customizing-charge-list',
            region: 'west',
            width: 350
        }, {
            xtype: 'customizing-charge-form',
            region: 'center'
        }];
    }
});
//{/block}
