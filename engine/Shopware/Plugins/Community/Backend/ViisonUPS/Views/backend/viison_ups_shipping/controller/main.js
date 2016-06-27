//{namespace name="backend/viison_ups_shipping/shipping"}
Ext.define("Shopware.apps.ViisonUPSShipping.controller.Main",{ override:"Shopware.apps.Shipping.controller.Main",init:function(){ this.callParent(arguments);this.addDispatchServiceProviderTabControllerClass("Shopware.apps.ViisonUPSShipping.controller.Tab")}});
