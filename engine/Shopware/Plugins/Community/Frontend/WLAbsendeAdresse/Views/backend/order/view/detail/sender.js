
//{block name="backend/order/view/detail/sender"}
Ext.define('Shopware.apps.Order.view.detail.Sender', {
    /**
     * Define that the billing field set is an extension of the Ext.form.FieldSet
     * @string
     */
    extend:'Ext.form.FieldSet',
    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias:'widget.order-sender-field-set',
    /**
     * Set css class for this component
     * @string
     */
    cls: Ext.baseCSSPrefix + 'sender-field-set',
    /**
     * Layout for the component.
     * @string
     */
    layout: 'column',
    /**
     * Contains all snippets for the view component
     * @object
     */
    snippets:{
        title:'{s name=sender/title}Absendeadresse{/s}',
        salutation:{
            label:'{s name=address/salutation}Anrede{/s}',
            mr:'{s name=address/salutation_mr}Herr{/s}',
            ms:'{s name=address/salutation_ms}Frau{/s}'
        },
        titleField:'{s name=address/title_field}Titel{/s}',
        firstName:'{s name=address/first_name}Vorname{/s}',
        lastName:'{s name=address/last_name}Nachname{/s}',
        street:'{s name=address/street}Strasse{/s}',
        zipCode:'{s name=address/zip_code}PLZ{/s}',
        city:'{s name=address/city}Stadt{/s}',
        additionalAddressLine1:'{s name=address/additionalAddressLine1}Adresszusatz 1{/s}',
        additionalAddressLine2:'{s name=address/additionalAddressLine2}Adresszusatz 2{/s}',
        state:'{s name=address/state}Bundesland{/s}',
        birthday:'{s name=address/birthday_label}geburtstag{/s}',
        country:'{s name=address/country}Land{/s}',
        phone:'{s name=address/phone}Telefon{/s}',
        company:'{s name=address/company}Firma{/s}',
        department:'{s name=address/department}Abteilung{/s}',
        vatId:'{s name=address/vat_id}Ust ID{/s}',
        fax:'{s name=address/fax}Fax{/s}'
    },


    fieldSalutation: '[0]',

    requestUrl: '{url controller="Senderaddress" action="getSenderData"}',



    /**
     * The initComponent template method is an important initialization step for a Component.
     * It is intended to be implemented by each subclass of Ext.Component to provide any needed constructor logic.
     * The initComponent method of the class being created is called first,
     * with each initComponent method up the hierarchy to Ext.Component being called thereafter.
     * This makes it easy to implement and, if needed, override the constructor logic of the Component at any step in the hierarchy.
     * The initComponent method must contain a call to callParent in order to ensure that the parent class' initComponent method is also called.
     *
     * @return void
     */
    initComponent:function () {
        var me = this;
        me.title = me.snippets.title;

        me.items = me.createElements();

        // Custom event which be fired when the product has changed (e.g. store loaded / split view change)
        me.requestSenderData(me.record.internalId);


        me.addEvents(
            /**
             * Fired when the user changes his country. Used to fill the state box
             * @param field
             * @param newValue
             */
            'countryChanged'

        );
        me.callParent(arguments);

    },

    /**
     * Creates the both containers for the field set
     * to display the form fields in two columns.
     *
     * @return [Array] Contains the left and right container
     */
    createElements:function () {
        var leftContainer, rightContainer, me = this;

        leftContainer = Ext.create('Ext.container.Container', {
            columnWidth:.5,
            border:false,
            layout:'anchor',
            defaults:{
                anchor:'95%',
                labelWidth:155,
                minWidth:250,
                labelStyle: 'font-weight: 700;',
                style: {
                    margin: '0 0 10px'
                },
                xtype:'textfield'
            },
            items:me.createLeftElements()
        });

        rightContainer = Ext.create('Ext.container.Container', {
            columnWidth:.5,
            border:false,
            layout:'anchor',
            defaults:{
                anchor:'95%',
                labelWidth:155,
                minWidth:250,
                labelStyle: 'font-weight: 700;',
                style: {
                    margin: '0 0 10px'
                },
                xtype:'textfield'
            },
            items:me.createRightElements()
        });

        var id = null;


        return [ leftContainer, rightContainer ];
    },


    /**
     *
     * @param { Shopware.apps.Order.store.Batch } order
     * @returns void
     */
    requestSenderData: function(orderId) {
        var me = this;


        me.setLoading(true);
        Ext.Ajax.request({
            url: me.requestUrl,
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

        var itemArray = me.items.items;
        var leftSide = itemArray[0].items.items;
        var rightSide = itemArray[1].items.items;

        var anredeField = leftSide[0];
        var vornameField = leftSide[1];
        var nachnameField = leftSide[2];
        var firmaField = leftSide[3];
        var abteilungField = leftSide[4];

        anredeField.setValue(response.data['salutation']);
        vornameField.setValue(response.data['firstName']);
        nachnameField.setValue(response.data['lastName']);
        firmaField.setValue(response.data['company']);
        abteilungField.setValue(response.data['department']);

        var strasseField = rightSide[0];
        var addr1Field = rightSide[1];
        var addr2Field = rightSide[2];
        var plzField = rightSide[3];
        var stadtField = rightSide[4];
        var bundeslandField = rightSide[5];
        var landField = rightSide[6];

        strasseField.setValue(response.data['street']);
        addr1Field.setValue(response.data['additionalAddressLine1']);
        addr2Field.setValue(response.data['additionalAddressLine2']);
        plzField.setValue(response.data['zipCode']);
        stadtField.setValue(response.data['city']);
        if(response.data['state'] != null && response.data['state'] != 'null'){
            bundeslandField.setValue(response.data['state']['id']);
        }
        if(response.data['country'] != null && response.data['country'] != 'null') {
            landField.setValue(response.data['country']['id']);
        }

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

   /**
    * Event listener method which will be triggered when the request to get the custom product preset was not successful.
    *
    * The method deactivates the fieldset (e.g. this component) and disables an error message to notify the user.
    *
    * @returns void
    */
    updateSenderData: function() {
        var me = this;

        var postUrl = '{url controller="Senderaddress" action="updateSenderData"}';
        var order = me.record.internalId;

        var toplevel = me.items.items;
        // wir kommen hier von einer ebene weiter oben an
        var itemArray = toplevel[3].items.items;
        var leftSide = itemArray[0].items.items;
        var rightSide = itemArray[1].items.items;

        var anredeField = leftSide[0];
        var vornameField = leftSide[1];
        var nachnameField = leftSide[2];
        var firmaField = leftSide[3];
        var abteilungField = leftSide[4];



        var strasseField = rightSide[0];
        var addr1Field = rightSide[1];
        var addr2Field = rightSide[2];
        var plzField = rightSide[3];
        var stadtField = rightSide[4];
        var bundeslandField = rightSide[5];
        var landField = rightSide[6];

       console.log(anredeField);

       var data = {};
       data.salutation = anredeField.value;
       data.firstname = vornameField.value;
       data.lastname = nachnameField.value;
       data.company = firmaField.value;
       data.department = abteilungField.value;
       data.street = strasseField.value;
       data.additional_address_line1 = addr1Field.value;
       data.additional_address_line2 = addr2Field.value;
       data.zipcode = plzField.value;
       data.city = stadtField.value;
       data.stateId = bundeslandField.value;
       data.countryId = landField.value;


       console.log(data);
       Ext.Ajax.request({
           method: 'POST',
           params: {
               orderId: order
           },
           jsonData: data,
           url: postUrl
       });

    },

    /**
     * Creates the left container of the sender field set.
     *
     * @return Ext.container.Container Contains the three components
     */
    createLeftElements:function () {
        var me = this;

        return [{
            xtype:'combobox',
            queryMode: 'local',
            triggerAction:'all',
            name:'sender[salutation]',
            fieldLabel:me.snippets.salutation.label,
            mode:'local',
            editable:false,
            allowBlank: false,
            valueField: 'key',
            displayField: 'label',
            store: Ext.create('Shopware.apps.Base.store.Salutation').load()
        }, {
            name:'sender[firstName]',
            fieldLabel:me.snippets.firstName,
            allowBlank:false
        }, {
            name:'sender[lastName]',
            fieldLabel:me.snippets.lastName,
            required:true,
            allowBlank:false
        }, {
            name:'sender[company]',
            fieldLabel:me.snippets.company
        }, {
            name:'sender[department]',
            fieldLabel:me.snippets.department
        }];
    },

    /**
     * Creates the left container of the billing field set.
     *
     * @return Ext.container.Container Contains the three components
     */
    createRightElements:function () {
        var me = this;

        me.countryStateCombo = Ext.create('Ext.form.field.ComboBox', {
            name:'sender[stateId]',
            action: 'senderStateId',
            fieldLabel:me.snippets.state,
            valueField: 'id',
            displayField: 'name',
            forceSelection: true,
            labelWidth:155,
            store: Ext.create('Shopware.store.CountryState').load(),
            minWidth: 250,
            editable: false,
            hidden: true,
            triggerAction:'all',
            queryMode: 'local'
        });

        me.countryCombo = Ext.create('Ext.form.field.ComboBox', {
            triggerAction:'all',
            name:'sender[countryId]',
            fieldLabel:me.snippets.country,
            valueField:'id',
            queryMode: 'local',
            displayField:'name',
            forceSelection: true,
            store:me.countriesStore,
            labelWidth:155,
            minWidth:250,
            required:true,
            editable:false,
            allowBlank:false,
            listeners: {
                change: function(field, newValue, oldValue, record) {
                    me.fireEvent('countryChanged', field, newValue, me.countryStateCombo, me.record.getBilling().first());
                }
            }
        });

        return [{
            name:'sender[street]',
            fieldLabel:me.snippets.street,
            required:true,
            allowBlank:false
        }, {
            name:'sender[additionalAddressLine1]',
            fieldLabel:me.snippets.additionalAddressLine1
        }, {
            name:'sender[additionalAddressLine2]',
            fieldLabel:me.snippets.additionalAddressLine2
        }, {
            name:'sender[zipCode]',
            fieldLabel:me.snippets.zipCode,
            required:true,
            allowBlank:false
        }, {
            name:'sender[city]',
            fieldLabel:me.snippets.city,
            required:true,
            allowBlank:false
        }, me.countryStateCombo, me.countryCombo];
    }
});
//{/block}

