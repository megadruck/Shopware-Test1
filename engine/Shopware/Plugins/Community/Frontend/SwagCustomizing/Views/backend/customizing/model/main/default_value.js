//{block name="backend/customizing/model/main/default_value"}
Ext.define('Shopware.apps.Customizing.model.main.DefaultValue', {

    extend: 'Ext.data.Model',

    fields: [
        //{block name="backend/customizing/model/main/default_value/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'value', type: 'string' }
    ],

    proxy: {
        type: 'ajax',
        api: {
            read : '{url controller=customizing action=getDefaultValues}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
//{/block}