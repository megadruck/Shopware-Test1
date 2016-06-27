//{namespace name=backend/customizing/view/main}
//{block name="backend/order/view/detail/position" append}
Ext.define('Shopware.apps.Order.view.detail.Window.Position', {

    override: 'Shopware.apps.Order.view.detail.Position',

    /**
     * Add new icon for custom product in action column of position grid
     *
     * @returns [array]
     */
    getColumns: function() {
        var me = this,
            columns = me.callParent(arguments),
            actionColumnIndex = -1;

        // Find index of last actionColumn in columns array
        Ext.Array.each(columns, function(item, index) {
            if (item.xtype == 'actioncolumn') {
                actionColumnIndex = index;
            }
        });

        // Add custom icon, if an actionColumn was found
        if (actionColumnIndex > -1) {
            var actionColumn = columns[actionColumnIndex];

            // Add custom action icon
            actionColumn.items.push(me.newCustomIcon());

            // "Reinitialize" action column
            columns[actionColumnIndex] = Ext.create('Ext.grid.column.Action', {
                width: actionColumn.width + 20,
                items: actionColumn.items
            });
        }

        return columns;
    },

    newCustomIcon: function() {
        var newAction = {
            iconCls: 'sprite-ui-combo-box-edit',
            tooltip: '{s name=position/column/customizing}This product have custom options{/s}',
            getClass: function(value, metadata, record) {
                var customizing = record.getAttributesStore.getAt(0).get('swagCustomizing');
                if (!customizing)  {
                    return 'x-hidden';
                }
            }
        };

        return newAction;
    }
});
//{/block}