//{namespace name=backend/order/customizing/main}

//{block name="backend/customizing/view/customizing/form"}
Ext.define('Shopware.apps.Customizing.view.customizing.Form', {
    extend: 'Ext.container.Container',
    alias: 'widget.customizing-order-tab-form',
    padding: 15,
    layout: {
        type: 'vbox'
    },
    defaults: {
        autoScroll: true
    },

    initComponent: function () {
        var me = this;
        me.items = me.getFormItems();

        me.callParent(arguments);
    },
    /**
     * Iterate store and generate custom form fields
     *
     * @returns [Array]
     */
    getFormItems: function () {
        var me = this,
            prevDetailId = 0,
            nextDetailId = 1,
            items = [];

        me.store.data.each(function (item) {
            if (item.data.detailId != nextDetailId) {
                prevDetailId = item.data.detailId;
                items.push(me.createFormFieldDivider(item));
            }

            items.push(me.getCustomFormElement(item));
            nextDetailId = item.data.detailId;
        });

        return items;
    },
    /**
     * Generate form fields for every type of Order Detail
     *
     * @param item
     */
    getCustomFormElement: function (item) {
        var me = this;

        switch (item.data.typeId) {
            case 11: // typeTextField
            case 12: // typeTextArea
                return me.createFormFieldTextArea(item);
            case 13: // typeHTML
                return me.createFormFieldTextHtml(item);
            case 21: // typeDataUpload
                return me.createFormFieldDataUpload(item);
            case 22: // typeImageUpload
            case 35: // typeImagePicker
                return me.createFormFieldImagePicker(item);
            case 41: // typeDate
                return me.createFormFieldDate(item);
            case 44: // typeDateTime
                return me.createFormFieldDateTime(item);
            case 45: // typeTime
                return me.createFormFieldTime(item);
            case 51: // typeColorSelector
            case 52: // typeColorPicker
                return me.createFormFieldColor(item);
            default:
                return me.createFormFieldText(item);
        }
    },
    /*******************************************************************************************************************
     * Helper methods for generating customizing form field types
     ******************************************************************************************************************/
    createFormFieldText: function (item) {
        return {
            id: item.data.uniqueId,
            fieldLabel: item.data.name,
            xtype: 'textfield',
            value: item.data.selectedValue,
            readOnly: true,
            supportText: item.data.number
        };
    },
    createFormFieldDivider: function (item) {
        return {
            xtype: 'label',
            forId: 'myFieldId',
            text: item.data.articleNumber + ' - ' + item.data.articleName,
            margin: '10 0 10 0',
            style: 'font-weight:bold; border-bottom: 1px solid; background:#fff; width:90%; padding:3px;'
        };
    },
    createFormFieldTextArea: function (item) {
        return Ext.create('Ext.form.field.TextArea', {
            id: item.data.uniqueId,
            fieldLabel: item.data.name,
            xtype: 'textareafield',
            value: item.data.selectedValueDescription,
            grow: true,
            frame: true,
            supportText: item.data.number
        });
    },
    createFormFieldTextHtml: function (item) {
        var me = this;
        return Ext.create('Ext.container.Container', {
            layout: 'fit',
            items: [
                Ext.create('Ext.form.field.HtmlEditor', {
                    id: item.data.uniqueId,
                    fieldLabel: item.data.name,
                    value: item.data.selectedValueDescription,
                    supportText: item.data.number,
                    frame: true,
                    enableAlignments: true
                })
            ]
        });
    },
    createFormFieldImagePicker: function (item) {
        var me = this,
            val = me.getFormFieldImage(item),
            height = item.data.selectedValue.split(",").length * 158;

        return {
            id: item.data.uniqueId,
            fieldLabel: item.data.name,
            xtype: 'displayfield',
            value: val,
            readonly: true,
            supportText: item.data.number,
            height: height
        };
    },
    createFormFieldDataUpload: function (item) {
        var me = this;

        return {
            id: item.data.uniqueId,
            fieldLabel: item.data.name,
            xtype: 'displayfield',
            value: me.getFormFieldDataUpload(item),
            supportText: item.data.number
        };
    },
    createFormFieldDate: function (item) {
        return {
            id: item.data.uniqueId,
            fieldLabel: item.data.name,
            xtype: 'datefield',
            value: Ext.util.Format.date(item.data.selectedValue),
            supportText: item.data.number
        };
    },
    createFormFieldTime: function (item) {
        return {
            id: item.data.uniqueId,
            fieldLabel: item.data.name,
            xtype: 'timefield',
            value: Ext.util.Format.date(item.data.selectedValue, timeFormat),
            supportText: item.data.number
        };
    },
    createFormFieldDateTime: function (item) {
        var me = this;
        return Ext.create('Ext.container.Container', {
            layout: 'fit',
            margin: '6 0',
            items: [
                Ext.create('Ext.form.field.Date', {
                    id: item.data.uniqueId,
                    columnWidth: 0.6,
                    fieldLabel: item.data.name,
                    value: Ext.util.Format.date(item.data.selectedValue)
                }),
                Ext.create('Ext.form.field.Time', {
                    name: 'validFromTime',
                    columnWidth: 0.4,
                    labelStyle: 'font-weight: bold',
                    fieldLabel: '&nbsp;',
                    labelSeparator: '',
                    supportText: item.data.number,
                    value: Ext.util.Format.date(item.data.selectedValue, timeFormat)
                })
            ]
        });
    },
    createFormFieldColor: function (item) {
        return {
            id: item.data.uniqueId,
            fieldLabel: item.data.name,
            xtype: 'textfield',
            value: item.data.selectedValue,
            fieldStyle: 'color: ' + item.data.selectedValueDescription + '; background-color: ' + item.data.selectedValue + '; background-image: none; font-weight: bold;',
            supportText: item.data.number
        };
    },
    getFormFieldImage: function (item) {
        var url = item.data.selectedValue;
        var urlArray = item.data.selectedValue.split(",");
        var itemHtml = "";
        if (item.data.selectedValue.length == 0) {
            return '<div style="text-align: center;">{s namespace="backend/customizing/view/main" name="window/detail/no_image"}{/s}</div>';
        }

        Ext.Array.each(urlArray, function (url, index, src) {
            itemHtml += Ext.String.format(
                    '<div style="text-align: center; padding: 4px;"><a href="[0]" target="_blank"><img style="max-height: 128px; max-width: 180px;" src="[0]" title="[1]" /></a></div>',
                    "{link file=''}" + urlArray[index],
                    urlArray[index].split("/").pop()
            );
        });
        return itemHtml;

    },
    getFormFieldDataUpload: function (item) {
        // multiple uploaded files -> comma separated string of urls
        var url = item.data.selectedValue;
        var urlArray = item.data.selectedValue.split(",");
        var itemHtml = "";

        if (item.data.selectedValue.length == 0) {
            return '<div style="text-align: center;">{s namespace="backend/customizing/view/main" name="window/detail/no_data"}{/s}</div>';
        }
        Ext.Array.each(urlArray, function (url, index, src) {
            itemHtml += Ext.String.format(
                '<div><a href="[0]" target="_blank">[1]</a></div>',
                "{link file=''}" + urlArray[index],
                urlArray[index].split("/").pop()
            );
        });
        return itemHtml;
    }

});
//{/block}