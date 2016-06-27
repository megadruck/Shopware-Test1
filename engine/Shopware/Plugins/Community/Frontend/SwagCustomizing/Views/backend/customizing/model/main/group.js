//{block name="backend/customizing/model/main/group"}
Ext.define('Shopware.apps.Customizing.model.main.Group', {
    extend: 'Ext.data.Model',

    fields: [
        //{block name="backend/customizing/model/main/group/fields"}{/block}
        { name: 'id', type: 'int', useNull: true },
        { name: 'name' },
        { name: 'imagePath' },
        { name: 'showGroupImage', type: 'boolean' },
        { name: 'description', defaultValue: null, useNull: true },
        { name: 'active', type: 'boolean' },
        { name: 'showName', type: 'boolean' },
        { name: 'showDescription', type: 'boolean' },
        { name: 'position', type: 'int' }
    ]
});
//{/block}