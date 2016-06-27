//{block name="backend/customizing/model/main/value"}
Ext.define('Shopware.apps.Customizing.model.main.Value', {
    extend: 'Ext.data.Model',

    fields: [
        //{block name="backend/customizing/model/main/value/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'optionId', type: 'int' },
        { name: 'value', type: 'string' },
        { name: 'number', type: 'string', defaultValue: null, useNull: true },
        { name: 'description', type: 'string', defaultValue: null, useNull: true },
        { name: 'position', type: 'int' }
    ]
});
//{/block}