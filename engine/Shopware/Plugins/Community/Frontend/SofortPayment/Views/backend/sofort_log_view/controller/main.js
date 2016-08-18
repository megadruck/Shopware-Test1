Ext.define( 'Shopware.apps.SofortLogView.controller.Main', {
    extend:     'Ext.app.Controller',
    mainWindow: null,
    init:       function ()
    {
        var me = this;
        me.mainWindow = me.getView( 'main.Window' ).create( {
            listStore: me.getStore( 'List' ).load()
        } );
        me.callParent( arguments );
    }
} );