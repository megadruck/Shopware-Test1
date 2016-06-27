//{namespace name=backend/customizing/view/main}

/**
 * todo@all: Documentation
 */
//{block name="backend/customizing/view/main/window"}
Ext.define('Shopware.apps.Customizing.view.main.Window', {
    extend: 'Enlight.app.Window',
    alias: 'widget.customizing-window',
    cls: Ext.baseCSSPrefix + 'customizing',

    layout: 'fit',
    width: 1100,
    height:'90%',

    title: '{s name=window/title}Custom Products{/s}',

    titleOption: '{s name=window/title_option}Custom Products - Option: [name]{/s}',
    titleGroup: '{s name=window/title_group}Custom Products - Group: [name]{/s}',
    titleNewOption: '{s name=window/title_new_option}Custom Products - New option{/s}',
    titleNewGroup: '{s name=window/title_new_group}Custom Products - New group{/s}',
    titleCharge: '{s name=window/title_charge}Custom Products - Charge: [name]{/s}',
    titleNewCharge: '{s name=window/title_new_charge}Custom Products - New charge{/s}',

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

    loadTitle: function(model, record) {
        var me = this, title, data ;
        if(!record) {
            title = '{s name=window/title}Custom Products{/s}';
        } else if(model == 'charge.Item') {
            title = record.get('id') ? me.titleCharge : me.titleNewCharge;
            data = record.data;
        } else if(record.get('id')) {
            title = model == 'main.Group' ? me.titleGroup : me.titleOption;
            data = record.data;
        } else {
            title = model == 'main.Group' ? me.titleNewGroup : me.titleNewOption;
            data = {};
        }
        title = new Ext.Template(title).applyTemplate(data);
        me.setTitle(title);
    },

    /**
     * Creates the fields sets and the sidebar for the detail page.
     * @return Array
     */
    getItems: function() {
        var me = this;
        return [{
            xtype: 'tabpanel',
            activeTab: 0,
            items: [{
                xtype: 'customizing-editor'
            }, {
                xtype: 'customizing-charge'
            }]
        }];
    }
});
//{/block}