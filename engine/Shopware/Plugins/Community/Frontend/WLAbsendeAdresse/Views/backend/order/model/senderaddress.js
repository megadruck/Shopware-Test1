Ext.define('Shopware.apps.order.model.Senderaddress', {
    extend: 'Shopware.data.Model',


    configure: function () {
        return {
            related: 'Shopware.apps.Order.view.detail.Sender'
        };
    },

    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'orderId', type: 'int', useNull: true },
        { name : 'stateId', type: 'int', useNull: true },
        { name : 'countryId', type: 'int', useNull: true },
        { name : 'company', type: 'string' },
        { name : 'department', type: 'string' },
        { name : 'salutation', type: 'string' },
        { name : 'firstName', type: 'string' },
        { name : 'lastName', type: 'string' },
        { name : 'street', type: 'string' },
        { name : 'zipCode', type: 'string' },
        { name : 'city', type: 'string' },
        { name : 'additionalAddressLine1', type: 'string' },
        { name : 'additionalAddressLine2', type: 'string' },
        { name : 'customerId', type: 'int' },
    ],

    associations: [
        { relation: 'ManyToOne', field: 'orderId', type: 'hasMany', model: 'Shopware.apps.Order.model.Order', name: 'getOrder', associationKey: 'order'}
    ]
});
