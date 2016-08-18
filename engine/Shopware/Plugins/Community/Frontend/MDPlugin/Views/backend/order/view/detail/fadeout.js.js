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
    initComponent: function() {
        var me = this;
        
        me.attributeForm = Ext.create('Shopware.attribute.Form', {
            collapsed: true,
            collapsible: true, 
            flex:2,
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
    }
//{/block}

