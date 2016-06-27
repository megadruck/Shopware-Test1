//{block name="backend/order/view/customizing/tab/store"}
Ext.define('Shopware.apps.Order.store.CustomizingDetails', {
    extend: 'Ext.data.Store',
    autoLoad: true,
    model : 'Shopware.apps.Order.model.CustomizingOption',
    groupField: 'detailId',
    /**
     * Configure the data communication
     * @object
     */
    proxy: {
        type: 'ajax',
        /**
         * Configure the url mapping
         * @object
         */
        api: {
            read: '{url controller="CustomizingDetails" action="getOrderDetails"}'
        },
        /**
         * Configure the data reader
         * @object
         */
        reader: {
            type: 'json',
            root: 'data',
            totalProperty:'total'
        }
    }
});
//{/block}