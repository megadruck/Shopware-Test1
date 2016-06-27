//{namespace name=backend/customizing/view/charge}

/**
 * Shopware Controller - Cache backend module
 */
//{block name="backend/customizing/controller/charge"}
Ext.define('Shopware.apps.Customizing.controller.Charge', {

    extend: 'Enlight.app.Controller',

    views: [
        'charge.Panel', 'charge.List', 'charge.Form', 'charge.Value'
    ],
    stores: [ 'charge.List', 'charge.Item' ],
    models: [ 'charge.List', 'charge.Item', 'charge.Value' ],

    refs: [
        { ref: 'window', selector: 'customizing-window' },
        { ref: 'list', selector: 'customizing-charge-list' },
        { ref: 'form', selector: 'customizing-charge-form' },
        { ref: 'deleteButton', selector: 'button[action=remove]' },
        { ref: 'valueSelect', selector: 'customizing-charge-value base-element-select[name=customerGroupId]' }
    ],

    messages: {
        deleteEntryTitle: '{s name=form/message/delete_entry_title}Delete entry „[name]“{/s}',
        deleteEntryMessage: '{s name=form/message/delete_entry_message}Do you really want to delete this entry?{/s}',
        deleteEntrySuccess: '{s name=form/message/delete_entry_success}Entry „[name]“ was deleted.{/s}',
        deleteEntryError: '{s name=form/message/delete_entry_error}Entry „[name]“ could not be deleted.{/s}',
        saveEntryTitle: '{s name=form/message/save_entry_title}Save entry{/s}',
        saveEntrySuccess: '{s name=form/message/save_entry_success}Entry „[name]“ has been saved.{/s}',
        saveEntryError: '{s name=form/message/save_entry_error}Entry „[name]“ could not be saved.{/s}'
    },

    /**
     *
     */
    init: function () {
        var me = this;

        me.customerGroupStore = me.getStore('Shopware.apps.Base.store.CustomerGroup').load({
            callback: function() {
                var record = me.customerGroupStore.first();
                if (record) {
                    me.defaultCustomerGroup = record;
                }
            }
        });

        me.control({
            'customizing-charge-list': {
                select: me.onSelectListEntry,
                selectionchange: me.onChangeListEntry
            },
            'customizing-charge-list button[action=remove]': {
                click: me.onRemoveListEntry
            },
            'customizing-charge-list button[action=add]': {
                click: me.onAddListEntry
            },
            'customizing-charge-form button[action=save]': {
                click: me.onSaveEntry
            },
            'customizing-charge-value button[action=add]': {
                click: me.onAddValueEntry
            },
            'customizing-charge-value': {
                delete: me.onDeleteValue
            },
            'customizing-charge-value [isPropertyFilter]': {
                change: function(field, value) {
                    var me = this,
                        table = field.up('grid'),
                        store = table.getStore(),
                        filter = field.getModelData();
                    me.filterByModel(store, filter);
                }
            }
        });

        me.callParent(arguments);
    },

    filterByModel: function(store, filter) {
        store.clearFilter(true);
        for(var key in filter) {
            var keyFilter = new Ext.util.Filter({
                filterFn: function(item) {
                    return item.get(key) === filter[key];
                }
            });
            store.filter(keyFilter);
        }
    },

    onAddListEntry: function(button) {
        var me = this,
            record = me.getModel('charge.Item').create();
        me.loadPanel(record);
    },

    onDeleteValue: function(panel, record) {
        var me = this,
            store = panel.getStore();
        store.remove(record);
    },

    onAddValueEntry: function(button) {
        var me = this,
            table = button.up('grid'),
            fields = table.query('[isFormField]'),
            store = table.getStore(),
            data = { }, fieldData;
        if(!table) {
            return;
        }
        Ext.each(fields, function(field) {
            fieldData = field.getModelData();
            data = Ext.apply(data, fieldData);
        });
        var record = store.add(data)[0],
            plugin = table.getPlugin('cellediting');
        plugin.startEdit(record, table.columns[0]);
    },

    onSaveEntry: function(button) {
        var me = this,
            form = me.getForm(),
            basicForm = form.getForm(),
            record = form.getRecord(),
            store = me.getStore('charge.Item'),
            cancelSaving = false,
            recordFilter;


        if(!basicForm.isValid()) {
            title = me.messages.saveEntryTitle;
            message = '{s name=form/message/save_entry_error_validation}Entry could not be saved. Please check the Form.{/s}';
            Shopware.Notification.createGrowlMessage(title, message, 'CustomProducts');
            return;
        }
        if(!record) {
            return;
        }
        basicForm.updateRecord();

        // save filter and clear it
        recordFilter = record.getValues().filters.items;
        record.getValues().clearFilter();

        // Check for duplicate values
        var knownValues = [];
        record.getValues().each(function(valueRecord) {
            var check = valueRecord.get('customerGroupId') + '-' + valueRecord.get('from');
            if (knownValues.indexOf(check) != -1) {
                message = Ext.String.format('{s name=form/message/save_entry_error_from}Entry `[0]` could not be saved. Entry with quantity `[1]` duplicated.{/s}', record.get('name'), valueRecord.get('from'));
                Shopware.Notification.createGrowlMessage(title, message, 'CustomProducts');
                cancelSaving = true;
            }
            knownValues.push(check);
        });

        // we stop the saving process
        if (cancelSaving) {
            record.getValues().filter.items = recordFilter;
            store.reload();
            return;
        }

        if(!record.store) {
            store.add(record);
        }

        form.setLoading();

        var message,
            title = me.messages.saveEntryTitle;

        store.sync({
            success :function (records, operation) {
                message = me.messages.saveEntrySuccess;
                me.createGrowlMessage(record, title, message);
                selectId = me.getValueSelect().getValue();

                var lastRecord = store.getById(record.get('id'));
                if (lastRecord) {
                    lastRecord.getValues().filter.items = recordFilter;
                }

                store.reload();
                me.onAfterSaveForm(selectId);
            },
            failure:function (operation) {
                message = me.messages.saveEntryError;
                if(operation.proxy.reader.rawData.message) {
                    message += '<br />' + operation.proxy.reader.rawData.message;
                }
                me.createGrowlMessage(record, title, message);
                me.onAfterSaveForm();
            }
        });
    },

    onAfterSaveForm: function(selectId) {
        var me = this,
            form = me.getForm(),
            list = me.getList();

        list.getStore().load({
            callback: function(records, operation, success) {
                if (null != selectId) {
                    me.getValueSelect().setValue(selectId);
                }
            }
        });

        form.setLoading(false);
    },

    createGrowlMessage: function(record, title, message) {
        var me = this,
            win = me.getWindow(),
            data = Ext.clone(record.data);

        title = new Ext.Template(title).applyTemplate(data);
        message = new Ext.Template(message).applyTemplate(data);
        Shopware.Notification.createGrowlMessage(title, message, win.title);
    },

    onSelectListEntry: function(tree, node) {
        var me = this,
            form = me.getForm(),
            win = me.getWindow(),
            value = node.get('id');

        form.setLoading(true);

        me.getStore('charge.Item').load({
            callback: function(records, operation, success) {
                var selectedRecord;

                Ext.each(records,  function(record) {
                    if (record.get('id') == value) {
                        selectedRecord = record;
                    }
                });

                if(selectedRecord) {
                    me.loadPanel(selectedRecord);
                }

                if (me.defaultCustomerGroup) {
                    me.getValueSelect().setValue();
                    me.getValueSelect().setValue(me.defaultCustomerGroup.get('id'));
                }
            }
        });
    },

    onChangeListEntry: function(table, records) {
        var me = this,
            record = records.length ? records[0] : null,
            btn = me.getDeleteButton();
        if(!record) {
            btn.disable();
        } else {
            btn.enable();
        }
    },

    onRemoveListEntry: function(button) {
        var me = this,
            list = me.getList(),
            selection = list.getSelectionModel().getLastSelected(),
            title = new Ext.Template(me.messages.deleteEntryTitle),
            message = new Ext.Template(me.messages.deleteEntryMessage),
            data = Ext.clone(selection.data),
            form = me.getForm();

        title = title.applyTemplate(data);
        message = message.applyTemplate(data);

        Ext.MessageBox.confirm(title, message, function (response) {
            if (response !== 'yes') {
                return;
            }

            form.disable();
            selection.destroy({
                callback: function (self, operation) {
                    if (operation.success) {
                        message = me.messages.deleteEntrySuccess;
                    } else {
                        message = me.messages.deleteEntryError;
                        var rawData = operation.records[0].proxy.reader.rawData;
                        if (rawData.message) {
                            message += '<br />' + rawData.message;
                        }
                    }
                    me.createGrowlMessage(selection, title, message);
                    list.store.load();
                }
            });
        });
    },

    loadPanel: function(record) {
        var me = this,
            win = me.getWindow(),
            form = me.getForm();

        win.loadTitle('charge.Item', record);

        form.loadRecord(record);
        record.setDirty();
        record.associations.each(function(association) {
            var store = record[association.name](),
                associationKey = association.associationKey,
                grid = form.down('grid[name=' + associationKey + ']');
            if(grid && store) {
                grid.reconfigure(store);
                var filter = grid.down('[isPropertyFilter]');
                if(filter) {
                    filter = filter.getModelData();
                    me.filterByModel(store, filter);
                }
            }
        });

        form.setLoading(false);
        form.enable();
    }
});
//{/block}
