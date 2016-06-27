//{namespace name=backend/customizing/view/charge}

//{block name="backend/customizing/view/charge/list"}
Ext.define('Shopware.apps.Customizing.view.charge.List', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.customizing-charge-list',

    store: 'charge.List',

//    margin: '10 0 0 0',
    border: false,
    viewConfig: {
        emptyText: '{s name=list/empty_text}No values{/s}'
    },

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            dockedItems: me.getToolbar(),
            columns: me.getColumns()
        });

        me.addEvents(
            'delete'
        );

        me.callParent(arguments);
        me.store.load();
    },

    getColumns: function() {
        var me = this, columns = [];
        return [{
            header: '{s name=list/columns/name}Name{/s}',
            dataIndex: 'name',
            flex: 2
        }, {
            header: '{s name=list/columns/number}Number{/s}',
            dataIndex: 'number',
            flex: 2
        }];
    },

    getToolbar: function() {
        var me = this;
        return {
            xtype: 'toolbar',
            dock: 'bottom',
            border: false,
            cls: 'shopware-toolbar',
            items: me.getToolbarItems()
        };
    },

    getToolbarItems: function () {
        var me = this;
        return [{
            text: '{s name=list/options/add}Add item{/s}',
            cls: 'secondary small',
            action: 'add'
        }, {
            text: '{s name=list/options/remove}Remove item{/s}',
            cls: 'secondary small',
            disabled: true,
            action: 'remove'
        }];
    }
});
//{/block}