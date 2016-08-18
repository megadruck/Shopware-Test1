//{block name="backend/customer/view/detail/window" append}
//{namespace name="backend/senderaddress/main"}
Ext.define('Shopware.apps.Customer.view.detail.Senderoverview', {
    override:'Shopware.apps.Customer.view.detail.Window',

    senderTemplate:  null,
    senderPaneel: null,
    parent : null,


    createAddressFieldSet: function() {
        var me = this;

        me.parent = me.callParent(arguments);

        this.createSenderContainer();

        return  me.parent;

        /*
        me.billingPanel = me.createBillingContainer();
        me.shippingPanel = me.createShippingContainer();

        return me.addressContainer = Ext.create('Ext.container.Container', {
            minWidth: 250,
            layout: {
                type: 'hbox',
                align: 'stretch'
            },
            margin: '0 0 10 0',
            items: [
                me.billingPanel,
                { width: 10, border: 0 },
                me.shippingPanel
            ]
        });
        */
    },


    /**
     * Creates the Ext.panel.Panel for the sender information.
     */
    createSenderContainer: function() {
        var me = this;

        var requestUrl = '{url controller="Senderaddress" action="getCustomerSenderData"}';
        return this.requestSenderData(me.record.internalId, requestUrl);
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
            return;
        }

        if(data.salutation == 'mr'){
            data.salutation = 'Herr';
        }
        if(data.salutation == 'ms'){
            data.salutation = 'Frau';
        }
        if(data.country != null && data.country != 'null'){
            if(data.country.name == null || data.country.name == 'null'){
                data.country.name = '';
            }
        } else {
            data.country = {};
            data.country.name = '';
        }
        if(data.additionalAddressLine1 == null || data.additionalAddressLine1 == 'null'){
            data.additionalAddressLine1 = '';
        }
        if(data.additionalAddressLine2 == null || data.additionalAddressLine2 == 'null'){
            data.additionalAddressLine2 = '';
        }
        if(data.zipcode == null || data.zipcode == 'null'){
            data.zipcode = '';
        }
        if(data.company == null || data.company == 'null'){
            data.company = '';
        }

        var senderPanel = Ext.create('Ext.panel.Panel', {
            title: 'Standard-Absendeadresse',
            bodyPadding: 10,
            flex: 1,
            style: 'padding: 0 8 0 0 !important;',
            items: [
                {
                    xtype: 'container',
                    renderTpl:  me.createSenderTemplate(),
                    renderData:  data
                }
            ]
        });

        me.parent.items.insert(3, senderPanel);

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
            '<span id="sender-company" class="sender-company">{company}</span>',
            '</p>',
            '<p>',
            '<span id="sender-firstname" class="sender-firstname">{salutation} {firstname}</span>&nbsp;',
            '<span id="sender-lastname" class="sender-lastname">{lastname}</span>',
            '</p>',
            '<p>',
            '<span id="sender-street" class="sender-street">{street}</span>&nbsp;',
            '</p>',
            '<p>',
            '<span id="sender-add1" class="sender-add1">{additionalAddressLine1}</span>',
            '</p>',
            '<p>',
            '<span id="sender-add2" class="sender-add2">{additionalAddressLine2}</span>',
            '</p>',
            '<p>',
            '<span id="sender-zipcode" class="sender-zipcode">{zipcode}</span>&nbsp;',
            '<span id="sender-city" class="sender-city">{city}</span>',
            '</p>',
            '<p>',
            '<span id="sender-country-name" class="sender-country-name">{country.name}</span>',
            '</p>',
            '</div>',
            '</div>',

            '</tpl>{/literal}'
        );
    }

});
//{/block}