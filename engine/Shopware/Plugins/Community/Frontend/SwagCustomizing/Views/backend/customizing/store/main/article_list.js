Ext.define('Shopware.apps.Customizing.store.main.ArticleList', {
    extend: 'Ext.data.Store',

    model:'Shopware.apps.Customizing.model.main.ArticleList',

    proxy: {
        type: 'ajax',
        api: {
            read : '{url action=getArticleList}'
        },
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});