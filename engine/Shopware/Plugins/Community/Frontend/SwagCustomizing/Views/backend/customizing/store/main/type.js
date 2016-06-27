//{block name="backend/customizing/store/main/type"}
Ext.define('Shopware.apps.Customizing.store.main.Type', {
    extend: 'Ext.data.Store',

    fields: [ 'id', 'name', 'type', 'parentName' ],
    autoLoad: true,
    pageSize: 1000,

    proxy: {
        type: 'ajax',
        url: '{url action=getTypeList}',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
//{/block}
