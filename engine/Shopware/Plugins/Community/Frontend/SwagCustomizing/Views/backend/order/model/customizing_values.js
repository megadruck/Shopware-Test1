//{block name="backend/order/view/customizing/tab/model/option"}
Ext.define('Shopware.apps.Order.model.CustomizingValues', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'id', type: 'int' },
        { name: 'value', type: 'string' },
        { name: 'description', type: 'string' }
    ]
});
//{/block}