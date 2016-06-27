//{namespace name=backend/customizing/view/main}

//{block name="backend/customizing/view/main/list"}
Ext.define('Shopware.apps.Customizing.view.main.List', {
    extend: 'Ext.tree.Panel',

    alias: 'widget.customizing-list',

    snippets: {
        goToPage: '{s name=form/paginator/gotToPage}go to page{/s}'
    },

    viewConfig: {
        plugins: {
            ptype: 'treeviewdragdrop'
        }
    },

    rootVisible: false,

    root: {
        expanded: true,
        id: 0
    },

    layout: 'fit',

    initComponent: function() {
        var me = this;

        me.customPager = Ext.create('Shopware.apps.Customizing.view.main.custompaging');
        me.initStore = me.customPager.setCustomStore('Shopware.apps.Customizing.store.main.List');

        var store = Ext.create('Shopware.apps.Customizing.store.main.List', { });
        Ext.applyIf(me, {
            store: me.initStore,
            columns: me.getColumns(),
            dockedItems: [
                me.getToolbar(),
                me.customPager
            ]
        });

        me.callParent(arguments);

        me.view.on('beforedrop', function() {
            me.fireEvent('beforeDropOption', arguments);
        });

    },

    /**
     * Creates the grid columns
     *
     * @return Array grid columns
     */
    getColumns: function() {
        return [{
            xtype: 'treecolumn',
            header: '{s name=list/columns/name}Name{/s}',
            dataIndex: 'name',
            flex: 4
        }, {
            header: '{s name=list/columns/assignment}Assignment{/s}',
            dataIndex: 'assignment',
            flex: 2
        }];
    },

    getToolbar: function() {
        var me = this;
        return {
            xtype: 'toolbar',
            dock: 'bottom',
            border: false,
            cls: 'shopware-toolbar',
            items: me.getToolbarItems()
        };
    },

    getToolbarItems: function () {
        var me = this;
        return [{
            text: '{s name=list/options/add_group}Add group{/s}',
            cls: 'secondary small',
            action: 'addGroup',
            model: 'main.Group'
        }, {
            text: '{s name=list/options/add_option}Add option{/s}',
            cls: 'secondary small',
            action: 'addOption',
            hidden: true,
            model: 'main.Option'
        }, {
            text: '{s name=list/options/remove_group}Remove group{/s}',
            cls: 'secondary small',
            disabled: true,
            action: 'removeGroup',
            model: 'main.Group'
        }, {
            text: '{s name=list/options/clone_group}Clone group{/s}',
            cls: 'secondary small',
            disabled: true,
            action: 'cloneGroup',
            model: 'main.Group'
        }, {
            text: '{s name=list/options/remove_option}Remove option{/s}',
            cls: 'secondary small',
            hidden: true,
            action: 'removeOption',
            model: 'main.Option'
        }];
    },
    
    fireChangeListEntry: function () {
        var me = this;
        me.fireEvent('onChangeListViewEntry', me);
    }
});
//{/block}