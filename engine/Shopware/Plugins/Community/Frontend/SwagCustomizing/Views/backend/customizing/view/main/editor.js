//{namespace name=backend/customizing/view/main}

//{block name="backend/customizing/view/main/editor"}
Ext.define('Shopware.apps.Customizing.view.main.Editor', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.customizing-editor',
    layout: 'border',

    title: '{s name=editor/title}Groups / Options{/s}',

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
            xtype: 'customizing-list',
            region: 'west',
            width: 460
        }, {
            xtype: 'customizing-panel',
            region: 'center'
        }];
    }
});
//{/block}
