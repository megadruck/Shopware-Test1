//{namespace name=backend/customizing/view/charge}

//{block name="backend/customizing/view/charge/value"}
Ext.define('Shopware.apps.Customizing.view.charge.Value', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.customizing-charge-value',

    name: 'values',
    isPropertyFilter: true,
    sortableColumns: false,

//    margin: '10 0 0 0',
    border: false,
    viewConfig: {
        emptyText: '{s name=scale/empty_text}No values{/s}'
    },

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            dockedItems: me.getToolbar(),
            columns: me.getColumns(),
            plugins: me.getPlugins()
        });

        me.addEvents(
            'delete'
        );

        me.callParent(arguments);
    },

    getPlugins: function() {
        var me = this;
        return [{
            ptype: 'cellediting',
            pluginId: 'cellediting',
            clicksToEdit: 1
        }];
    },

    getToolbar: function() {
        var me = this;
        return {
            xtype: 'toolbar',
            dock: 'top',
            border: false,
            cls: 'shopware-toolbar',
            items: me.getTopBar()
        };
    },

    getColumns: function() {
        var me = this;

        return [{
            header: '{s name=scale/columns/from}Item quantity{/s}',
            dataIndex: 'from',
            align: 'right',
            flex: 1,
            xtype: 'numbercolumn',
            format: '0,000',
            editor: {
                xtype: 'numberfield',
                minValue: 1,
                decimalPrecision: 0
            }
        }, {
            xtype: 'numbercolumn',
            header: '{s name=scale/columns/value}Value{/s}',
            dataIndex: 'value',
            align: 'right',
            flex: 1,
            format: '0,000.00',
            editor: {
                xtype: 'numberfield',
                minValue: 0,
                decimalPrecision: 2
            }
        }, me.getActionColumn()];
    },

    getActionColumn: function() {
        var me = this;
        return {
            xtype: 'actioncolumn',
            width: 25,
            items: [{
                iconCls: 'sprite-minus-circle-frame',
                action: 'delete',
                tooltip: '{s name=scale/delete_tooltip}Delete entry{/s}',
                handler: function (view, rowIndex, colIndex, item, opts, record) {
                    me.fireEvent('delete', view, record, rowIndex);
                }
            }]
        };
    },

    getTopBar: function () {
        var me = this;
        return [{
            xtype: 'base-element-select',
            flex: 1,
            isPropertyFilter: true,
            name: 'customerGroupId',
            value: 1,
            store: 'base.CustomerGroup',
            emptyText: '{s name=scale/customer_group_empty_text}Please select...{/s}'
        }, {
            iconCls:'sprite-plus-circle-frame',
            text:'{s name=scale/add_text}Add entry{/s}',
            cls: 'secondary small',
            action:'add'
        }];
    }
});
//{/block}