//{namespace name="backend/viison_ups/shipping"}
Ext.define("Shopware.apps.ViisonUPSShipping.controller.DefaultForm",{ override:"Shopware.apps.Shipping.controller.DefaultForm",init:function(){ this.callParent(arguments);this.addDispatchServiceProviderControllerClass("Shopware.apps.ViisonUPSShipping.controller.Tab")}});
