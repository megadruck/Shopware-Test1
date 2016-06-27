//{namespace name="backend/viison_shipping_common_order/order"}
Ext.define("Shopware.apps.ViisonShippingCommonOrder.controller.List",{ override:"Shopware.apps.Order.controller.List",init:function(){ this.callParent(arguments);this.subApplication.getController("Shopware.apps.ViisonShippingCommonOrder.controller.ListDispatch")}});
