//{block name="backend/customizing/application"}
Ext.define('Shopware.apps.Customizing', {
    extend: 'Enlight.app.SubApplication',
    bulkLoad: true,

    loadPath: '{url action=load}',
    /*{if !$licenceCheck}*/
    controllers: ['Charge', 'Main', 'Custompaging'],

    views: [
        'charge.Panel',
        'charge.List',
        'charge.Form'
    ],
    /*{/if}*/
    /**
     * This method will be called when all dependencies are solved and
     * all member controllers, models, views and stores are initialized.
     */
    launch: function() {
        var me = this;

        /*{if $licenceCheck}*/
        Shopware.Notification.createGrowlMessage('{s name=main/license/error}Error{/s}', '{s name=main/license/message}License check for module SwagCustomizing has failed{/s}');
        /*{else}*/
        me.controller = me.getController('Main');
        return me.controller.mainWindow;
        /*{/if}*/
    }
});
//{/block}
