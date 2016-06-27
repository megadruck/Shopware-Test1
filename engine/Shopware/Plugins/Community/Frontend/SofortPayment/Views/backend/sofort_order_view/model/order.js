Ext.define( 'Shopware.apps.SofortOrderView.model.Order', {
    extend: 'Shopware.apps.Order.model.Order',
    fields: [
		//{block name="backend/order/model/order/fields"}{/block}
        { name : 'id', type: 'int' },
        { name : 'number', type: 'string' },
        { name : 'customerId' },
        { name : 'invoiceAmountNet', type: 'float' },
        { name : 'invoiceShippingNet', type: 'float' },
        { name : 'status' },
        { name : 'cleared'},
        { name : 'paymentId' },
        { name : 'transactionId', type: 'string' },
        { name : 'net', type: 'int' },
        { name : 'taxFree', type: 'int' },
        { name : 'partnerId', type: 'string' },
        { name : 'temporaryId', type: 'string' },
        { name : 'referer', type: 'string' },
        { name : 'clearedDate', type: 'date', dateFormat: 'd.m.Y' },
        { name : 'trackingCode', type: 'string' },
        { name : 'languageIso', type: 'string' },
        { name : 'dispatchId'},
        { name : 'currency', type: 'string' },
        { name : 'currencyFactor', type: 'float' },
        { name : 'shopId' },
        { name : 'remoteAddress', type: 'string' },
        { name : 'invoiceAmount', type: 'float' },
        { name : 'invoiceShipping', type: 'float' },
        { name : 'orderTime', type: 'date' },
        {
            name : 'invoiceShippingEuro',
            type: 'float',
            convert: function(value, record) {
                var factor = record.get('currencyFactor');
                if (!Ext.isNumeric(factor)) {
                    factor = 1;
                }
                return Ext.util.Format.round(record.get('invoiceShipping') / factor, 2);
            }
        },
        {
            name : 'invoiceAmountEuro',
            type: 'float',
            convert: function(value, record) {
                var factor = record.get('currencyFactor');
                if (!Ext.isNumeric(factor)) {
                    factor = 1;
                }
                return Ext.util.Format.round(record.get('invoiceAmount') / factor, 2);
            }
        }
    ],
    proxy:  {
        type:   'ajax',
        api:    {
            read: '{url action=loadStore}'
        },
        reader: {
            type:          'json',
            root:          'data'
        }
    }
} );