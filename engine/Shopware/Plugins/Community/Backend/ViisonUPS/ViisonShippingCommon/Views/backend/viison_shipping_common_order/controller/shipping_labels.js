//{namespace name="backend/viison_shipping_common_order/shipping_labels"}
Ext.define("Shopware.apps.ViisonShippingCommonOrder.controller.ShippingLabels",{ extend:"Ext.app.Controller",snippets:{ notifications:{ createLabelSuccess:{ title:'{s name=shipping_labels/notifications/create_label_success/title}{/s}',message:'{s name=shipping_labels/notifications/create_label_success/message}{/s}'},createLabelError:{ title:'{s name=shipping_labels/notifications/create_label_error/title}{/s}',message:'{s name=shipping_labels/notifications/create_label_error/message}{/s}'},destroyLabelSuccess:{ title:'{s name=shipping_labels/notifications/destroy_label_success/title}{/s}',message:'{s name=shipping_labels/notifications/destroy_label_success/message}{/s}'},destroyLabelError:{ title:'{s name=shipping_labels/notifications/destroy_label_error/title}{/s}',message:'{s name=shipping_labels/notifications/destroy_label_error/message}{/s}'},destroyLabelErrorSticky:{ title:'{s name=shipping_labels/notifications/destroy_label_error_sticky/title}{/s}',buttonTitle:'{s name=shipping_labels/notifications/destroy_label_error_sticky/button_title}{/s}',message:'{s name=shipping_labels/notifications/destroy_label_error_sticky/message}{/s}',messageAddition:'{s name=shipping_labels/notifications/destroy_label_error_sticky/message_addition}{/s}'},invalidFormError:{ title:'{s name=shipping_labels/notifications/invalid_form_error/title}{/s}',message:'{s name=shipping_labels/notifications/invalid_form_error/message}{/s}'},limitReachedInfo:{ title:'{s name=shipping_labels/notifications/limit_reached_info/title}{/s}',message:'{s name=shipping_labels/notifications/limit_reached_info/message}{/s}'},sendReturnLabelEmailSuccess:{ title:'{s name=shipping_labels/notifications/send_return_label_email_success/title}{/s}',message:'{s name=shipping_labels/notifications/send_return_label_email_success/message}{/s}'},sendReturnLabelEmailError:{ title:'{s name=shipping_labels/notifications/send_return_label_email_error/title}{/s}',message:'{s name=shipping_labels/notifications/send_return_label_email_error/message}{/s}'}}},constructor:function(){ this.refs=(this.refs||[]).concat([{ ref:"labelConfirmWindow",selector:"order-viison-shipping-common-label-confirm-window"},{ ref:"labelConfirmPanel",selector:"order-viison-shipping-common-label-confirm-panel"},{ ref:"mailWindow",selector:"order-viison-shipping-common-mail-window"}]);this.callParent(arguments)},init:function(){ var e=this;e.control(e.labelPanelSelector,{ showLabelConfirmWindow:e.onShowLabelConfirmWindow,showReturnLabelConfirmWindow:e.onShowReturnLabelConfirmWindow});e.control(e.labelPanelSelector+" order-viison-shipping-common-label-list",{ trackShippingLabel:e.onTrackShippingLabel,destroyShippingLabel:e.onDestroyShippingLabel});e.control(e.dispatchServiceProviderLabelConfirmPanel,{ createShippingLabel:e.onCreateShippingLabel,createReturnLabel:e.addReturnLabel,destroyLabelConfirmWindow:e.onDestroyLabelConfirmWindow});e.control('order-viison-shipping-common-mail[cls="'+e.dispatchServiceProviderCSSClass+'"] button[action=cancel]',{ click:e.onCancelSendReturnLabelMail});e.control('order-viison-shipping-common-mail[cls="'+e.dispatchServiceProviderCSSClass+'"]',{ sendReturnLabelMail:e.onSendReturnLabelMail})},onShowLabelConfirmWindow:function(e,i,a){ var t=this;t.updateOrderWeight(i);t.updatePackageDimensions(i);if(t.getLabelConfirmWindow()!==undefined){ t.getLabelConfirmWindow().destroy()}if(a.count()>=t.maxNumberOfLabels){ var s=t.snippets.notifications.limitReachedInfo;var o=s.message.replace("#LIMIT#",t.maxNumberOfLabels);Shopware.Notification.createGrowlMessage(s.title,o,t.customSnippets.notifications.growlMessage);return}Ext.create("Shopware.apps.ViisonShippingCommonOrder.view.detail.LabelConfirmWindow",{ record:i,store:a,maxNumberOfLabels:t.maxNumberOfLabels,dispatchServiceProviderTab:e,productStore:Ext.create(t.productStoreClass).load(),labelConfirmPanelClass:t.labelConfirmPanelClass,subApp:t.subApplication}).show()},onShowReturnLabelConfirmWindow:function(e,i,a){ var t=this;if(t.getLabelConfirmWindow()!==undefined){ t.getLabelConfirmWindow().destroy()}Ext.create("Shopware.apps.ViisonShippingCommonOrder.view.detail.LabelConfirmWindow",{ record:i,store:a,maxNumberOfLabels:t.maxNumberOfLabels,dispatchServiceProviderTab:e,productStore:Ext.create(t.productStoreClass).load(),labelConfirmPanelClass:t.labelConfirmPanelClass,subApp:t.subApplication,isReturn:true}).show()},addReturnLabel:function(e,i,a,t,s,o,r){ var n=this;if(!n.verifyForm(r)){ return}var l=n.getShippingLabelParams(i?i.getId():null,a,t,s,o);l.returnShipment=true;var c=Ext.create(n.shippingLabelClass,l);r.loadMask.show();c.save({ callback:function(e,i){ if(i.success!==true||e.get("creationSuccess")!==true){ r.loadMask.hide();var a=n.snippets.notifications.createLabelError;var t=e.get("message")&&e.get("message").length>0?e.get("message"):a.message;Shopware.Notification.createGrowlMessage(a.title,t,n.customSnippets.notifications.growlMessage);return}else{ r.destroy()}var a=n.snippets.notifications.createLabelSuccess;Shopware.Notification.createGrowlMessage(a.title,a.message,n.customSnippets.notifications.growlMessage);n.refreshLabelLists();if(n.getMailWindow()!==undefined){ n.getMailWindow().destroy()}Ext.create("Shopware.apps.ViisonShippingCommonOrder.view.detail.MailWindow",{ trackingCode:c.get("trackingCode"),mail:e.getProxy().getReader().rawData.data.mail,cls:n.dispatchServiceProviderCSSClass,subApp:n.subApplication}).show()}})},onCreateShippingLabel:function(e,i,a,t,s,o,r){ var n=this;Ext.override(Ext.data.proxy.Ajax,{ timeout:6e4});if(!n.verifyForm(r)){ return}if(r!==undefined){ r.loadMask.show()}var l={ closeWindow:true,newTrackingCodes:[]};var c=n.after(s.numberOfLabels,function(){ n.afterLabelCreationFinished(e,i,a,r,l)});for(var d=0;d<s.numberOfLabels;d++){ var S=n.getShippingLabelParams(i?i.getId():null,a,t,s,o);n.createLabel(S,l,c)}},verifyForm:function(e){ var i=this;if(!e.down("panel").getForm().isValid()){ var a=i.snippets.notifications.invalidFormError;Shopware.Notification.createGrowlMessage(a.title,a.message,i.customSnippets.notifications.growlMessage);return false}return true},onDestroyLabelConfirmWindow:function(e){ if(e!==undefined){ e.destroy()}},onDestroyShippingLabel:function(e,i,a,t,s){ var o=this;e.loadMask.show();a.destroy({ params:{ onlyLocal:s},callback:function(r,n){ e.loadMask.hide();var l=false;if(n.response&&n.response.responseText&&n.response.responseText.length>0){ var c=Ext.JSON.decode(n.response.responseText);if(c&&c.status&&c.status.success){ l=c.status.success}}if(l===true){ o.refreshLabelLists();var d=a.get("trackingCode");var S=new RegExp(d,"g");if(i){ //{literal}
							var newTrackingCodeString = record.data.trackingCode.replace(re, '').replace(/(^,)|(,$)/, '').replace(/,,/, ',');
						//{/literal}
o.updateOrderTrackingCodes(e,i,newTrackingCodeString)}var p=o.snippets.notifications.destroyLabelSuccess;Shopware.Notification.createGrowlMessage(p.title,p.message,o.customSnippets.notifications.growlMessage)}else if(s===true){ var p=o.snippets.notifications.destroyLabelError;var m=c&&c.status&&c.status.message&&c.status.message.length>0?c.status.message:p.message;Shopware.Notification.createGrowlMessage(p.title,m,o.customSnippets.notifications.growlMessage)}else{ var p=o.snippets.notifications.destroyLabelErrorSticky;var m=c&&c.status&&c.status.message&&c.status.message.length>0?c.status.message:p.message;m+="<br><br>"+p.messageAddition;var f=Shopware.Notification.createStickyGrowlMessage({ title:p.title,text:m,width:450,btnDetail:{ text:p.buttonTitle,scope:o,link:"-"}});f.getComponent(1).getComponent(0).setHandler(function(){ o.onDestroyShippingLabel(e,i,a,t,true);Shopware.Notification.closeGrowlMessage(f,Shopware.Notification)})}}})},onCancelSendReturnLabelMail:function(e,i,a){ if(this.getMailWindow()){ this.getMailWindow().destroy()}},onSendReturnLabelMail:function(e){ var i=this;if(!e.getForm().isValid()){ return}i.getMailWindow().loadMask.show();Ext.Ajax.request({ url:i.sendMailURL,method:"POST",jsonData:{ trackingCode:e.trackingCode,mail:e.getForm().getFieldValues()},scope:this,callback:function(e,a,t){ i.getMailWindow().loadMask.hide();a=false;if(t&&t.responseText&&t.responseText.length>0){ var s=Ext.JSON.decode(t.responseText);if(s&&s.success){ a=s.success}}if(a){ this.onCancelSendReturnLabelMail();var o=this.snippets.notifications.sendReturnLabelEmailSuccess;Shopware.Notification.createGrowlMessage(o.title,o.message,this.snippets.notifications.growlMessage)}else{ var o=this.snippets.notifications.sendReturnLabelEmailError;Shopware.Notification.createGrowlMessage(o.title,o.message,this.snippets.notifications.growlMessage)}}})},onTrackShippingLabel:function(e){ this.getController(this.listDispatchActionsControllerClass).openTrackingWindow(e.get("trackingCode"))},updateRequiredFields:function(e){ if(!e){ return}var i=this;Ext.Ajax.request({ url:i.getRequiredFieldsURL,params:{ orderId:e.getId()},success:function(a){ var t=Ext.JSON.decode(a.responseText);e[i.dispatchServiceProviderPrefix]=e[i.dispatchServiceProviderPrefix]||{ };e[i.dispatchServiceProviderPrefix].fieldConstraints=e[i.dispatchServiceProviderPrefix].fieldConstraints||{ };e[i.dispatchServiceProviderPrefix].fieldConstraints.packageDimensionsRequired=t.packageDimensionsRequired;if(i.getLabelConfirmWindow()&&i.getLabelConfirmWindow().dataPanel){ i.getLabelConfirmWindow().dataPanel.updateConstraints()}}})},afterLabelCreationFinished:function(e,i,a,t,s){ var o=this;if(t!==undefined){ if(s.closeWindow){ t.destroy()}else{ t.loadMask.hide()}}if(i){ var r=i.data.trackingCode;if(r.length>0&&s.newTrackingCodes.length>0){ r+=","}r+=s.newTrackingCodes.join(",");o.updateOrderTrackingCodes(e,i,r);if(a.saveInOrder){ var n=e.up("order-detail-window");o.updateOrderShippingAddress(n,i,{ salutation:a.salutation,firstName:a.firstName,lastName:a.lastName,company:a.company,department:a.department,street:a.street,streetNumber:a.streetNumber,zipCode:a.zipCode,city:a.city,countryId:a.country,phone:a.phone,email:a.email})}Ext.each(Ext.ComponentQuery.query("order-list"),function(e){ e.store.reload()})}Ext.each(s.newTrackingCodes,function(e){ Ext.Ajax.request({ url:o.downloadDocumentsURL,method:"GET",params:{ trackingCode:e}})});Ext.override(Ext.data.proxy.Ajax,{ timeout:3e4})},after:function(e,i){ return function(){ if(--e<1){ return i.apply(this,arguments)}}},getShippingLabelParams:function(e,i,a,t,s){ var o={ orderId:e,detailsStateId:i.state,detailsCountryId:i.country};Ext.iterate(i,function(e,i){ o["details"+e.charAt(0).toUpperCase()+e.slice(1)]=i});if(a){ Ext.apply(o,{ packagingLength:a.packagingLength,packagingWidth:a.packagingWidth,packagingHeight:a.packagingHeight,packagingWeight:a.weight})}if(t){ Ext.apply(o,{ settingsProduct:t.product,settingsCashOnDelivery:t.cashOnDelivery,settingsCreateExportDocument:t.createExportDocument,settingsSaveInOrder:i.saveInOrder})}if(s){ Ext.iterate(s,function(e,i){ o["extraSettings"+e.charAt(0).toUpperCase()+e.slice(1)]=i})}return o},updatePackageDimensions:function(e){ if(!e){ return}var i=this;if(!("getDefaultPackageDimensionsURL"in i)){ return}Ext.Ajax.request({ url:i.getDefaultPackageDimensionsURL,params:{ orderId:e.getId()},success:function(a){ var t=Ext.JSON.decode(a.responseText);if(t.success){ e[i.dispatchServiceProviderPrefix]=e[i.dispatchServiceProviderPrefix]||{ };Ext.apply(e[i.dispatchServiceProviderPrefix],{ defaultPackageLength:t.defaultPackageLength,defaultPackageWidth:t.defaultPackageWidth,defaultPackageHeight:t.defaultPackageHeight})}if(i.getLabelConfirmWindow()&&i.getLabelConfirmWindow().dataPanel){ i.getLabelConfirmWindow().dataPanel.setValues()}}})},updateOrderWeight:function(e){ if(!e){ return}var i=this;Ext.Ajax.request({ url:i.calculateShippingWeightURL,params:{ orderId:e.getId()},success:function(a){ var t=Ext.JSON.decode(a.responseText);if(t.success){ e[i.dispatchServiceProviderPrefix]=e[i.dispatchServiceProviderPrefix]||{ };e[i.dispatchServiceProviderPrefix].calculatedShipmentWeight={ weight:t.weight,complete:!t.orderHasItemsWithoutWeight,isDefault:t.isDefault}}else{ delete e[i.dispatchServiceProviderPrefix].calculatedShipmentWeight}if(i.getLabelConfirmWindow()&&i.getLabelConfirmWindow().dataPanel){ i.getLabelConfirmWindow().dataPanel.setValues()}}})},updateStreet:function(e){ if(!e){ return}var i=this;Ext.Ajax.request({ url:'{url controller="ViisonShippingCommonOrder" action="splitAddress"}',params:{ orderId:e.getId()},success:function(a){ var t=Ext.JSON.decode(a.responseText);if(t.success){ e[i.dispatchServiceProviderPrefix]=e[i.dispatchServiceProviderPrefix]||{ };e[i.dispatchServiceProviderPrefix].splittedAddress=t.address}if(i.getLabelConfirmWindow()&&i.getLabelConfirmWindow().dataPanel){ i.getLabelConfirmWindow().dataPanel.setValues()}}})},refreshLabelLists:function(){ var e=this;var i=Ext.ComponentQuery.query(e.dispatchServiceProviderPanelClass);Ext.each(i,function(e){ e.shippingLabelStore.load({ callback:function(i,a,t){ var s=e.down("order-viison-shipping-common-label-list");s.getView().refresh()}})})},createLabel:function(e,i,a){ var t=this;var s=false;var o=Ext.create(t.shippingLabelClass,e);o.save({ callback:function(e,o){ if(o.success===true&&e.get("creationSuccess")===true){ t.refreshLabelLists();var r=t.snippets.notifications.createLabelSuccess;Shopware.Notification.createGrowlMessage(r.title,r.message,t.customSnippets.notifications.growlMessage);i.newTrackingCodes.push(e.get("trackingCode"));s=true}else{ t.handleLabelCreationError(e.get("message"),e.get("errorCode"));i.closeWindow=false}a.call(t)}});return s},handleLabelCreationError:function(e,i){ var a=this;var t=a.snippets.notifications.createLabelError;var e=e&&e.length>0?e:t.message;Shopware.Notification.createGrowlMessage(t.title,e,a.customSnippets.notifications.growlMessage)}});
