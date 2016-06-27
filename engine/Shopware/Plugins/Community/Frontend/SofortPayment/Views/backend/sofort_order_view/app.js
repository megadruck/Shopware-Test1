Ext.define('Shopware.apps.SofortOrderView', {
    extend: 'Enlight.app.SubApplication',
    name: 'Shopware.apps.SofortOrderView',
    bulkLoad: true,
    loadPath: '{url action=load}',
    controllers: ['Main'],
    store: ['Order'],
    models: ['Order'],
    views: ['main.Window', 'list.List'],
    launch: function()
    {
        var me = this;
        me.windowTitle = '{$title}';
        var mainController = me.getController('Main');
        return mainController.mainWindow;
    }
});