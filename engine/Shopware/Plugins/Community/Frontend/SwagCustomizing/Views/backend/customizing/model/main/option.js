//{block name="backend/customizing/model/main/option"}
Ext.define('Shopware.apps.Customizing.model.main.Option', {
    extend: 'Ext.data.Model',

    fields: [
        //{block name="backend/customizing/model/main/option/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'groupId', type: 'int' },
        { name: 'name' },
        { name: 'typeId', type: 'int', defaultValue: null, useNull: true },
        { name: 'required', type: 'boolean' },
        { name: 'active', type: 'boolean', defaultValue: true },
        { name: 'position', type: 'int' },
        { name: 'number', defaultValue: null, useNull: true },
        { name: 'defaultValue', defaultValue: null, useNull: true },
        { name: 'emptyText', defaultValue: null, useNull: true },
        { name: 'maxUploads', defaultValue: 1, useNull: false},
        { name: 'maxValue', defaultValue: null, useNull: true },
        { name: 'minValue', defaultValue: null, useNull: true },
        { name: 'type', convert: function(v, record) {
            return record.raw && record.raw.type && record.raw.type.type;
        }, useNull: true }
    ],
    associations: [{
        type: 'hasMany',
        model: 'Shopware.apps.Customizing.model.main.Value',
        name: 'getValues',
        associationKey: 'values'
    }]
});
//{/block}