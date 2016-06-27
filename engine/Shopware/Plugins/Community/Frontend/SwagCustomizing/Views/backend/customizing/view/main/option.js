//{namespace name=backend/customizing/view/main}

//{block name="backend/customizing/view/main/option"}
Ext.define('Shopware.apps.Customizing.view.main.Option', {

    extend: 'Shopware.apps.Customizing.view.main.Form',
    alias: 'widget.customizing-option',

    initComponent:function () {
        var me = this;
        if(me.record && me.record.get('id')) {
            me.type = me.record.get('type');
            me.multiValues = false;
            me.fieldType = 'text';
            me.plugins = me.getPlugins();

            switch(me.type) {
                case 'checkbox': me.multiValues = true; break;
                case 'color_field': me.fieldType = 'color'; break;
                case 'date':me.fieldType = 'date'; break;
                case 'date_time': me.fieldType = 'datetime'; break;
                case 'time': me.fieldType = 'time'; break;
                case 'text_area': me.fieldType = 'textarea'; break;
                case 'text_field': break;
                case 'text_html': me.fieldType = 'html'; break;
                case 'color_select': me.fieldType = 'color'; me.multiValues = true; break;
                case 'image_select': me.fieldType = 'media'; me.multiValues = true; break;
                case 'multiple': me.multiValues = true; break;
                case 'radio': me.multiValues = true; break;
                case 'select': me.multiValues = true; break;
                case 'upload_file': me.fieldType = 'upload'; break;
                case 'upload_image': me.fieldType = 'upload'; break;
                default:
                    break;
            }
        }
        me.callParent(arguments);
    },

    getPlugins: function() {
        return [{
            pluginId: 'translation',
            ptype: 'translation',
            translationType: 'customizing-option'
        }];
    },

    /**
     * @return Array
     */
    getItems: function() {
        var me = this;
        var items = [{
            fieldLabel: '{s name=option/fields/name}Name{/s}',
            allowBlank: false,
            name: 'name',
            translatable: true
        }, {
            fieldLabel: '{s name=option/fields/type}Type{/s}',
            allowBlank: false,
            xtype: 'base-element-select',
            store: 'main.Type',
            name: 'typeId',
            listConfig: me.getTypeListConfig(),
            disabled: !!me.fieldType
        }, {
            fieldLabel: '{s name=option/fields/required}Required{/s}',
            xtype: 'base-element-boolean',
            name: 'required'
        }, {
            fieldLabel: '{s name=option/fields/active}Active{/s}',
            xtype: 'base-element-boolean',
            name: 'active'
        }, {
            fieldLabel: '{s name=option/fields/position}Position{/s}',
            xtype: 'base-element-number',
            name: 'position'
        }, {
            fieldLabel: '{s name=option/fields/number}Number{/s}',
            name: 'number',
            maxLength: 30,
            maxLengthText: '{s name=form/fields/number_error}Max. 30 characters allowed{/s}',
            supportText: '{s name=option/fields/numberSupport}{/s}'
        }];

        if(me.multiValues) {
            var defaultStore = Ext.create('Shopware.apps.Customizing.store.main.DefaultValue');
            defaultStore.filter({ property: 'filter', value: me.record.get('id') });

            var defaultValue = me.record.get('defaultValue'),
                          id = defaultStore.findRecord('value', defaultValue);

            me.defaultValue = Ext.create('Ext.form.ComboBox', {
                labelWidth: 155,
                name: 'defaultValue',
                fieldLabel: '{s name=option/fields/defaultValue}Default value{/s}',
                store: defaultStore,
                displayField: 'value',
                valueField: 'id',
                allowBlank: true,
                queryMode: 'local',
                forceSelection: true,
                value: id,
                emptyText: '{s name=scale/customer_group_empty_text}Please select...{/s}',
                listeners: {
                    change: function(combo, value) {
                        if (!value || value === 0) {
                            combo.reset();
                            me.record.set('defaultValue', 0);
                        }
                    }
                }
            });

            items.push(me.defaultValue);
        }
        if(me.fieldType != 'upload' && me.fieldType != 'time' && me.fieldType != "html") {
            items.push({
                fieldLabel: '{s name=option/fields/emptyText}Empty text{/s}',
                name: 'emptyText',
                translatable: true,
                xtype: 'base-element-text',
                helpText: '{s name=option/fields/emptyTextHelp}A default value being shown when the field is empty. E.g. \'Please select\'{/s}'
            });
        }

        if(me.fieldType == 'upload') {
            items.push(
            {
                fieldLabel: '{s name=option/fields/maxUploads}max uploads{/s}',
                    xtype: 'base-element-number',
                name: 'maxUploads',
                value: 3
            });
        }

        if(!me.multiValues && (me.fieldType == 'text' || me.fieldType == 'textarea')) {
            items.push({
                fieldLabel: '{s name=option/fields/maxValue}Max length/value{/s}',
                xtype: 'base-element-number',
                name: 'maxValue'
            });
        }

        if(me.multiValues) {
            items.push({
                xtype: 'customizing-value',
                fieldType: me.fieldType,
                translationKey: me.translationKey
            });
        }
        return items;
    },

    getTypeListConfig: function() {
        return {
            tpl: Ext.create('Ext.XTemplate',
                //{literal}
                '<ul><tpl for=".">',
                '<tpl if="xindex == 1 || this.getGroupStr(parent[xindex - 2]) != this.getGroupStr(values)">',
                '<li class="x-combo-list-group" style="padding-left: 6px"><b>{parentName}</b></li>',
                '</tpl>',
                '<li role="option" class="x-boundlist-item" style="padding-left: 12px">{name}</li>',
                '</tpl>',
                '</ul>', {
                    getGroupStr: function (values) {
                        return values.parentName;
                    }
                }
                //{/literal}
            )
        }
    }
});
//{/block}