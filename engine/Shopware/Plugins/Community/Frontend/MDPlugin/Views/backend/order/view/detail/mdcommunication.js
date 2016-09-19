/*

 * Copyright (c) MegaDruck.de Produktions- und Vertriebs GmbH
 * Joerg Frintrop
 * j.frintrop@megadruck.de
 */

//{namespace name=backend/order/main}
//{block name="backend/order/view/detail/communication" append}

    Ext.define('Shopware.apps.Order.view.detail.Mdcommunication', {
    override:'Shopware.apps.Order.view.detail.Communication',

    /**
     * Creates the container for the internal communication fields
     * @return Ext.form.FieldSet
     */


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
    },
        /**
         * Creates the container for the external communication fields
         * @return Ext.form.FieldSet
         */
        createExternalFieldSet: function() {
            var me = this;

            return Ext.create('Ext.form.FieldSet', {
                title: me.snippets.external.title,
                defaults: {
                    labelWidth: 155,
                    labelStyle: 'font-weight: 700;'
                },
                layout: 'anchor',
                minWidth:250,
                items: me.createExternalElements()
            });
        },

        /**
         * Creates the elements for the internal communication field set which is displayed on
         * top of the communication tab panel.
         * @return Array - Contains the description container, the text area for the internal comment and the save button.
         */
        createInternalElements: function() {
            var me = this;
            me.internalDescriptionContainer = null;
            me.internalTextArea = null;
            me.internalButton = null;
            me.externalDescriptionContainer = null;
            me.externalTextArea = null;
            me.externalButton = null;
            me.internalDescriptionContainer = Ext.create('Ext.container.Container', {
                style: 'color: #999; font-style: italic; margin: 0 0 15px 0;',
                html: me.snippets.internal.text
            });
            me.internalTextArea = Ext.create('Ext.form.field.TextArea', {
                name: 'internalComment',
                height: 90,
                anchor: '100%',
                cols: 4,
                allowBlank: true,
                grow: true
            });

            me.internalButton = Ext.create('Ext.button.Button', {
                style: 'margin: 8px 0;',
                cls: 'small primary',
                text: me.snippets.internal.button,
                handler: function() {
                    me.record.set('internalComment', me.internalTextArea.getValue());
                    me.fireEvent('saveInternalComment', me.record, me);
                }
            });


            me.externalTextArea = Ext.create('Ext.form.field.TextArea', {
                name: 'comment',
                fieldStyle:'background:#fee6e6;color:black;',
                height: 90,
                anchor: '100%',
                cols: 4,
                allowBlank: true,
                grow: true
            });
            me.externalButton = Ext.create('Ext.button.Button', {
                style: 'margin: 8px 0;',
                cls: 'small primary',
                text: me.snippets.external.button,
                handler: function() {
                    me.record.set('comment', me.externalTextArea.getValue());
                    me.fireEvent('saveExternalComment', me.record, me);
                }
            });


            return [me.internalDescriptionContainer, me.internalTextArea, me.internalButton,me.externalDescriptionContainer, me.externalTextArea, me.externalButton];
        },

        /**
         * Creates the elements for the external communication field set which is displayed on
         * bottom of the communication tab panel.
         * @return Array - Contains the description container, the text area for the external and the customer comment and the save button.
         */
        createExternalElements: function() {

            var me = this;
            me.customerTextArea = null;

            me.customerTextArea = Ext.create('Ext.Panel', {
                height: 90,
                css:'border:0px; background:#;color:black;',
                anchor: '100%',
                cols: 4,
                name: 'customerComment',
                allowBlank: true,
                grow: true
            });

            me.internalDescriptionContainer = Ext.create('Ext.container.Container', {
                style: 'color: #999; font-style: italic; margin: 0 0 15px 0;',
                html: me.snippets.internal.text
            });



            return [me.customerTextArea];

        },

    });

//{/block}