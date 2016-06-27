/**
 * Extend the article main controller, to load the store and register on the 'storeChanged' events
 */
//{block name="backend/article/controller/main" append}
Ext.define('Shopware.apps.Article.controller.Main-Customizing', {
    override: 'Shopware.apps.Article.controller.Main',
    init: function(){
        var me = this;
        // Add customizing store
        me.getStore('Customizing');

        me.callOverridden(arguments);


        // SW > 4.1.0: Connect to the storesChanged-Event in order to refresh the data in SplitView
        me.subApplication.on('ProductModule:storesChanged', me.onSwagCustomizingArticleChanged);

    },

    /**
     * Callback function called, when the article was changed by the SplitView
     */
    onSwagCustomizingArticleChanged: function() {
        var subApplication = this,
            mainController = subApplication.getController('Main'),
            mainWindow = mainController.mainWindow,
            customizingField = mainController.mainWindow.down('article-base-field-set base-element-select[name=customizingId]'),
            article;


        if (mainWindow) {
            article = mainWindow.article;
        }

        if (!mainWindow || !article) {
            return;
        }
        
        if (customizingField) {
            customizingField.setValue(article.data.customizingId);
        }


    }

});

Ext.define('Shopware.apps.Article.store.Customizing', {
    extend: 'Ext.data.Store',
    fields: ['id', 'name'],
    autoLoad: true,
    proxy: {
        type:'ajax',
        url:'{url controller=customizing action=getGroupList}',
        reader:{
            type:'json',
            root:'data',
            totalProperty:'total'
        }
    }
});
//{/block}

/**
 * Extends the article model and adds the customizingId field
 */
/*
{block name="backend/article/model/article/fields" append}
    { name: 'customizingId', type: 'int', useNull : true },
{/block}
*/

/**
 * Extend the article's base fieldSet and add our customizing field
 */
//{block name="backend/article/view/detail/base" append}
Ext.define('Shopware.apps.Article.view.detail.Base-Customizing', {
    override: 'Shopware.apps.Article.view.detail.Base',
    createRightElements: function() {
        var me = this,
            elements = me.callOverridden(arguments);
        elements.push({
            xtype: 'base-element-select',
            name: 'customizingId',
            emptyText: me.snippets.empty,
            fieldLabel: 'Custom Products',
            allowBlank: true,
            store: 'Customizing',
            listeners: {
                change: function (combo, value) {
                    if (value == null || value == ' ') {
                        var record = this.up('form').getRecord();
                        record.set('customizingId', null);
                    }
                }
            }
        });
        return elements;
    }
});
//{/block}