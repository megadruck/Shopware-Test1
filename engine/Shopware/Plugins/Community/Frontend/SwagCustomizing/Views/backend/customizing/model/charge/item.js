//{block name="backend/customizing/model/charge/item"}
Ext.define('Shopware.apps.Customizing.model.charge.Item', {
    extend: 'Ext.data.Model',

    fields: [
        //{block name="backend/customizing/model/charge/item/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'name', type: 'string' },
        { name: 'number', type: 'string' },
        { name: 'percentage', type: 'boolean' }
    ],

    associations: [{
        type: 'hasMany',
        model: 'Shopware.apps.Customizing.model.charge.Value',
        name: 'getValues',
        associationKey: 'values'
    }]
});
//{/block}