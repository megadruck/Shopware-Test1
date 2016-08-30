//{block name="backend/order/view/detail/overview" append}
//{namespace name="backend/senderaddress/main"}
Ext.define('Shopware.apps.Order.view.detail.Senderoverview', {
    override:'Shopware.apps.Order.view.detail.Overview',

    senderTemplate:  null,
    senderPaneel: null,
    parent : null,

    createCustomerInformation: function() {
        var me = this;

        me.parent = me.callParent(arguments);

        this.createSenderContainer();



        return  me.parent;
    },

    /**
     * Creates the Ext.panel.Panel for the sender information.
     */
    createSenderContainer: function() {
        var me = this;

        var requestUrl = '{url controller="Senderaddress" action="getSenderData"}';
        return this.requestSenderData(me.record.internalId, requestUrl);
    },

    requestSenderData: function(orderId, requestUrl) {
        var me = this;


        me.setLoading(true);
        Ext.Ajax.request({
            url: requestUrl,
            async: false,
            params: {
                orderId: orderId
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
            return;
        }

        if(data.salutation == 'mr'){
            data.salutation = 'Herr';
        }
        if(data.salutation == 'ms'){
            data.salutation = 'Frau';
        }

        var senderPanel = Ext.create('Ext.panel.Panel', {
            title: 'Absendeadresse',
            bodyPadding: 10,
            flex: 1,
            paddingRight: 5,
            items: [
                {
                    xtype: 'container',
                    renderTpl:  me.createSenderTemplate(),
                    renderData:  data
                }
            ]
        });

        me.parent.items.insert(2, senderPanel);

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
        console.log(response);

        me.setLoading(false);
        me.setDisabled(true);

    },

    createSenderTemplate:function () {
        return new Ext.XTemplate(
            '{literal}<tpl for=".">',
            '<div class="customer-info-pnl">',
            '<div class="base-info">',
            '<p>',
            '<span>{company}</span>',
            '</p>',
            '<p>',
            '<span>{salutation} {firstName}</span>&nbsp;',
            '<span>{lastName}</span>',
            '</p>',
            '<p>',
            '<span>{street}</span>&nbsp;',
            '</p>',
            '<p>',
            '<span>{additionalAddressLine1}</span>',
            '</p>',
            '<p>',
            '<span>{additionalAddressLine2}</span>',
            '</p>',
            '<p>',
            '<span>{zipCode}</span>&nbsp;',
            '<span>{city}</span>',
            '</p>',
            '<p>',
            '<span>{country.name}</span>',
            '</p>',
            '</div>',
            '</div>',

            '</tpl>{/literal}'
        );
    }

});
//{/block}