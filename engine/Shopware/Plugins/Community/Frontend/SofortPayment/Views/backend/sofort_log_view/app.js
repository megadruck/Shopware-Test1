Ext.define( 'Shopware.apps.SofortLogView', {
    extend:      'Enlight.app.SubApplication',
    name:        'Shopware.apps.SofortLogView',
    bulkLoad:    true,
    loadPath:    '{url action=load}',
    controllers: ['Main'],
    models:      ['Main'],
    views:       ['main.Window'],
    store:       ['List'],
    launch:      function ()
    {
        var me = this;
        me.windowTitle = '{$title}';
        var mainController = me.getController( 'Main' );
        return mainController.mainWindow;
    }
} );