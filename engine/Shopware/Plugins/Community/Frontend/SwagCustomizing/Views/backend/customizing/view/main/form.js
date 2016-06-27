//{namespace name=backend/customizing/view/main}

//{block name="backend/customizing/view/main/form"}
Ext.define('Shopware.apps.Customizing.view.main.Form', {

    extend: 'Ext.form.Panel',
    alias: 'widget.customizing-form',

    cls: 'shopware-form',
    layout: 'anchor',
    autoScroll: true,
    bodyPadding: 10,

    defaults: {
        xtype: 'textfield',
        anchor: '100%',
        labelWidth: 155,
        labelStyle: 'font-weight: bold'
    },

    /**
     *
     */
    initComponent:function () {
        var me = this;

        Ext.applyIf(me, {
            items: me.getItems(),
            buttons: me.getButtons()
        });

        me.callParent(arguments);
    },

    /**
     * @return Array
     */
    getItems: function() {
        var me = this;
        return [];
    },

    /**
     * @return Array
     */
    getButtons: function() {
        var me = this;
        return [{
            text: '{s name=form/buttons/save}Save{/s}',
            action: 'save',
            cls: 'primary'
        }];
    }
});
//{/block}
