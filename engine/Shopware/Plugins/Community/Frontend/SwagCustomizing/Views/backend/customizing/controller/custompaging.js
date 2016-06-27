//{namespace name=backend/customizing/view/custompaging}

/**
 * Shopware Controller - Cache backend module
 */
//{block name="backend/customizing/controller/custompaging"}
Ext.define('Shopware.apps.Customizing.controller.Custompaging', {

    extend: 'Enlight.app.Controller',

    views: [
        'main.custompaging',
        'main.List'
    ],

    snippets: {
        ofPages: '{s namespace=backend/customizing/view/main name=form/paginator/ofPages}of{/s}'
    },

    refs: [
        { ref: 'paginator', selector: 'custompaging' },
        { ref: 'list', selector: 'customizing-list' }
    ],

    direction: {
        UP: 1,
        DOWN: 0
    },

    itemCount: {
        DEFAULT: 0,
        CURRENT: 0,
        MAX: 0
    },

    pagesCount: {
        DEFAULT: 1,
        CURRENT: 1,
        MAX: 0
    },


    init: function() {
        var me = this;

        me.PAGINATOR =  me.getPaginator();
        me.LISTVIEW = me.getList();

        me.BUTTONS = me.PAGINATOR.getButtons();
        me.INPUT = me.PAGINATOR.getInput();
        me.LABELS = me.PAGINATOR.getLabels();

        me.callParent(arguments);
        me.setEventHandler();
        me.INPUT.PAGE.setDisabled(false);
    },

    setEventHandler: function() {
        var me = this;
        me.PAGINATOR.on('storeLoaded', function() {
            me.storeLoaded();
        });

        me.LISTVIEW.on('onChangeListViewEntry', function () {
            me.onChangeListViewEntry();
        });
        me.setButtonHandler();
    },

    setButtonHandler: function () {
        var me = this;

        me.BUTTONS.FIRST.on('click', function() {
            me.goToPage(me.pagesCount.DEFAULT);
            me.INPUT.PAGE.setDisabled(false);
        });

        me.BUTTONS.PREV.on('click', function() {
            me.onLoadBack();
            me.INPUT.PAGE.setDisabled(false);
        });

        me.BUTTONS.NEXT.on('click', function() {
            me.onLoadForward();
            me.INPUT.PAGE.setDisabled(false);
        });

        me.BUTTONS.LAST.on('click', function () {
            me.goToPage(me.pagesCount.MAX);
            me.INPUT.PAGE.setDisabled(false);
        });

        me.BUTTONS.REFRESH.on('mouseover', function () {
            me.goToPageHelper = me.INPUT.PAGE.getValue();
        });

        me.BUTTONS.REFRESH.on('click', function () {
            me.goToPage(me.goToPageHelper);
            me.INPUT.PAGE.setDisabled(false);
        });

        me.BUTTONS.SEARCH.on('click', function(){
            me.search();
            me.INPUT.PAGE.setDisabled(false);
        });

    },

    enableAllButtons: function () {
        var me = this;
        me.BUTTONS.FIRST.setDisabled(false);
        me.BUTTONS.PREV.setDisabled(false);
        me.BUTTONS.NEXT.setDisabled(false);
        me.BUTTONS.LAST.setDisabled(false);
        me.BUTTONS.REFRESH.setDisabled(false);
        me.BUTTONS.SEARCH.setDisabled(false);
    },

    showAllButtons: function () {
        var me = this;
        me.BUTTONS.FIRST.show();
        me.BUTTONS.PREV.show();
        me.BUTTONS.NEXT.show();
        me.BUTTONS.LAST.show();
        me.BUTTONS.REFRESH.show();
        me.BUTTONS.SEARCH.show();
    },

    storeLoaded: function () {
        var me = this;
        me.itemCount.MAX = parseInt(me.PAGINATOR.getCustomStore().getProxy().reader.rawData.count);
        me.INPUT.PAGE.setValue(me.pagesCount.CURRENT);
        me.setMaxPage();
        me.setPagingLabel();

        me.INPUT.PAGE.setDisabled(false);
        me.enableAllButtons();
        if(me.pagesCount.CURRENT == me.pagesCount.MAX){
            me.BUTTONS.NEXT.setDisabled(true);
            me.BUTTONS.LAST.setDisabled(true);
        }
        if(me.pagesCount.CURRENT == me.pagesCount.DEFAULT){
            me.BUTTONS.FIRST.setDisabled(true);
            me.BUTTONS.PREV.setDisabled(true);
        }
    },

    setCurrentCountAndPage: function (direction) {
        var me = this,
            currentCountTmp = me.itemCount.CURRENT,
            currentPageTmp = me.pagesCount.CURRENT;
        switch (direction) {
            case 0:
                currentCountTmp-= 20;
                if(currentCountTmp <= me.itemCount.DEFAULT) {
                    currentCountTmp = me.itemCount.DEFAULT;
                }
                currentPageTmp-= 1;
                if(currentPageTmp < me.pagesCount.DEFAULT) {
                    currentPageTmp = me.pagesCount.DEFAULT;
                    return false;
                }
                break;
            case 1:
                currentCountTmp+= 20;
                if(currentCountTmp >= me.itemCount.MAX) {
                    currentCountTmp = me.itemCount.MAX;
                }
                currentPageTmp++;
                if(currentPageTmp > me.pagesCount.MAX) {
                    currentPageTmp = me.pagesCount.MAX;
                    return false;
                }
                break;
        }
        me.itemCount.CURRENT = currentCountTmp;
        me.pagesCount.CURRENT = currentPageTmp;
        me.INPUT.PAGE.setValue(me.pagesCount.CURRENT);
        return true;
    },

    search: function () {
        var me = this;
        me.PAGINATOR.getCustomStore().getProxy().extraParams.searchValue = me.INPUT.SEARCH.getValue();
        me.loadStore();
        me.PAGINATOR.getCustomStore().getProxy().extraParams.searchValue = '';
    },

    goToPage: function (value) {
        var me = this,
            tmpCurrentCount;
        if(value <= me.pagesCount.MAX && value >= me.pagesCount.DEFAULT) {
            tmpCurrentCount = ((value * 20) - 20);
            me.itemCount.CURRENT = tmpCurrentCount;
            me.pagesCount.CURRENT = value;
        }
        me.loadStore();
    },

    onLoadForward: function () {
        var me = this;
        me.enableAllButtons();
        if(!me.setCurrentCountAndPage(me.direction.UP)) {
            return;
        }
        me.loadStore();
    },

    onLoadBack: function () {
        var me = this;
        me.enableAllButtons();
        if(!me.setCurrentCountAndPage(me.direction.DOWN)) {
            return;
        }
        me.loadStore();
    },

    loadStore: function() {
        var me = this;
        me.PAGINATOR.getCustomStore().getProxy().extraParams.start = me.itemCount.CURRENT;
        me.PAGINATOR.getCustomStore().load();
    },

    setMaxPage: function () {
        var me = this,
            tmpCount = parseInt((me.itemCount.MAX / 20));
        if((me.itemCount.MAX % 20) > 0) {
            tmpCount++;
        }
        me.pagesCount.MAX = tmpCount;
    },

    setPagingLabel: function () {
        var me = this;
        me.LABELS.FROM.setText(me.snippets.ofPages + ' ' + me.pagesCount.MAX);
    },

    onChangeListViewEntry: function () {
        var me = this;
        me.showAllButtons();
        me.enableAllButtons();
        if(me.currentPage == me.pagesCount.MAX){
            me.BUTTONS.NEXT.setDisabled(true);
            me.BUTTONS.LAST.setDisabled(true);
        }
        if(me.currentPage == me.pagesCount.DEFAULT){
            me.BUTTONS.FIRST.setDisabled(true);
            me.BUTTONS.PREV.setDisabled(true);
        }
        me.INPUT.PAGE.setValue(me.pagesCount.CURRENT);
    }

});

//{/block}