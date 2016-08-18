/**
 * Shopware 4
 * Copyright © shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */
/**
 * Shopware SwagImportExport Plugin
 *
 * @category Shopware
 * @package Shopware\Plugins\SwagImportExport
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
//{namespace name=backend/swag_import_export/view/main}
//{block name="backend/swag_import_export/view/manager/export"}
Ext.define('Shopware.apps.SwagImportExport.view.manager.Operation', {
    extend: 'Ext.container.Container',
    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias: 'widget.swag-import-export-manager-operation',
    title: '{s name=swag_import_export/manager/operation/title}Previous operations{/s}',
    layout: 'fit',
    style: {
        background: '#fff'
    },
    snippets: {
        file: '{s name=swag_import_export/column/file}File{/s}',
        fileSize: '{s name=swag_import_export/column/file_size}File size{/s}',
        type: '{s name=swag_import_export/column/type}Type{/s}',
        profile: '{s name=swag_import_export/column/profile}Profile{/s}',
        records: '{s name=swag_import_export/column/records}Records{/s}',
        totalCount: '{s name=swag_import_export/column/totalCount}Total count{/s}',
        user: '{s name=swag_import_export/column/user}User{/s}',
        date: '{s name=swag_import_export/column/date}Date{/s}',
        status: '{s name=swag_import_export/column/status}Status{/s}',
        resume: '{s name=swag_import_export/action/resume}Resume operation{/s}',
        download: '{s name=swag_import_export/action/download}Download file{/s}',
        deleteFile: '{s name=swag_import_export/action/delete}Delete file{/s}',
        deleteOperations: '{s name=swag_import_export/action/delete_operation}Delete selected operation{/s}'
    },
    
    bodyPadding: 10,
    autoScroll: true,
    initComponent: function() {
        var me = this;
        me.items = [me.createGrid()];
//        me.dockedItems = [me.getPagingBar()];
        me.callParent(arguments);
    },
    listeners: {
        activate: function(tab, opt){
            var me = this;
            me.sessionStore.reload();
        }
    },
    /**
     * Registers events in the event bus for firing events when needed
     */
    registerEvents: function() {
        this.addEvents('deleteSession','deleteMultipleSessions');
    },
    createGrid: function() {
        var me = this;

        me.sessionGrid =  Ext.create('Ext.grid.Panel', {
            title: me.snippets.panelTitle,
            id: 'operation-grid',
            store: me.sessionStore,
            multiSelect: true,
            viewConfig: {
                enableTextSelection: true
            },
            selModel: me.getGridSelModel(),
            tbar: me.getToolbar(),
            columns: me.getColumns(),
            dockedItems: [me.getPagingBar()],
            features: [
                {
                    ftype: 'grouping'
                }
            ]
        });

        return me.sessionGrid;
    },

    /**
     * Creates the grid selection model for checkboxes
     *
     * @return [Ext.selection.CheckboxModel] grid selection model
     */
    getGridSelModel: function () {
        var me = this;

        return Ext.create('Ext.selection.CheckboxModel', {
            listeners:{
                // Unlocks the delete button if the user has checked at least one checkbox
                selectionchange: function (sm, selections) {
                    me.deleteButton.setDisabled(selections.length === 0);
                }
            }
        });
    },

    /**
     * Creates the grid columns
     *
     * @return [array] grid columns
     */
    getColumns: function() {
        var me = this;

        return [
            {
                header: me.snippets.file,
                dataIndex: 'fileName',
                flex: 5,
                renderer: function(value, view, record) {
                    return '<a href={url action="downloadFile"}' + '/type/' + record.get('type') + '/fileName/' + value + ' >' + value + '</a>';
                }
            },
            {
                header: me.snippets.type,
                dataIndex: 'type',
                flex: 1
            },
            {
                header: me.snippets.profile,
                dataIndex: 'profileName',
                flex: 2
            },
            {
                header: me.snippets.records,
                dataIndex: 'position',
                flex: 1
            },
            {
                header: me.snippets.totalCount,
                dataIndex: 'totalCount',
                flex: 1
            },
            {
                header: me.snippets.fileSize,
                dataIndex: 'fileSize',
                flex: 2
            },
            {
                header: me.snippets.user,
                dataIndex: 'username',
                flex: 2
            },
            {
                xtype : 'datecolumn',
                header: me.snippets.date,
                format: 'Y-m-d H:i:s',
                dataIndex: 'createdAt',
                flex: 2
            },
            {
                header: me.snippets.status,
                dataIndex: 'state',
                flex: 1
            },
            {
                /**
                 * Special column type which provides
                 * clickable icons in each row
                 */
                xtype: 'actioncolumn',
                width: 60,
                items: [
                    me.createResumeButton(),
//                    me.createDownloadFileButton(),
                    me.createDeleteSessionButton()
                ],
                renderer: function(value, appearance, record){
                    
                    //todo: display resume on records with closed status ?                    
                    if (record.get('totalCount') == record.get('position')) {
                        
                    }                     
                }
            }
        ];
    },
        /**
     * Creates the grid toolbar
     *
     * @return [Ext.toolbar.Toolbar] grid toolbar
     */
    getToolbar: function() {
        var me = this, buttons = [];

        //creates the delete button to remove all selected esds in one request.
        me.deleteButton = Ext.create('Ext.button.Button', {
            iconCls:'sprite-minus-circle-frame',
            text: me.snippets.deleteOperations,
            disabled: true,
            handler: function() {
                var selectionModel = me.sessionGrid.getSelectionModel(),
                    records = selectionModel.getSelection();

                if (records.length > 0) {
                    me.fireEvent('deleteMultipleSessions', records, me.sessionStore);
                }
            }
        });

        buttons.push(me.deleteButton);

        return Ext.create('Ext.toolbar.Toolbar', {
            ui: 'shopware-ui',
            items: buttons
        });
    },
    /**
     * Creates the paging toolbar for session grid and store paging. 
     * The paging toolbar uses the same store as the Grid
     *
     * @return Ext.toolbar.Paging The paging toolbar for the session grid
     */
    getPagingBar:function () {
        var me = this;

        var pagingBar = Ext.create('Ext.toolbar.Paging', {
            store: me.sessionStore,
            dock: 'bottom',
            displayInfo: true
        });

        return pagingBar;
    },
    createResumeButton: function() {
        var me = this;
        
        me.resumeButton = {
            iconCls: 'sprite-arrow-circle-315',
            action: 'resume',
            tooltip: me.snippets.resume,
            /**
             * Add button handler to fire the deleteSession event which is handled
             * in the list controller.
             */
            handler:function (view, rowIndex, colIndex, item) {
                var store = view.getStore(),
                    record = store.getAt(rowIndex);
                if(record.get('type') === 'export'){
                    me.fireEvent('resumeExport', record, me.sessionStore);                    
                }
                
                if(record.get('type') === 'import'){
                    me.fireEvent('resumeImport', record, me.sessionStore);                    
                }
                
            }
        };
        
        return me.resumeButton;
    },
    createDownloadFileButton: function() {
        var me = this;
        return {
            iconCls: 'sprite-inbox-download',
            action: 'download',
            tooltip: me.snippets.download,
            /**
             * Add button handler to fire the deleteSession event which is handled
             * in the list controller.
             */
            handler:function (view, rowIndex, colIndex, item) {
                var store = view.getStore(),
                    record = store.getAt(rowIndex);

                me.fireEvent('downloadFile', record);
            }
        };
    },
    createDeleteSessionButton: function() {
        var me = this;
        return {
            iconCls:'sprite-minus-circle-frame',
            action:'deleteFile',
            tooltip: me.snippets.deleteFile,
            /**
             * Add button handler to fire the deleteSession event which is handled
             * in the list controller.
             */
            handler:function (view, rowIndex, colIndex, item) {
                var store = view.getStore(),
                    record = store.getAt(rowIndex);

                me.fireEvent('deleteSession', record, store);
            }
        };
    }

});
//{/block}