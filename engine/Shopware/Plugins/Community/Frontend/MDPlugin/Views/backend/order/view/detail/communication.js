/* 
 
 * Copyright (c) MegaDruck.de Produktions- und Vertriebs GmbH
 * Joerg Frintrop
 * j.frintrop@megadruck.de
 */

//{namespace name=backend/order/main}
//{block name="backend/order/view/detail/communication" append}
Ext.define('Shopware.apps.Order.view.detail.Communication', {

    /**
     * Defines an override applied to a class.
     * @string
     */
    override: 'Shopware.apps.Order.view.detail.Communication',
    
    createInternalFieldSet: function() {
        var me = this;

        return Ext.create('Ext.form.FieldSet', {
            title: me.snippets.internal.title,
            defaults: {
                labelWidth: 155,
                labelStyle: 'font-weight: 700;'
            },
            layout: 'anchor',
            minWidth:250,
            items: me.createInternalElements()
        });
    }
});
//{/block}