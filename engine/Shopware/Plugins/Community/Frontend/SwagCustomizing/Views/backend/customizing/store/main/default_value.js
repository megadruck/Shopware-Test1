//{block name="backend/customizing/store/main/default_value"}
Ext.define('Shopware.apps.Customizing.store.main.DefaultValue', {

    extend: 'Ext.data.Store',

    model:'Shopware.apps.Customizing.model.main.DefaultValue',

    remoteSort: false,

    remoteFilter: true,

    pageSize: 20,

    autoLoad: false

});
//{/block}