/**
 * Displays a grid which holds all options for a template.
 */

//{namespace name="backend/swag_custom_products/detail/option_list"}
//{block name="backend/swag_custom_products/view/detail/option_list"}
Ext.define('Shopware.apps.SwagCustomProducts.view.detail.OptionList', {
    extend: 'Shopware.grid.Panel',

    alias: 'widget.swag-custom-products-option-list',

    snippets: {
        title: '{s name="title"}Options{/s}',
        header: {
            name: '{s name="header/name"}Name{/s}',
            typeId: '{s name="header/type_id"}Type{/s}',
            position: '{s name="header/position"}Position{/s}'
        }
    },

    /**
     * Overrides the initComponent to add the title and load the option store.
     */
    initComponent: function () {
        var me = this;

        me.viewConfig = me.createDragAndDrop();
        me.title = me.snippets.title;
        me.callParent(arguments);
    },

    /**
     * @returns { Object }
     */
    configure: function () {
        var me = this;

        return {
            detailWindow: 'Shopware.apps.SwagCustomProducts.view.option.Window',
            pagingbar: false,
            rowEditing: true,
            columns: {
                name: {
                    header: me.snippets.header.name,
                    editor: me.getNameEditor()
                },
                type: {
                    header: me.snippets.header.typeId,
                    renderer: me.onRenderTypeColumn,
                    editor: false
                }
            }
        }
    },

    /**
     * This is the editor for the nameField. This contains the validator function in
     * the property validateValue.
     *
     * @returns { Shopware.apps.SwagCustomProducts.view.components.RowTextEditor }
     */
    getNameEditor: function () {
        var me = this;

        return Ext.create('Shopware.apps.SwagCustomProducts.view.components.RowTextEditor');
    },

    /**
     * @param value
     * @returns { * }
     */
    onRenderTypeColumn: function (value) {
        var typeTransLater = Ext.create('Shopware.apps.SwagCustomProducts.view.components.TypeTranslator');

        return typeTransLater.getTranslation(value);
    },

    /**
     * @overwrite
     *
     * @returns { Ext.button.Button }
     */
    createAddButton: function () {
        var me = this,
            button = me.callParent(arguments);

        button.handler = Ext.bind(me.editAddButton, me);

        return button;
    },

    editAddButton: function () {
        var me = this,
            window,
            optionRecord;

        optionRecord = Ext.create('Shopware.apps.SwagCustomProducts.model.Option', {
            position: me.getStore().getCount()
        });

        window = Ext.create('Shopware.apps.SwagCustomProducts.view.option.Window', {
            record: optionRecord,
            templateRecord: me.templateRecord,
            optionStore: me.getStore()
        }).show();
    },

    /**
     * @overwrite
     */
    createEditColumn: function () {
        var me = this,
            editButton = me.callParent(arguments);

        editButton.handler = Ext.bind(me.editButtonHandler, me);

        return editButton;
    },

    /**
     * @param view
     * @param rowIndex
     * @param colIndex
     * @param item
     * @param opts
     * @param record
     */
    editButtonHandler: function (view, rowIndex, colIndex, item, opts, record) {
        var me = this,
            window = Ext.create('Shopware.apps.SwagCustomProducts.view.option.Window', {
                record: record,
                templateRecord: me.templateRecord,
                optionStore: me.getStore()
            }).show();
    },

    /**
     * @returns
     * { { plugins: { ptype: string, dragText: string }, listeners: { drop:
     *              { fn: Shopware.apps.Advisor.view.details.Questions.onDrop,
     *                  scope: Shopware.apps.Advisor.view.details.Questions
     *           } } } }
     */
    createDragAndDrop: function () {
        var me = this;

        return {
            plugins: {
                ptype: 'gridviewdragdrop',
                dragText: 'ddt',
                dragGroup: 'optionDD',
                dropGroup: 'optionDD'
            },
            listeners: {
                drop: {
                    fn: me.onDrop,
                    scope: me
                }
            }
        };
    },

    /**
     * iterate over all questions and set the new index as order
     */
    onDrop: function () {
        var me = this;

        me.getStore().each(function (option, index) {
            option.set('position', index);
        });
    },

    /**
     * @returns { * }
     */
    createColumns: function () {
        var me = this,
            columns = me.callParent(arguments),
            ddIndicatorColumn = {
                header: '&#009868',
                width: 24,
                renderer: me.renderSortHandleColumn,
                hideable: false,
                sortable: false,
                menuDisabled: true
            };

        columns = Ext.Array.insert(columns, 0, [ddIndicatorColumn]);

        return columns;
    },

    /**
     * Renderer for ddIndicatorColumn
     *
     * @param { string } value
     * @param { * } metadata
     * @returns { string }
     */
    renderSortHandleColumn: function (value, metadata) {
        var me = this;

        metadata.tdAttr = Ext.String.format('data-qtip="[0]"', me.snippets.hintDragDrop);

        return '<div style="cursor: n-resize;">&#009868;</div>';
    },

    /**
     * Use own selection-model to fix an issue with the drag'n'drop-plugin and selection-model
     * @overwrite
     */
    createSelectionModel: function () {
        var me = this,
            selModel;

        selModel = Ext.create('Shopware.apps.SwagCustomProducts.view.components.OptionsSelectionModel', {
            listeners: {
                selectionchange: function (selModel, selection) {
                    return me.fireEvent(me.eventAlias + '-selection-changed', me, selModel, selection);
                }
            }
        });

        me.fireEvent(me.eventAlias + '-selection-model-created', me, selModel);

        return selModel;
    }
});
//{/block}
