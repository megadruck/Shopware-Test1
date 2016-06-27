//{block name="backend/customizing/store/charge/list"}
Ext.define('Shopware.apps.Customizing.store.charge.List', {
    extend: 'Ext.data.Store',
    model: 'Shopware.apps.Customizing.model.charge.List',
    remoteSort: true,
    remoteFilter: true,
    pageSize: 20,
    proxy: {
        type: 'ajax',
        api: {
            read : '{url action=getChargeList}',
            destroy: '{url action=deleteChargeItem}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
//{/block}
