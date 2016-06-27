Ext.define('Shopware.apps.Customizing.model.main.ArticleList', {
    extend: 'Ext.data.Model',

    fields: [
        { name: 'id', type: 'int' },
        { name: 'orderNumber', type: 'string' },
        { name: 'name', type: 'string' }
    ]
});