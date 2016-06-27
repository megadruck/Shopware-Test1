//{block name="backend/customizing/store/charge/item"}
Ext.define('Shopware.apps.Customizing.store.charge.Item', {
    extend: 'Ext.data.Store',
    model: 'Shopware.apps.Customizing.model.charge.Item',
    remoteSort: true,
    remoteFilter: true,
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: '{url action=getChargeItemList}',
        api: {
            create: '{url action=saveChargeItem}',
            update: '{url action=saveChargeItem}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
//{/block}
