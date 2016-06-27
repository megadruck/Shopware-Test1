//{block name="backend/customizing/store/main/list"}
Ext.define('Shopware.apps.Customizing.store.main.List', {
    extend: 'Ext.data.TreeStore',

    model:'Shopware.apps.Customizing.model.main.List',

    pageSize: 20,

    proxy: {
        type: 'ajax',
        api: {
            read : '{url action=getList}',
            destroy: '{url action=deleteListItem}'
        },
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'count'
        },
        extraParams: {
            start: 0,
            limit: 20,
            searchValue: ''
        }
    }
});
//{/block}
