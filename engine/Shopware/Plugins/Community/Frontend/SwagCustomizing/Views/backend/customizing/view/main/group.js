//{namespace name=backend/customizing/view/main}

//{block name="backend/customizing/view/main/group"}
Ext.define('Shopware.apps.Customizing.view.main.Group', {

    extend: 'Shopware.apps.Customizing.view.main.Form',

    alias: 'widget.customizing-group',

    plugins: [
        {
            pluginId: 'translation',
            ptype: 'translation',
            translationType: 'customizing-group'
        }
    ],

    /**
     * @return Array
     */
    getItems: function() {
        var me = this,
            groupId = me.record.data.id,
            store;

        store = Ext.create('Shopware.apps.Customizing.store.main.ArticleList');
        store.getProxy().extraParams.groupId = groupId;
        store.load();

        var mediaSelection = Ext.create('Shopware.MediaManager.MediaSelection', {
            fieldLabel: '{s name=group/fields/groupImage}Group image{/s}',
            name: 'imagePath',
            multiSelect: false,
            buttonText: '{s name=group/fields/uploadGroupImage}Upload{/s}',
            buttonConfig : {
                iconCls: 'sprite-drive-upload'
            }
        });

        return [

            mediaSelection,

            {
                fieldLabel: '{s name=group/fields/showGroupImage}Show group image{/s}',
                xtype: 'base-element-boolean',
                name: 'showGroupImage'
            },
            {
                fieldLabel: '{s name=group/fields/name}Name{/s}',
                name: 'name',
                translatable: true
            },
            {
                fieldLabel: '{s name=group/fields/showName}Show name{/s}',
                xtype: 'base-element-boolean',
                name: 'showName'
            },
            {
                xtype: 'base-element-html',
                label: '{s name=group/fields/description}Description{/s}',
                name: 'description',
                translatable: true
            },
            {
                fieldLabel: '{s name=group/fields/showDescription}Show Description{/s}',
                xtype: 'base-element-boolean',
                name: 'showDescription'
            },
            {
                fieldLabel: '{s name=group/fields/active}Active{/s}',
                xtype: 'base-element-boolean',
                name: 'active'
            },
            {
                xtype: 'gridpanel',
                store: store,
                title: '{s name=group/fields/assignedArticles}Assigned articles to this group{/s}',
                columns: [
                    { header: '{s name=group/fields/headerOrderNumber}Order number{/s}', dataIndex: 'orderNumber', flex: 1 },
                    { header: '{s name=group/fields/headerName}Article name{/s}', dataIndex: 'name', flex: 1 },
                    {
                        xtype: 'actioncolumn',
                        width: 25,
                        items: [{
                            iconCls: 'sprite-inbox--arrow',
                            width: 30,
                            tooltip: '{s name=group/fields/openArticleTooltip}Open article{/s}',
                            handler: function(grid, rowIndex, colIndex, metaData, event, record) {
                                me.fireEvent('openArticle', record.get('id'));
                            }
                        }]
                    }
                ]
            }
        ];
    }
});
//{/block}