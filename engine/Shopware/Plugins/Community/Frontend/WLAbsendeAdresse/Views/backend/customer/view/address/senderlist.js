//{block name="backend/customer/view/address/list" append}
//{namespace name="backend/senderaddress/main"}
Ext.define('Shopware.apps.Customer.view.detail.Senderlist', {

    override:'Shopware.apps.Customer.view.address.List',

   defaults: [],
   addressID:null,


    /**
     * Indicate default billing and shipping addresses
     *
     * @param value
     * @param col
     * @param record
     * @returns { String }
     */
    defaultColumnRenderer: function (value, col, record) {

        var me = this;
        me.defaults = [],
            customer = record.getCustomerStore && record.getCustomerStore.first();

        console.log(value);
        console.log(col);
        console.log(record);
        if (customer && customer.get('default_billing_address_id') == record.get('id')) {
            me.defaults.push('{s name="list/data/billingaddress"}{/s}');
        }

        if (customer && customer.get('default_shipping_address_id') == record.get('id')) {
            me.defaults.push('{s name="list/data/shippingaddress"}{/s}');
        }

        var customerId = record.getCustomerStore && record.getCustomerStore.first().get('id');
        var requestUrl = '{url controller="Senderaddress" action="getCustomerSenderData"}';
        return this.requestSenderData(customerId, requestUrl);



        //var me = this;

        //me.parent = me.callParent(arguments);
        //me.addressID = record.get('id');

       // console.log(me.parent);
        //console.log(me.addressID);





    },


    requestSenderData: function(customerId, requestUrl) {
        var me = this;


        me.setLoading(true);
        Ext.Ajax.request({
            url: requestUrl,
            async: false,
            params: {
                customerId: customerId
            },
            success: Ext.bind(me.onFetchSuccess, me),
            error: Ext.bind(me.onFetchError, me)
        });
    },

    /**
     * Event handler method which will be triggered when the batch stores of the product module was loaded and the
     * request to get the custom product preset was processed successfully.
     *
     * The methods updates the necessary elements, changes the configuration button text and provides the custom product
     * data globally in the component.
     *
     * @param { Object } response
     * @returns { boolean }
     */
    onFetchSuccess: function(response) {
        var me = this;

        me.setLoading(false);

        response = Ext.JSON.decode(response.responseText);

        var data = response.data;
        if(data == null){
            return me.defaults.join("<br/>");
        }


        if(data.id == me.addressID){
            me.defaults.push('Absender');
        }

        return me.defaults.join("<br/>");

    },

    /**
     * Event listener method which will be triggered when the request to get the custom product preset was not successful.
     *
     * The method deactivates the fieldset (e.g. this component) and disables an error message to notify the user.
     *
     * @returns void
     */
    onFetchError: function() {
        var me = this;

        response = Ext.JSON.decode(response.responseText);
        return defaults.join("<br/>");

    }



});
//{/block}
