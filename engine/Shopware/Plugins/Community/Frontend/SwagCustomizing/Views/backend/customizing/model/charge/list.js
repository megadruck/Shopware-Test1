//{block name="backend/customizing/model/charge/list"}
Ext.define('Shopware.apps.Customizing.model.charge.List', {
    extend: 'Ext.data.Model',

    proxy: {
        type: 'ajax',
        api: {
            read : '{url action=getChargeList}',
            destroy: '{url action=deleteChargeItem}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    },

    fields: [
        //{block name="backend/customizing/model/charge/list/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'name' },
        { name: 'number' },
        { name: 'assignment', type: 'int', defaultValue: null, useNull: true }
    ]
});
//{/block}