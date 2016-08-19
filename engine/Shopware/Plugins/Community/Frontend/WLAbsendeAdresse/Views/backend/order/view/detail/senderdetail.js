//{block name="backend/order/view/detail/detail" append}
//{namespace name="backend/senderaddress/main"}
Ext.define('Shopware.apps.Order.view.detail.Senderdetail', {
    override:'Shopware.apps.Order.view.detail.Detail',


    initComponent: function () {

        var me = this;
        me.callParent(arguments);
        me.loadRecord(me.record);


        var senderForm = Ext.create('Shopware.apps.Order.view.detail.Sender', { record: me.record, countriesStore: me.countriesStore });

        me.items.insert(3, senderForm);
        me.on('saveDetails', senderForm.updateSenderData);


        return me;

    }


});
//{/block}