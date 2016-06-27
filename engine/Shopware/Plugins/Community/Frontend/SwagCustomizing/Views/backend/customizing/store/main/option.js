//{block name="backend/customizing/model/main/option"}
Ext.define('Shopware.apps.Customizing.store.main.Option', {
    extend: 'Ext.data.Store',
    model:'Shopware.apps.Customizing.model.main.Option',
    remoteSort: true,
    remoteFilter: true,
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: '{url action=getOptionList}',
        api: {
            create: '{url action=saveOption}',
            update: '{url action=saveOption}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
//{/block}