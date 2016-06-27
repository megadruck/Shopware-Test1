//{namespace name=backend/customizing/view/main}

/**
 * Shopware Controller - Cache backend module
 */
//{block name="backend/customizing/controller/main"}
Ext.define('Shopware.apps.Customizing.controller.Main', {

    extend: 'Enlight.app.Controller',

    views: [
        'main.Window',
        'main.List',
        'main.Panel',
        'main.Form',
        'main.Group',
        'main.Option',
        'main.Value',
        'main.Editor'
    ],

    stores: [
        'main.List',
        'main.Type',
        'main.Option',
        'main.Group',
        'main.ArticleList',
        'main.DefaultValue'
    ],

    models: [
        'main.List',
        'main.Option',
        'main.Group',
        'main.Value',
        'main.Clone',
        'main.ArticleList',
        'main.DefaultValue'
    ],

    refs: [
        { ref: 'window', selector: 'customizing-window' },
        { ref: 'list', selector: 'customizing-list' },
        { ref: 'panel', selector: 'customizing-panel' },
        { ref: 'form', selector: 'customizing-form' },
    ],

    messages: {
        deleteEntryTitle: '{s name=form/message/delete_entry_title}Delete entry „[name]“{/s}',
        deleteEntryMessage: '{s name=form/message/delete_entry_message}Do you really want to delete this entry?{/s}',
        deleteEntrySuccess: '{s name=form/message/delete_entry_success}Entry „[name]“ was deleted.{/s}',
        deleteEntryError: '{s name=form/message/delete_entry_error}Entry „[name]“ could not be deleted.{/s}',
        saveEntryTitle: '{s name=form/message/save_entry_title}Save entry{/s}',
        saveEntrySuccess: '{s name=form/message/save_entry_success}Entry „[name]“ has been saved.{/s}',
        saveEntryError: '{s name=form/message/save_entry_error}Entry „[name]“ could not be saved.{/s}',
        moveEntrySuccess: '{s name=tree/move_success}Entry has been move.{/s}',
        moveEntryError: '{s name=tree/move_failure}Entry could not be moved.{/s}'

    },

    /**
     * Class property which holds the main application if it is created
     *
     * @default null
     * @object
     */
    mainWindow: null,

    /**
     *
     */
    init: function() {
        var me = this;
        me.mainWindow = me.getView('main.Window').create({ }).show();

        me.control({
            'customizing-list button[action=removeGroup]': {
                click: me.onRemoveListEntry
            },
            'customizing-list button[action=cloneGroup]': {
                click: me.onCloneListEntry
            },
            'customizing-list button[action=removeOption]': {
                click: me.onRemoveListEntry
            },
            'customizing-list button[action=addGroup]': {
                click: me.onAddListEntry
            },
            'customizing-list button[action=addOption]': {
                click: me.onAddListEntry
            },
            'customizing-form button[action=save]': {
                click: me.onSaveEntry
            },
            'customizing-value button[action=add]': {
                click: me.onAddValueEntry
            },
            'customizing-value': {
                delete: me.onDeleteValue
            },
            'customizing-group': {
                openArticle: me.onOpenArticle
            },
            'customizing-list': {
                select: me.onSelectListEntry,
                selectionchange: me.onChangeListEntry,
                beforeDropOption: me.onBeforeDropOption,
                itemmove: me.onEntryMove
            }
        });


        me.callParent(arguments);
    },

    onOpenArticle: function(articleId) {
        Shopware.app.Application.addSubApplication({
            name: 'Shopware.apps.Article',
            action: 'detail',
            params: {
                articleId: articleId
            }
        });
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
            data = { },
            fieldData;

        if (!table) {
            return;
        }

        Ext.each(fields, function(field) {
            fieldData = field.getModelData();
            data = Ext.apply(data, fieldData);
        });

        var record = store.add(data)[0],
            plugin = table.getPlugin('cellediting');
        //plugin.startEdit(record, table.columns[0]);
    },

    onAddListEntry: function(button) {
        var me = this, record,
            list = me.getList(),
            selection = list.getSelectionModel().getLastSelected()
        me.createOption = false;

        record = me.getModel(button.model).create();
        if (selection && button.model == 'main.Option') {
            record.set('groupId',selection.get('groupId'));
            me.createOption = true;
            if (selection.get('position')) {
                record.set('position', selection.get('position') + 1);
            }
        }

        me.loadPanel(button.model, record);
    },

    onRemoveListEntry: function(button) {
        var me = this,
            list = me.getList(),
            selection = list.getSelectionModel().getLastSelected(),
            title = new Ext.Template(me.messages.deleteEntryTitle),
            message = new Ext.Template(me.messages.deleteEntryMessage),
            data = Ext.clone(selection.data),
            panel = me.getPanel();
        title = title.applyTemplate(data);
        message = message.applyTemplate(data);

        Ext.MessageBox.confirm(title, message, function(response) {
            if (response !== 'yes') {
                return;
            }
            panel.removeAll(true);
            selection.destroy({
                callback: function(self, operation) {
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
                }
            });
        });
    },

    onCloneListEntry: function() {
        var me = this,
            list = me.getList(),
            selection = list.getSelectionModel().getLastSelected(),
            groupId = selection.get('id');

        Ext.Msg.prompt('{s name=clone/title}Clone group{/s}', '{s name=clone/group_name}Group name{/s}', function(btn, text) {
            if (btn == 'ok') {
                if (text == "") {
                    //you can not groupn with empty names try it again
                    me.onCloneListEntry();
                } else {
                    me.onSaveCloneGroup(text, groupId);
                }
            }
        });
    },

    onSaveCloneGroup: function(groupName, groupId) {
        var me = this,
            list = me.getList();

        model = Ext.create('Shopware.apps.Customizing.model.main.Clone');
        
        model.set('groupId', groupId);
        model.set('groupName', groupName);
        model.save({
            success: function() {
                list.getStore().load();
            },
            failure: function(result, operation) {
                var message = result.getProxy().getReader().rawData.message;
                Shopware.Notification.createGrowlMessage('', message, 'CustomProducts');
            }
        });
    },

    onSaveEntry: function(button) {
        var me = this,
            form = button.up('form'),
            basicForm = form.getForm(),
            record = form.getRecord(),
            store = form.store,
            storeId = store.storeId,
            message,
            title = me.messages.saveEntryTitle,
            treeOption = false;

        if (!basicForm.isValid()) {
            title = me.messages.saveEntryTitle;
            message = '{s name=form/message/save_entry_error_validation}Entry could not be saved. Please check the Form.{/s}';
            Shopware.Notification.createGrowlMessage(title, message, 'CustomProducts');
            return;
        }
        if (!record) {
            return;
        }
        basicForm.updateRecord();
        if (!record.store) {
            store.add(record);
        }

        if(record.data.type) {
            treeOption = true;
        }

        form.setLoading();
        //form.loadRecord();

        store.sync({
            success: function(records, operation) {
                message = me.messages.saveEntrySuccess;
                me.createGrowlMessage(record, title, message);
                me.onAfterSaveForm(storeId, treeOption);
            },
            failure: function(operation) {
                message = me.messages.saveEntryError;
                if (operation.proxy.reader.rawData.message) {
                    message += '<br />' + operation.proxy.reader.rawData.message;
                }
                me.createGrowlMessage(record, title, message);
                me.onAfterSaveForm(storeId, treeOption);
            }
        });
    },

    onAfterSaveForm: function(storeId, treeOption) {
        var me = this,
            form = me.getForm(),
            list = me.getList(),
            sm = list.getSelectionModel(),
            selection = sm.getLastSelected(),
            root = list.getRootNode(),
            node = selection || root;

        treeNode = (storeId == 'main.Option' && treeOption) ? node.parentNode : root;
        if (me.createOption) {
            treeNode = node;
            me.createOption = false;
        }

        list.getStore().load({
            node: treeNode,
            callback: function() {
                if (null != treeNode) {
                    treeNode.expand();
                }
            }
        });

        form.setLoading(false);
        form.destroy();
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
            panel = me.getPanel(),
            model = node.isLeaf() ? 'main.Option' : 'main.Group',
            value = node.get(node.isLeaf() ? 'optionId' : 'groupId');

        panel.setLoading(true);

        me.getStore(model).load({
            filters: [
                {
                    property: 'id',
                    value: value
                }
            ],
            callback: function(records, operation, success) {
                var record = records[0];
                if (record) {
                    me.loadPanel(model, record);
                }
            }
        });
    },

    onChangeListEntry: function(table, records) {
        var me = this,
            record = records.length ? records[0] : null,
            list = me.getList(),
            buttons = list.query('button');

        Ext.each(buttons, function(button) {
            button.hide().enable();
        });

        if (!record || !record.isLeaf()) {
            list.down('button[action=removeGroup]').show();
            list.down('button[action=cloneGroup]').show();
        } else {
            list.down('button[action=addOption]').show();
            list.down('button[action=removeOption]').show();
        }
        list.down('button[action=addGroup]').show();
        if (!record) {
            list.down('button[action=removeGroup]').disable();
            list.down('button[action=cloneGroup]').show();
        } else {
            list.down('button[action=addOption]').show();
        }

        me.getList().fireChangeListEntry();
    },

    loadPanel: function(model, record) {
        var me = this,
            form,
            win = me.getWindow(),
            store = me.getStore(model),
            panel = me.getPanel();

        form = me.getView(model).create({
            store: store,
            record: record
        });

        panel.removeAll(true);
        panel.add(form);

        win.loadTitle(model, record);

        form.loadRecord(record);
        record.setDirty();
        record.associations.each(function(association) {
            var store = record[association.name](),
                associationKey = association.associationKey,
                grid = form.down('grid[name=' + associationKey + ']');
            if (grid && store) {
                grid.reconfigure(store);
            }
        });

        panel.setLoading(false);
    },

    onBeforeDropOption: function (options) {
        var dropHandlers = options[4];

        dropHandlers.wait = true;
        Ext.MessageBox.confirm(
            '{s name=tree/move_confirmation}Note!{/s}',
            '{s name=tree/move_message}Are you sure you want to move this entry?{/s}',
            function (button) {
                if (button == 'yes') {
                    dropHandlers.processDrop();
                } else {
                    dropHandlers.cancelDrop();
                }
            }
        );
    },

    onEntryMove: function (node, oldParent, newParent, position) {
        var me = this,
            list = me.getList();

        node.data.position = position;
        node.data.groupId = !newParent.isRoot() ? newParent.data.id : null;

        var mainWindow = me.getWindow();
        mainWindow.setLoading(true);
        node.save({
            success: function () {
                mainWindow.setLoading(false);

                me.saveNewChildPositions(newParent, oldParent);

                Shopware.Notification.createGrowlMessage('', me.messages.moveEntrySuccess, 'CustomProducts');
            },
            failure: function () {
                mainWindow.setLoading(false);

                list.node = null;
                list.getStore().load();

                Shopware.Notification.createGrowlMessage('', me.messages.moveEntryError, 'CustomProducts');
            }
        });
    },

    saveNewChildPositions: function (newParent, oldParent) {
        var me = this,
            url = '{url controller=Customizing action=saveNewChildPositions}',
            childNodeIds = [];

        //save the new position for all child categories in the parent category
        newParent.eachChild(function (node) {
            childNodeIds.push(node.get('optionId'));
        });

        Ext.Ajax.request({
            url: url,
            params: {
                ids: Ext.JSON.encode(childNodeIds),
                newParentId: newParent.raw.groupId,
                oldParentId: oldParent.raw.groupId
            }
        });
    }
});
//{/block}