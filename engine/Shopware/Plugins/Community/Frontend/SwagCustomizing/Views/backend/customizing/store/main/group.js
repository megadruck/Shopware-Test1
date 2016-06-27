//{block name="backend/customizing/model/main/group"}
Ext.define('Shopware.apps.Customizing.store.main.Group', {
    extend: 'Ext.data.Store',
    model:'Shopware.apps.Customizing.model.main.Group',
    remoteSort: true,
    remoteFilter: true,
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: '{url action=getGroupList}',
        api: {
            create: '{url action=saveGroup}',
            update: '{url action=saveGroup}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
//{/block}