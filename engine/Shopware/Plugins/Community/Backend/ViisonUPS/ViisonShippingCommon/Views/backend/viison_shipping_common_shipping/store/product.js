Ext.define("Shopware.apps.ViisonShippingCommonShipping.store.Product",{ extend:"Ext.data.Store",model:"Shopware.apps.ViisonShippingCommonShipping.model.Product",pageSize:10,autoLoad:false,constructor:function(){ this.setProxy({ type:"ajax",url:this.getProductsURL,reader:{ type:"json",root:"data",totalProperty:"total"}});this.callParent(arguments)}});
