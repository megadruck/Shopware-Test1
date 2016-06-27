//{block name="backend/order/view/customizing/tab/model/option"}
Ext.define('Shopware.apps.Order.model.CustomizingOption', {
    extend: 'Ext.data.Model',

    idProperty: 'uniqueId',

    fields: [
        { name: 'id', type: 'int' },
        { name: 'name', type: 'string' },
        { name: 'required', type: 'int' },
        { name: 'detailId', type: 'int' },
        { name: 'typeId', type: 'int' },
        { name: 'articleName', type: 'string' },
        { name: 'articleNumber', type: 'string' },
        { name: 'number', type: 'string' },
        { name: 'selectedValue', type: 'string' },
        { name: 'selectedValueDescription', type: 'string' }
    ],

    associations: [
        { type: 'hasMany', model: 'Shopware.apps.Order.model.CustomizingValues', name: 'getCustomizingValues', associationKey: 'values' }
    ]
});
//{/block}