/*
 * Copyright (c) 2016
 * Megadruck.de Produktions- und Vertriebs GmbH
 * Joerg Frintrop
 * j.frintrop@megadruck.de
 *
 * Eichendorffstrasse 34b
 * 26655 Westerstede
 * Tel. 04488 52540-25
 * Fax. 04488 52540-14
 *
 * www.megadruck.de
 */

//{namespace name=backend/order/main}
//{block name="backend/order/view/list/filter" append}

Ext.define('Shopware.apps.Order.view.list.mdFilter', {
    override: 'Shopware.apps.Order.view.list.Filter',

    createFieldContainer: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            border: false,
            padding: 10,
            items: [
                me.createFilterForm(),
                me.createFilterForm2(),
                me.createFilterButtons(),
            ]
        });
    },
    createFilterForm: function () {
        var me = this;
        me.filterForm = null;
        me.filterForm = Ext.create('Ext.form.Panel', {
            border: false,
            cls: Ext.baseCSSPrefix + 'filter-form',
            defaults: {
                anchor: '98%',
                labelWidth: 155,
                minWidth: 250,
                xtype: 'pagingcombo',
                style: 'box-shadow: none;',
                labelStyle: 'font-weight:700;'
            },
            items: [
                me.createOrderStatusField(),
              ]
        });
        return me.filterForm;
    },
    createFilterForm2: function () {
        var me = this;
        me.filterForm2 = Ext.create('Ext.form.Panel', {
            border: false,
            collapsed: true,
            collapsible: true,
            title:'Weitere Optionen',
            cls: Ext.baseCSSPrefix + 'filter-form',
            defaults: {
                anchor: '98%',
                labelWidth: 155,
                minWidth: 250,
                xtype: 'pagingcombo',
                style: 'box-shadow: none;',
                labelStyle: 'font-weight:700;'
            },
            items: [
                me.createFromField(),
                me.createToField(),
                me.createPaymentStatusField(),
                me.createPaymentField(),
                me.createDispatchField(),
                me.createCustomerGroupField(),
                me.createArticleSearch(),
                me.createShopField(),
                me.createPartnerField()
            ]
        });
        me.filterForm.insert(1,me.filterForm2);
        return me.filterForm;
    },

    createOrderStatusField: function() {
        var me = this;

        return Ext.create('Ext.form.field.ComboBox', {
            name: 'orders.status',
            queryMode: 'local',
            store: me.orderStatusStore,
            valueField: 'id',
            displayField: 'description',
            emptyText: me.snippets.empty,
            fieldLabel: me.snippets.orderState
        });
    }

});
//{/block}