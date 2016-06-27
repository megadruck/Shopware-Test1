//{namespace name=backend/customizing/view/charge}

//{block name="backend/customizing/view/charge/form"}
Ext.define('Shopware.apps.Customizing.view.charge.Form', {

    extend: 'Ext.form.Panel',
    alias: 'widget.customizing-charge-form',

    cls: 'shopware-form',
    layout: 'anchor',
    autoScroll: true,
    bodyPadding: 10,
    disabled: true,

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
     *
     *
     * @return Array
     */
    getItems: function() {
        var me = this;
        return [{
            fieldLabel: '{s name=form/fields/name}Name{/s}',
            name: 'name',
            translatable: true
        }, {
            fieldLabel: '{s name=form/fields/number}Number{/s}',
            name: 'number',
            maxLength: 30,
            maxLengthText: '{s name=form/fields/number_error}Max. 30 characters allowed{/s}'
        }, {
            xtype: 'base-element-boolean',
            fieldLabel: '{s name=form/fields/percentage}Percentage{/s}',
            name: 'percentage'
        }, {
            xtype: 'customizing-charge-value'
        }];
    },

    /**
     * Close Button
     *
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
