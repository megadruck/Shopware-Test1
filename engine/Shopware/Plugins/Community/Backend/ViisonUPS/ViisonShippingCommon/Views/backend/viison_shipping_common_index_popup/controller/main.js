Ext.define("Shopware.apps.ViisonShippingCommonIndexPopup.controller.Main",{ extend:"Ext.app.Controller",mainWindow:null,init:function(){ this.mainWindow=this.getView("Window").create();this.mainWindow.setTitle(this.subApplication.localizedName);if(typeof this.subApplication.params.windowWidth!=="undefined"){ this.mainWindow.setWidth(this.subApplication.params.windowWidth)}if(typeof this.subApplication.params.html!=="undefined"){ this.mainWindow.setContentHTML(this.subApplication.params.html)}else if(typeof this.subApplication.params.contentURL!=="undefined"){ this.mainWindow.setContentURL(this.subApplication.params.contentURL)}if(typeof this.subApplication.params.saveURL!=="undefined"){ this.saveURL=this.subApplication.params.saveURL}else{ this.mainWindow.hideDialogInTheFutureCheckbox.hide()}this.control({ "viison-shipping-commmon-index-popup-window":{ closeWindow:this.onCloseWindow}});this.callParent(arguments)},onCloseWindow:function(i){ if(typeof this.saveURL!=="undefined"){ Ext.Ajax.request({ url:this.saveURL,params:{ hideDialogInTheFuture:this.mainWindow.hideDialogInTheFutureCheckbox.checked}})}this.mainWindow.close()}});
