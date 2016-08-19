/* 
 
 * Copyright (c) MegaDruck.de Produktions- und Vertriebs GmbH
 * Joerg Frintrop
 * j.frintrop@megadruck.de
 */

//{namespace name=backend/order/main}
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
            title: '{s name="attribute_title"}{/s}',
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
    },
    
    /**
     * Creates the XTemplate for the billing information panel
     *
     * @return [Ext.XTemplate] generated Ext.XTemplate
     */
    createPaymentTemplate:function () {
        var me = this;
        return new Ext.XTemplate(
            '{literal}<tpl for=".">',
                '<div class="customer-info-pnl">',
                    '<div class="base-info">',
                        '<p>',
                            '<span>{description}</span>',
                        '</p>',                       
                        '<p>',
                            '<span>{invoiceAmount}</span>',
                        '</p>',
                    '</div>',
                '</div>',
            '</tpl>{/literal}'
        );
    }

        
    }); 
//{/block}
