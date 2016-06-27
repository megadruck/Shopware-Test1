//{namespace name="backend/viison_shipping_common_order/order"}
Ext.define("Shopware.apps.ViisonShippingCommonOrder.controller.ListDispatchActions",{ extend:"Ext.app.Controller",openTrackingWindow:function(i){ var n=this;var e=n.trackingURL+i.replace(/,/g,n.trackingNumberDelimiter);window.open(e,"_blank")}});
