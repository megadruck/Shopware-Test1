Ext.define( 'Shopware.apps.SofortLogView.model.Main', {
    extend: 'Ext.data.Model',
    fields: ['id', 'entry_date', 'version_module', 'source', 'message'],
    proxy:  {
        type:   'ajax',
        api:    {
            read: '{url action=loadStore}'
        },
        reader: {
            type:          'json',
            root:          'data',
            totalProperty: 'total'
        }
    }
} );