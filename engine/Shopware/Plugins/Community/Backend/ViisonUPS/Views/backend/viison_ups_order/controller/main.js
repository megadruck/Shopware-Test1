//{namespace name="backend/viison_ups_order"}
Ext.define("Shopware.apps.ViisonUPSOrder.controller.Main",{ override:"Shopware.apps.Order.controller.Main",init:function(){ this.callParent(arguments);this.addDispatchServiceProviderTabControllerClass("Shopware.apps.ViisonUPSOrder.controller.Tab")}});
