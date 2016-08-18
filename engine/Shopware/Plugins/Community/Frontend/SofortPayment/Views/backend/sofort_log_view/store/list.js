Ext.define( 'Shopware.apps.SofortLogView.store.List', {
    extend:   'Ext.data.Store',
    autoLoad: false,
    pageSize: 20,
    model:    'Shopware.apps.SofortLogView.model.Main'
} );