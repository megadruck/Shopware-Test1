//{namespace name="backend/viison_ups_config/config"}
Ext.define("Shopware.apps.ViisonUPSConfig.view.Panel",{ extend:"Shopware.apps.ViisonShippingCommonConfig.view.Panel",xtype:"viisonupsconfig-panel",snippets:{ items:{ senderName:{ label:'{s name=panel/items/sender_name/label}{/s}',help:'{s name=panel/items/sender_name/help}{/s}'},streetName:{ label:'{s name=panel/items/street_name/label}{/s}',help:'{s name=panel/items/street_name/help}{/s}'},streetNumber:{ label:'{s name=panel/items/street_number/label}{/s}',help:'{s name=panel/items/street_number/help}{/s}'},zipCode:{ label:'{s name=panel/items/zip_code/label}{/s}',help:'{s name=panel/items/zip_code/help}{/s}'},city:{ label:'{s name=panel/items/city/label}{/s}',help:'{s name=panel/items/city/help}{/s}'},country:{ label:'{s name=panel/items/country/label}{/s}',help:'{s name=panel/items/country/help}{/s}'},contactPerson:{ label:'{s name=panel/items/contact_person/label}{/s}',help:'{s name=panel/items/contact_person/help}{/s}'},email:{ label:'{s name=panel/items/email/label}{/s}',help:'{s name=panel/items/email/help}{/s}'},phoneNumber:{ label:'{s name=panel/items/phone_number/label}{/s}',help:'{s name=panel/items/phone_number/help}{/s}'},userId:{ label:'{s name=panel/items/user_id/label}{/s}',help:'{s name=panel/items/user_id/help}{/s}'},password:{ label:'{s name=panel/items/password/label}{/s}',help:'{s name=panel/items/password/help}{/s}'},accessKey:{ label:'{s name=panel/items/access_key/label}{/s}',help:'{s name=panel/items/access_key/help}{/s}'},accountNumber:{ label:'{s name=panel/items/account_number/label}{/s}',help:'{s name=panel/items/account_number/help}{/s}'},negotiatedRates:{ label:'{s name=panel/items/negotiated_rates/label}{/s}',help:'{s name=panel/items/negotiated_rates/help}{/s}'},defaultPackagingType:{ label:'{s name=panel/items/default_packaging_type/label}{/s}',help:'{s name=panel/items/default_packaging_type/help}{/s}'},returnProduct:{ label:'{s name=panel/items/return_product/label}{/s}',help:'{s name=panel/items/return_product/help}{/s}'},pdfSize:{ label:'{s name=panel/items/pdf_size/label}{/s}',help:'{s name=panel/items/pdf_size/help}{/s}'},weightSeparator:{ label:'{s name=panel/items/weight_separator/label}{/s}'},defaultShipmentWeight:{ label:'{s name=panel/items/default_shipment_weight/label}{/s}',help:'{s name=panel/items/default_shipment_weight/help}{/s}'},calculateWeight:{ label:'{s name=panel/items/calculate_weight/label}{/s}',help:'{s name=panel/items/calculate_weight/help}{/s}'},fillerSurcharge:{ label:'{s name=panel/items/filler_surcharge/label}{/s}',help:'{s name=panel/items/filler_surcharge/help}{/s}'},packagingSurcharge:{ label:'{s name=panel/items/packaging_surcharge/label}{/s}',help:'{s name=panel/items/packaging_surcharge/help}{/s}'},notificationEmailSeparator:{ label:'{s name=panel/items/notification_email_description/label}{/s}'},sendDispatchNotification:{ label:'{s name=panel/items/send_dispatch_notification/label}{/s}',help:'{s name=panel/items/send_dispatch_notification/help}{/s}'},dispatchNotificationText:{ label:'{s name=panel/items/dispatch_notification_text/label}{/s}',help:'{s name=panel/items/dispatch_notification_text/help}{/s}'},sendDeliveryNotification:{ label:'{s name=panel/items/send_delivery_notification/label}{/s}',help:'{s name=panel/items/send_delivery_notification/help}{/s}'}},groupValidationFailed:'{s name=panel/group_validation_failed}{/s}',groupDependencyValidationFailed:'{s name=panel/group_dependency_validation_failed}{/s}',disallowBlankIfCheckedValidationFailed:'{s name=panel/disallow_blank_if_checked_validation_failed}{/s}'},groupDependencies:{ base:["international-shipment"]},initComponent:function(){ var e=this;e.packagingTypeStore=Ext.create("Shopware.apps.ViisonUPSShipping.store.PackagingType").load(function(){ if(e.packagingTypeStore.find("id",0)===-1){ e.packagingTypeStore.add({ id:null,name:"-",code:""})}});e.productStore=Ext.create("Shopware.apps.ViisonUPSShipping.store.Product").load();e.countriesStore=Ext.create("Shopware.apps.ViisonShippingCommonFreeFormLabels.store.Country").load();e.pdfSizeData=[["A5"],["A4"]];e.callParent(arguments)},createItems:function(e){ var i=this;var l=[{ xtype:"config-element-text",name:"senderName",fieldLabel:i.snippets.items.senderName.label,helpText:i.snippets.items.senderName.help,maxLength:30,group:"base",validator:i.groupValidator},{ xtype:"config-element-text",name:"streetName",fieldLabel:i.snippets.items.streetName.label,helpText:i.snippets.items.streetName.help,maxLength:30,group:"base",validator:i.groupValidator},{ xtype:"config-element-text",name:"streetNumber",fieldLabel:i.snippets.items.streetNumber.label,helpText:i.snippets.items.streetNumber.help,maxLength:30,group:"base",validator:i.groupValidator},{ xtype:"config-element-text",name:"zipCode",fieldLabel:i.snippets.items.zipCode.label,helpText:i.snippets.items.zipCode.help,maxLength:30,group:"base",validator:i.groupValidator,maskRe:/[\d]/},{ xtype:"config-element-text",name:"city",fieldLabel:i.snippets.items.city.label,helpText:i.snippets.items.city.help,maxLength:30,group:"base",validator:i.groupValidator},{ xtype:"combobox",name:"countryId",fieldLabel:i.snippets.items.country.label,helpText:i.snippets.items.country.help,valueField:"id",displayField:"name",queryMode:"local",mode:"local",editable:false,store:i.countriesStore,group:"base",validator:i.groupValidator},{ xtype:"config-element-text",name:"contactPerson",fieldLabel:i.snippets.items.contactPerson.label,helpText:i.snippets.items.contactPerson.help,maxLength:30,group:"base",validator:i.groupValidator},{ xtype:"config-element-text",name:"email",fieldLabel:i.snippets.items.email.label,helpText:i.snippets.items.email.help,maxLength:50,vtype:"email",group:"base",validator:i.groupValidator},{ xtype:"config-element-text",name:"phoneNumber",fieldLabel:i.snippets.items.phoneNumber.label,helpText:i.snippets.items.phoneNumber.help,maxLength:30,group:"base",validator:i.groupValidator,maskRe:/[\d\+]/},{ xtype:"config-element-text",name:"userId",fieldLabel:i.snippets.items.userId.label,helpText:i.snippets.items.userId.help,maxLength:255,group:"base",validator:i.groupValidator},{ xtype:"config-element-text",name:"password",fieldLabel:i.snippets.items.password.label,helpText:i.snippets.items.password.help,maxLength:255,inputType:"password",group:"base",validator:i.groupValidator},{ xtype:"config-element-text",name:"accessKey",fieldLabel:i.snippets.items.accessKey.label,helpText:i.snippets.items.accessKey.help,group:"base",validator:i.groupValidator},{ xtype:"config-element-text",name:"accountNumber",fieldLabel:i.snippets.items.accountNumber.label,helpText:i.snippets.items.accountNumber.help,group:"base",validator:i.groupValidator},{ xtype:"config-element-checkbox",name:"negotiatedRates",fieldLabel:i.snippets.items.negotiatedRates.label,helpText:i.snippets.items.negotiatedRates.help,checked:false},{ xtype:"combobox",name:"defaultPackagingType",fieldLabel:i.snippets.items.defaultPackagingType.label,helpText:i.snippets.items.defaultPackagingType.help,valueField:"id",displayField:"name",queryMode:"local",mode:"local",editable:false,emptyText:"-",store:i.packagingTypeStore},{ xtype:"combobox",name:"returnProduct",fieldLabel:i.snippets.items.returnProduct.label,helpText:i.snippets.items.returnProduct.help,valueField:"id",displayField:"name",queryMode:"local",mode:"local",editable:false,store:i.productStore},{ xtype:"combobox",name:"pdfSize",fieldLabel:i.snippets.items.pdfSize.label,helpText:i.snippets.items.pdfSize.help,valueField:"text",displayField:"text",queryMode:"local",mode:"local",editable:false,store:Ext.create("Ext.data.SimpleStore",{ fields:["text"],data:i.pdfSizeData})},{ xtype:"container",margin:"15 0 10 0",items:[{ xtype:"label",text:i.snippets.items.weightSeparator.label}]},{ xtype:"x-custom-field-weight",name:"defaultShipmentWeight",fieldLabel:i.snippets.items.defaultShipmentWeight.label,helpText:i.snippets.items.defaultShipmentWeight.help,defaultSubmitValue:0,allowBlank:false},{ xtype:"config-element-checkbox",name:"calculateWeight",fieldLabel:i.snippets.items.calculateWeight.label,helpText:i.snippets.items.calculateWeight.help,checked:false},{ xtype:"x-custom-field-weight",name:"fillerSurcharge",fieldLabel:i.snippets.items.fillerSurcharge.label,helpText:i.snippets.items.fillerSurcharge.help,defaultSubmitValue:0,allowBlank:false},{ xtype:"x-custom-field-weight",name:"packagingSurcharge",fieldLabel:i.snippets.items.packagingSurcharge.label,helpText:i.snippets.items.packagingSurcharge.help,defaultSubmitValue:0,allowBlank:false},{ xtype:"container",margin:"15 0 10 0",items:[{ xtype:"label",text:i.snippets.items.notificationEmailSeparator.label}]},{ xtype:"config-element-checkbox",name:"sendDispatchNotification",fieldLabel:i.snippets.items.sendDispatchNotification.label,helpText:i.snippets.items.sendDispatchNotification.help,checked:false},{ xtype:"config-element-textarea",name:"dispatchNotificationText",fieldLabel:i.snippets.items.dispatchNotificationText.label,helpText:i.snippets.items.dispatchNotificationText.help,allowBlank:true},{ xtype:"config-element-checkbox",name:"sendDeliveryNotification",fieldLabel:i.snippets.items.sendDeliveryNotification.label,helpText:i.snippets.items.sendDeliveryNotification.help,checked:false}];return l}});
