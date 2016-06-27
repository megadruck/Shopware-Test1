//{namespace name=backend/customizing/view/custompaging}

//{block name="backend/customizing/view/main/custompaging"}

Ext.define('Shopware.apps.Customizing.view.main.custompaging', {

    extend: 'Ext.toolbar.Paging',

    dock: 'bottom',

    alias: 'widget.custompaging',

    snippets: {
        search: '{s namespace=backend/customizing/view/main name=form/paginator/search}search{/s}'
    },

    initComponent: function() {
        var me = this;
        me.callParent(arguments);

        me.add(me.createSpacer());
        me.add(me.createSearchField());
        me.add(me.createSearchButton());


        me.BUTTONS = {
            FIRST: me.child('#first'),
            PREV: me.child('#prev'),
            NEXT: me.child('#next'),
            LAST: me.child('#last'),
            REFRESH: me.child('#refresh'),
            SEARCH: me.child('#searchButton')
        };

        me.INPUT = {
            PAGE: me.child('#inputItem'),
            SEARCH: me.child('#searchTextField')
        };

        me.LABELS = {
            FROM: me.child('#afterTextItem')
        };

    },
    
    getButtons: function () {
        var me = this;
        return me.BUTTONS;
    },

    getLabels: function () {
        var me = this;
        return me.LABELS;
    },

    getInput: function () {
        var me = this;
        return me.INPUT;
    },

    /********************************
     *          IMPORTANT
     ********************************
     * The store requires the following settings:
     *
     *      extraParams: {
     *          start: 0,
     *          limit: XXX,
     *          seachValue: ''
     *      }
     *
     ********************************/
    setCustomStore: function(storeName) {
        var me = this;

        me.customStore = Ext.create(storeName, {
            listeners: {
                load: function () {
                    me.fireEvent('storeLoaded', me);
                }
            }
        });

        return me.customStore;
    },
    
    getCustomStore: function () {
        var me = this;
        return me.customStore;
    },

    createSpacer: function () {
        return Ext.create('Ext.Toolbar.Fill', {
            name: 'name'
        });
    },
    
    createSearchField: function () {
        var me = this;
        return Ext.create('Ext.form.field.Text', {
            name: 'searchTextField',
            itemId: 'searchTextField',
            emptyText: me.snippets.search
        });
    }, 
    
    createSearchButton: function () {
        return Ext.create('Ext.Button', {
            name: 'searchButton',
            itemId: 'searchButton',
            text: '',
            iconCls: 'sprite-magnifier'
        });
    }

});

//{/block}