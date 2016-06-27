//{block name="backend/customizing/model/charge/value"}
Ext.define('Shopware.apps.Customizing.model.charge.Value', {
    extend: 'Ext.data.Model',

    fields: [
        //{block name="backend/customizing/model/charge/value/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'from', type: 'int' },
        { name: 'value', type: 'float' },
        { name: 'customerGroupId', type: 'int' }
    ]
});
//{/block}