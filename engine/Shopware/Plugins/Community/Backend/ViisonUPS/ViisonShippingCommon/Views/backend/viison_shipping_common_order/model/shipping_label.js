Ext.define("Shopware.apps.ViisonShippingCommonOrder.model.ShippingLabel",{ extend:"Ext.data.Model",fields:[{ name:"id",type:"int"},{ name:"orderId",type:"int",useNull:true},{ name:"trackingCode",type:"string"},{ name:"url",type:"string"},{ name:"created",type:"string"},{ name:"exportDocumentUrl",type:"string"},{ name:"customerAddress",type:"string"},{ name:"returnShipment",type:"boolean",defaultValue:false},{ name:"useDetails",type:"boolean",defaultValue:true},{ name:"detailsSalutation",type:"string"},{ name:"detailsFirstName",type:"string"},{ name:"detailsLastName",type:"string"},{ name:"detailsStreet",type:"string"},{ name:"detailsStreetNumber",type:"string"},{ name:"detailsAdditionalAddressLine",type:"string"},{ name:"detailsZipCode",type:"string"},{ name:"detailsCity",type:"string"},{ name:"detailsStateId",type:"int"},{ name:"detailsCountryId",type:"int"},{ name:"detailsCompany",type:"string"},{ name:"detailsDepartment",type:"string"},{ name:"detailsPhone",type:"string"},{ name:"detailsEmail",type:"string"},{ name:"packagingLength",type:"int",useNull:true},{ name:"packagingWidth",type:"int",useNull:true},{ name:"packagingHeight",type:"int",useNull:true},{ name:"packagingWeight",type:"string"},{ name:"settingsProduct",type:"int"},{ name:"settingsCreateExportDocument",type:"boolean",defaultValue:false},{ name:"settingsSaveInOrder",type:"boolean",defaultValue:false},{ name:"settingsCashOnDelivery",type:"boolean",defaultValue:false},{ name:"extraSettingsAmount",type:"float",defaultValue:0},{ name:"extraSettingsCurrency",type:"string",defaultValue:""},{ name:"creationSuccess",type:"boolean",defaultValue:true},{ name:"message",type:"string"},{ name:"errorCode",type:"int"}],constructor:function(){ this.setProxy({ type:"ajax",api:{ create:this.createURL,destroy:this.destroyURL},reader:{ type:"json",root:"data",totalProperty:"total"}});this.callParent(arguments)}});
