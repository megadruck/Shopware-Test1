Ext.define( 'Shopware.apps.SofortOrderView.store.Order', {
    extend:   'Ext.data.Store',
    autoLoad: false,
    pageSize: 20,
    model:    'Shopware.apps.SofortOrderView.model.Order'
} );

