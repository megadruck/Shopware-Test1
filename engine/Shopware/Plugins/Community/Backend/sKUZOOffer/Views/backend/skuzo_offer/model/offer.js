/**
 *
 */
Ext.define('Shopware.apps.sKUZOOffer.model.Offer', {
    extend: 'Shopware.data.Model',

    /**
     * The fields used for this model
     * @array
     */
    configure: function() {
        return {
            controller: 'sKUZOOffer'

        };
    },

    fields: [

        { name : 'id', type: 'int' },
        { name : 'number', type: 'string' },
        { name : 'offerTime', type: 'date' },
        { name : 'invoiceAmount', type: 'float'},
        { name : 'discountAmount', type: 'float' },
        { name : 'invoiceShipping', type: 'float' },
        { name : 'invoiceAmountNet', type: 'float' },
        { name : 'invoiceShippingNet', type: 'float' },
        { name : 'customerId', type: 'int' },
        { name : 'orderId', type: 'int' },
        { name : 'orderNumber', type: 'int' },
        { name : 'dispatchId', type: 'int' },
        { name : 'paymentId', type: 'int' },
        { name : 'shopId', type: 'int' },
        { name : 'active', type: 'boolean' },
        { name : 'status', type: 'int' },
        { name : 'customerComment', type: 'string' },
        { name : 'internalComment', type: 'string' },
        { name : 'comment', type: 'string' },
        { name : 'currency', type: 'string' },
        { name : 'emailPreview', type: 'int' },
        {
            name: 'discount',
            type:'float',
            convert: function(value, record) {
                if (!Ext.isNumeric(record.get('invoiceAmount'))) {
                    return record.get('invoiceAmount');
                }
                return record.get('invoiceAmount') - record.get('discountAmount');
            }
        }
    ],
    associations:[
           { type:'hasMany', model:'Shopware.apps.sKUZOOffer.model.Position', name:'getPositions', associationKey:'details' },
           { type:'hasMany', model:'Shopware.apps.Base.model.Payment', name:'getPayment', associationKey:'payment' },
           { type:'hasMany', model:'Shopware.apps.Base.model.Shop', name:'getShop', associationKey:'shop' },
           { type:'hasMany', model:'Shopware.apps.Customer.model.Billing', name:'getBilling', associationKey:'billing' },
           { type:'hasMany', model:'Shopware.apps.sKUZOOffer.model.Receipt', name:'getReceipt', associationKey:'documents' },
           { type:'hasMany', model:'Shopware.apps.Base.model.Customer', name:'getCustomer', associationKey:'customer' },
           { type:'hasMany', model:'Shopware.apps.sKUZOOffer.model.States', name:'getStates', associationKey:'states' },
           { type:'hasMany', model:'Shopware.apps.sKUZOOffer.model.OfferBilling', name:'getOfferBilling', associationKey:'offerBilling' },
           { type:'hasMany', model:'Shopware.apps.sKUZOOffer.model.OfferShipping', name:'getOfferShipping', associationKey:'offerShipping' },
           { type:'hasMany', model:'Shopware.apps.Base.model.Dispatch', name:'getDispatch', associationKey:'dispatch' }
    ]

});