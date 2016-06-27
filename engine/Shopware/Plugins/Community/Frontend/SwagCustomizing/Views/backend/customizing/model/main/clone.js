//{block name="backend/customizing/model/main/clone"}
Ext.define('Shopware.apps.Customizing.model.main.Clone', {
    extend: 'Ext.data.Model',
    
    fields: [
        { name: 'groupId', type: 'int' },
        { name: 'groupName', type: 'string' }
    ],
    
    proxy: {
        type: 'ajax',
        api: {
            create : '{url action=cloneGroup}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }   
});
//{/block}