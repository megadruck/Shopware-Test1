Ext.define("Shopware.apps.ViisonShippingCommonShipping.view.edit.Panel",{ override:"Shopware.apps.Shipping.view.edit.Panel",addDispatchServiceProviderTabClass:function(e){ var i=this;i.dispatchServiceProviderTabClasses=i.dispatchServiceProviderTabClasses||[];i.dispatchServiceProviderTabClasses.push(e)},createTabPanel:function(){ var e=this;var i=this.callParent(arguments);Ext.each(e.dispatchServiceProviderTabClasses,function(a){ i.add(Ext.create(a,{ store:e.mainStore,dispatchId:e.dispatchId,record:e.editRecord}))});return i}});
