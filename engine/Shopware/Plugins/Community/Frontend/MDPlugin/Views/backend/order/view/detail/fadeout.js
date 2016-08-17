/* 
 
 * Copyright (c) MegaDruck.de Produktions- und Vertriebs GmbH
 * Joerg Frintrop
 * j.frintrop@megadruck.de
 */
//{block name="backend/order/view/detail/overview" append}
 
Ext.define('Shopware.apps.Order.view.detail.Fadeout', {
    override:'Shopware.apps.Order.view.detail.Overview',

 /**
     * @returns { Shopware.attribute.Form }
     */
    createAttributeForm: function() {
        var me = this;
        me.attributeForm = null;
        me.attributeForm = Ext.create('Shopware.attribute.Form', {
            collapsed: true,
            collapsible: true,
            flex:1,
            table: 's_order_attributes',
            name: 'order-attributes',
            title: me.snippets.details.attribute_title,
            border: true,
            margin: '10 0',
            bodyPadding: 10,
            listeners: {
                'hide-attribute-field-set': function() {
                    me.attributeForm.hide();
                }
            }
        });
        return me.attributeForm;
    },
    
    createDetailsContainer: function() {
        var me = this;
        me.detailsForm = null;
        me.detailsForm = Ext.create('Ext.form.Panel', {
            collapsed: true,
            collapsible: true,
            flex:1,
            title: me.snippets.details.title,
            bodyPadding: 10,
            layout: 'anchor',
            defaults: {
                anchor: '100%'
            },
            margin: '10 0',
            items: [
                me.createInnerDetailContainer()
            ]
        });
        return me.detailsForm;
    }
    });
//{/block}

