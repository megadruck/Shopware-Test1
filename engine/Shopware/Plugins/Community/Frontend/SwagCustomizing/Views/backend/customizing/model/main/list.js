//{block name="backend/customizing/model/main/list"}
Ext.define('Shopware.apps.Customizing.model.main.List', {
    extend: 'Ext.data.Model',

    proxy: {
        type: 'ajax',
        api: {
            destroy: '{url action=deleteListItem}',
            update: '{url action=moveTreeItem}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    },

    fields: [
        //{block name="backend/customizing/model/main/list/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'groupId', type: 'int', defaultValue: null, useNull: true },
        { name: 'optionId', type: 'int', defaultValue: null, useNull: true },
        { name: 'name' },
        { name: 'position', type: 'int' },
        { name: 'active', type: 'boolean' },
        { name: 'assignment', type: 'int', defaultValue: null, useNull: true },
        { name: 'leaf', convert: function(v, record) { return !!record.data.optionId; } }
    ]
});
//{/block}