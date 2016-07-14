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

//{namespace name=backend/swag_import_export/view/profile}
//{block name="backend/swag_import_export/view/profile/profile"}
Ext.define('Shopware.apps.SwagImportExport.view.profile.Profile', {
	extend: 'Ext.container.Container',
	
	/**
	 * List of short aliases for class names. Most useful for defining xtypes for widgets.
	 * @string
	 */
	alias: 'widget.swag-import-export-profile-profile',
    
    snippets: {
        title: '{s name=swag_import_export/profile/profile/title}Profile{/s}',
        toolbar: {
            emptyText: '{s name=swag_import_export/profile/profile/toolbar/empty_text}Select Profile...{/s}',
            createProfile: '{s name=swag_import_export/profile/profile/toolbar/create_profile}Create Own Profile{/s}',
            deleteProfile: '{s name=swag_import_export/profile/profile/toolbar/delete_profile}Delete Selected Profile{/s}',
            duplicateProfile: '{s name=swag_import_export/profile/profile/toolbar/duplicate_profile}Duplicate Selected Profile{/s}',
            renameProfile: '{s name=swag_import_export/profile/profile/toolbar/rename_profile}Rename Selected Profile{/s}',
            showConversions: '{s name=swag_import_export/profile/profile/toolbar/show_conversions}Show Conversions{/s}'
        }
    },
    
    layout: 'border',
	
	style: {
		background: '#fff'
	},
		
	autoScroll: false,
	
	loadNew: function(profileId) {
		var me = this;
        
        me.profileId = profileId;
        if (profileId !== null) {
            me.toolbar.items.get('deleteSelectedProfile').setDisabled(false);
            me.toolbar.items.get('duplicateSelectedProfile').setDisabled(false);
            me.toolbar.items.get('renameSelectedProfile').setDisabled(false);
            me.toolbar.items.get('conversionsMenu').setDisabled(false);
            me.treeStore.getProxy().setExtraParam('profileId', profileId);
            me.treeStore.load({ params: { profileId: profileId } });
            me.columnStore.load({ params: { profileId: profileId } });
            me.sectionStore.load({ params: { profileId: profileId } });
            me.formPanel.hideFields();
            me.treePanel.getView().setDisabled(false);
        } else {
            me.toolbar.items.get('deleteSelectedProfile').setDisabled(true);
            me.toolbar.items.get('duplicateSelectedProfile').setDisabled(true);
            me.toolbar.items.get('renameSelectedProfile').setDisabled(true);
            me.toolbar.items.get('conversionsMenu').setDisabled(true);
            me.treePanel.getView().setDisabled(true);
            me.formPanel.hideFields();
            me.treePanel.collapseAll();
        }
	},
	
	initComponent: function() {
		var me = this;
		
		me.treeStore = Ext.create('Shopware.apps.SwagImportExport.store.Profile');		
		me.selectedNodeId = 0;
        
        me.columnStore = Ext.create('Shopware.apps.SwagImportExport.store.Column');
        me.parentKeyStore = Ext.create('Shopware.apps.SwagImportExport.store.ParentKey');
        me.sectionStore = Ext.create('Shopware.apps.SwagImportExport.store.Section');

        me.title = me.snippets.title;
        me.items = [me.createToolbar(), me.createTreeItem(), me.createFormPanel()];
		
		me.treePanel.getView().setDisabled(true);
		me.callParent(arguments);
	},

    createToolbar: function() {
        var me = this;
        
        me.toolbar = Ext.create('Ext.toolbar.Toolbar', {
            region: 'north',
            items: [{
                    xtype: 'combobox',
                    itemId: 'profilesCombo',
                    allowBlank: false,
                    store: me.profilesStore,
                    labelStyle: 'font-weight: 700; text-align: left;',
                    width: 150,
                    valueField: 'id',
                    displayField: 'name',
                    editable: false,
                    name: 'profile',
                    emptyText: me.snippets.toolbar.emptyText,
                    listeners: {
                        change: function(combo, value) {
                            me.loadNew(value);
                        }
                    }
                },
                '-',
                {
                    text: me.snippets.toolbar.createProfile,
                    handler: function() {
                        me.fireEvent('createOwnProfile', me.profilesStore, me.toolbar.child('#profilesCombo'));
                    }
                }, {
                    itemId: 'duplicateSelectedProfile',
                    text: me.snippets.toolbar.duplicateProfile,
                    disabled: true,
                    handler: function() {
                        me.fireEvent('duplicateSelectedProfile', me.toolbar.items.get('profilesCombo'), me.profilesStore, me.profileId);
                    }
                }, {
                    itemId: 'renameSelectedProfile',
                    text: me.snippets.toolbar.renameProfile,
                    disabled: true,
                    handler: function() {
                        me.fireEvent('renameSelectedProfile', me.profilesStore, me.profileId, me.toolbar.items.get('profilesCombo'));
                    }
                }, {
                    itemId: 'deleteSelectedProfile',
                    text: me.snippets.toolbar.deleteProfile,
                    disabled: true,
                    handler: function() {
                        me.fireEvent('deleteSelectedProfile', me.toolbar.items.get('profilesCombo'), me.profilesStore, me.profileId);
                    }
                }, {
                    itemId: 'conversionsMenu',
                    text: me.snippets.toolbar.showConversions,
                    disabled: true,
                    handler: function() {
                        me.fireEvent('showMappings', me.profileId);
                    }
                }
            ],
            style: {
                borderRight: '1px solid #A4B5C0',
                borderLeft: '1px solid #A4B5C0',
                borderTop: '1px solid #A4B5C0',
                borderBottom: '1px solid #A4B5C0'
            }
        });
        
        return me.toolbar;
    },
	
	createTreeItem: function() {
		var me = this;
		
		me.treePanel = Ext.create('Ext.tree.Panel', {
			region: 'west',
			store: me.treeStore,
            viewConfig: {
				plugins: {
					ptype: 'customtreeviewdragdrop'
				},
                listeners: {
                    drop: function(node, data, overModel, dropPosition, eOpts) {
                        if (dropPosition === 'append') {
                            if (overModel.get('type') !== 'iteration') {
                                overModel.set('type', '');
                                overModel.set('iconCls', '');
                            }
                        }
                        
                        me.treeStore.sync({
                            success: function() {
                                // fix selection
                                me.treePanel.getSelectionModel().deselectAll(true);
                                me.treePanel.expand();
                                me.treePanel.getSelectionModel().select(me.treeStore.getById(data.records[0].get('id')));
                            }
                        });
                    }
                }
            },
            rootVisible: false,
            title: 'Profile',
            width: 310,
            useArrows: true,
            expandChildren: true,
			dockedItems: [{
					itemId: 'toolbar',
					xtype: 'toolbar',
					style: {
						borderRight: '1px solid #A4B5C0',
						borderLeft: '1px solid #A4B5C0',
						borderTop: '1px solid #A4B5C0'
					},
					items: [{
							itemId: 'createIteration',
							text: '{s name=newIterationNode}New iteration node{/s}',
                            disabled: true,
							handler: function() {
                                me.fireEvent('addNewIteration', me.treePanel, me.treeStore, me.selectedNodeId);
							}
						}, {
							itemId: 'createChild',
							text: '{s name=newNode}New node{/s}',
                            disabled: true,
							handler: function() {
                                me.fireEvent('addNewNode', me.treePanel, me.treeStore, me.selectedNodeId);
							}
						}, {
							itemId: 'createAttribute',
							text: '{s name=newAttribute}New attribute{/s}',
                            disabled: true,
							handler: function() {
                                me.fireEvent('addNewAttribute', me.treePanel, me.treeStore, me.selectedNodeId);
							}
						}, {
							itemId: 'deleteSelected',
							text: '{s name=delete}delete{/s}',
                            disabled: true,
							handler: function() {
								me.fireEvent('deleteNode', me.treeStore, me.selectedNodeId, me.treePanel.getSelectionModel());
							}
						}]
				}],
			listeners: {
				select: {
                    fn: function(view, record, item, index, event) {
                        me.selectedNodeId = record.getId();
                        me.formPanel.fillForm();

                        var toolbar = this.dockedItems.get('toolbar');

                        if (record.get('type') === 'attribute') {
                            toolbar.items.get('createIteration').setDisabled(true);
                            toolbar.items.get('createAttribute').setDisabled(true);
                            toolbar.items.get('createChild').setDisabled(true);
                            toolbar.items.get('deleteSelected').setDisabled(false);
                        } else if (record.get('type') === 'leaf') {
                            toolbar.items.get('createIteration').setDisabled(false);
                            toolbar.items.get('createAttribute').setDisabled(false);
                            toolbar.items.get('createChild').setDisabled(false);
                            toolbar.items.get('deleteSelected').setDisabled(false);
                        } else if (record.get('type') === 'iteration') {
                            toolbar.items.get('createIteration').setDisabled(false);
                            toolbar.items.get('createAttribute').setDisabled(false);
                            toolbar.items.get('createChild').setDisabled(false);
                            toolbar.items.get('deleteSelected').setDisabled(false);
                        } else {
                            if (record.get('inIteration') === true) {
                                toolbar.items.get('createAttribute').setDisabled(false);
                            } else {
                                toolbar.items.get('createAttribute').setDisabled(true);
                            }
                            toolbar.items.get('createIteration').setDisabled(false);
                            toolbar.items.get('createChild').setDisabled(false);
                            if (record.getId() === 'root') {
                                toolbar.items.get('deleteSelected').setDisabled(true);
                            } else {
                                toolbar.items.get('deleteSelected').setDisabled(false);
                            }
                        }
                    }
                }
            }
		});
        
		return me.treePanel;
	},
	
	createFormPanel: function() {
		var me = this;
		
		me.formPanel = Ext.create('Ext.form.Panel', {
			region: 'center',
			bodyPadding: 12,
			defaultType: 'textfield',
			border: false,
			bodyStyle: {
				border: '0 !important'
			},
			hideFields: function() {
				this.child('#nodeName').hide();
				this.child('#swColumn').hide();
			},
			fillForm: function() {
				var node = me.treeStore.getById(me.selectedNodeId);
				this.child('#nodeName').show();
				this.child('#nodeName').setValue(node.get('text'));
				this.child('#swColumn').setValue(node.get('swColumn'));

				if (node.get('type') === 'attribute') {
                    this.child('#swColumn').getStore().load({ params: { profileId: me.profileId, adapter: node.get('adapter') } });
					this.child('#swColumn').show();
                    this.child('#adapter').hide();
                    this.child('#parentKey').hide();
				} else if (node.get('type') === 'leaf') {
                    this.child('#swColumn').getStore().load({ params: { profileId: me.profileId, adapter: node.get('adapter') } });
					this.child('#swColumn').show();
                    this.child('#adapter').hide();
                    this.child('#parentKey').hide();
				} else if (node.get('type') === 'iteration') {
					this.child('#swColumn').hide();
                    this.child('#adapter').show();
                    this.child('#adapter').setValue(node.get('adapter'));
                    
                    // check if it's the first iteration node
                    var parentNode = node.parentNode;
                    var isFirst = true;
                    while (parentNode.get('id') !== 'root') {
                        if (parentNode.get('type') === 'iteration') {
                            isFirst = false;
                        }
                        parentNode = parentNode.parentNode;
                    }
                    // enable the parentKey only if the node is not the first one
                    if (!isFirst) {
                        this.child('#parentKey').getStore().load({ params: { profileId: me.profileId, adapter: node.get('adapter') } });
                        this.child('#parentKey').setValue(node.get('parentKey'));
                        this.child('#parentKey').show();
                    } else {
                        this.child('#parentKey').hide();
                        this.child('#parentKey').setValue('');
                    }
                } else {
					this.child('#swColumn').hide();
                    this.child('#adapter').hide();
                    this.child('#parentKey').hide();
                    this.child('#defaultValue').hide();
				}
			},
			items: [{
					itemId: 'nodeName',
					fieldLabel: '{s name=nodeName}Node name{/s}',
					hidden: true,
					width: 400,
					labelWidth: 150,
					name: 'nodeName',
					allowBlank: false
				}, {
                    itemId: 'defaultValue',
                    fieldLabel: '{s name=defaultValue}Default value{/s}',
                    hidden: true,
                    width: 400,
                    labelWidth: 150,
                    name: 'defaultValue',
                    allowBlank: true
                }, {
					itemId: 'swColumn',
					fieldLabel: '{s name=shopwareColumn}Shopware column{/s}',
					hidden: true,
                    xtype: 'combobox',
                    editable: false,
                    emptyText: 'Select Column',
                    queryMode: 'local',
                    store: me.columnStore,
                    valueField: 'id',
                    displayField: 'name',
                    width: 400,
                    labelWidth: 150,
                    name: 'swColumn',
					allowBlank: false,
                    listeners: {
                        change: function(field, newValue) {
                            var defaultValue = me.treeStore.getById(me.selectedNodeId).get('defaultValue');
                            me.fireEvent('changeColumn', me.columnStore, newValue, defaultValue);
                        }
                    }
				}, {
					itemId: 'adapter',
					fieldLabel: '{s name=adapter}Adapter{/s}',
					hidden: true,
                    xtype: 'combobox',
                    editable: false,
                    emptyText: '{s name=selectColumn}Select column{/s}',
                    queryMode: 'local',
                    store: me.sectionStore,
                    valueField: 'id',
                    displayField: 'name',
                    width: 400,
                    labelWidth: 150,
                    name: 'adapter',
					allowBlank: false,
                    listeners: {
                        change: function(field, newValue, oldValue, eOpts) {
                            me.formPanel.child('#parentKey').getStore().load({ params: { profileId: me.profileId, adapter: newValue } });
                        }
                    }
				}, {
					itemId: 'parentKey',
					fieldLabel: '{s name=parentKey}Parent key{/s}',
					hidden: true,
                    xtype: 'combobox',
                    editable: false,
                    emptyText: '{s name=selectColumn}Select column{/s}',
                    store: me.parentKeyStore,
                    valueField: 'id',
                    displayField: 'name',
                    width: 400,
                    labelWidth: 150,
                    name: 'parentKey',
					allowBlank: false
				}],
			dockedItems: [{
					xtype: 'toolbar',
					dock: 'bottom',
					ui: 'shopware-ui',
					cls: 'shopware-toolbar',
					style: {
						backgroundColor: '#F0F2F4'
					},
					items: ['->', {
							text: '{s name=save}Save{/s}',
							cls: 'primary',
							action: 'swag-import-export-manager-profile-save',
							handler: function() {
								me.fireEvent('saveNode', me.treePanel, me.treeStore, me.selectedNodeId, me.formPanel.child('#nodeName').getValue(), me.formPanel.child('#swColumn').getValue(), me.formPanel.child('#defaultValue').getValue(), me.formPanel.child('#adapter').getValue(), me.formPanel.child('#parentKey').getValue());
							}
						}]
				}]
		});

		return me.formPanel;
	}
});
//{/block}
