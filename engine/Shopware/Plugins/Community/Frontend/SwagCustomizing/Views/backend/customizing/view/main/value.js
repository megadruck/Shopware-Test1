//{namespace name=backend/customizing/view/main}

//{block name="backend/customizing/view/main/value"}
Ext.define('Shopware.apps.Customizing.view.main.Value', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.customizing-value',

    name: 'values',

    margin: '10 0 0 0',
    border: false,
    viewConfig: {
        emptyText: '{s name=value/empty_text}No values{/s}'
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
        }, {
            pluginId: 'translation',
            ptype: 'gridtranslation',
            translationType: 'customizing-value',
            translationKey: me.translationKey
        }];
    },

    getColumns: function() {
        var me = this, columns = [];
        return [{
            header: '{s name=value/columns/value}Value{/s}',
            dataIndex: 'value',
            editor: me.getValueEditor(),
            translationEditor: me.getValueEditor(),
            flex: 2
        }, {
            header: '{s name=value/columns/description}Description{/s}',
            dataIndex: 'description',
            editor: {
                xtype: 'textfield'
            },
            translationEditor: {
                xtype: 'textfield',
                name: 'description',
                fieldLabel: '{s name=value/columns/description}Description{/s}'
            },
            flex: 2
        }, {
            header: '{s name=value/columns/number}Number{/s}',
            dataIndex: 'number',
            editor: {
                xtype: 'textfield',
                maxLength: 30,
                maxLengthText: '{s name=form/fields/number_error}Max. 30 characters allowed{/s}'
            },
            flex: 2
        }, {
            header: '{s name=value/columns/position}Position{/s}',
            dataIndex: 'position',
            editor: {
                xtype: 'numberfield',
                minValue: 0,
                decimalPrecision: 0
            },
            flex: 1
        }, me.getActionColumn()];
    },

    getValueEditor: function() {
        var me = this, editor;
        if(me.fieldType == 'text') {
            editor = {
                xtype: 'base-element-' + me.fieldType,
                name: 'value'
            };
        }
        return editor;
    },

    getActionColumn: function() {
        var me = this;
        return {
            xtype: 'actioncolumn',
            width: 25,
            items: [{
                iconCls: 'sprite-minus-circle-frame',
                action: 'delete',
                tooltip: '{s name=value/delete_tooltip}Delete entry{/s}',
                handler: function (view, rowIndex, colIndex, item, opts, record) {
                    me.fireEvent('delete', view, record, rowIndex);
                }
            }]
        };
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

    getTopBar: function () {
        var me = this;
        return [{
            name: 'value',
            flex: 1,
            regex: null,
            xtype: 'base-element-' + me.fieldType
        }, {
            iconCls:'sprite-plus-circle-frame',
            cls: 'secondary small',
            text:'{s name=value/add_text}Add entry{/s}',
            action:'add'
        }];
    }
});
//{/block}