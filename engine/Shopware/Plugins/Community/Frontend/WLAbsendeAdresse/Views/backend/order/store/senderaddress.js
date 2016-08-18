Ext.define('Shopware.apps.Order.store.Senderaddress', {
    extend:'Shopware.store.Listing',
    configure: function() {
        return { controller: 'Senderaddress' };
    },
    model: 'Shopware.apps.Order.model.Senderaddress'
});