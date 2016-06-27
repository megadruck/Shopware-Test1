Ext.define("Shopware.apps.ViisonShippingCommonOrder.view.WeightField",{ extend:"Ext.form.field.Text",alias:"widget.x-custom-field-weight",maskRe:new RegExp("[\\d\\.,]"),defaultSubmitValue:null,initComponent:function(){ this.on("change",function(e,t,i){ //{literal}
			var matcher = new RegExp('^\\d+((\\.|,)\\d{0,3})?$', 'i');
//{/literal}
if(!matcher.test(t)&&t.length>0){ e.setValue(i)}});this.callParent(arguments)},getSubmitValue:function(){ var e=typeof this.value=="string"?parseFloat(this.value.replace(",",".")):this.value;return e||this.defaultSubmitValue},setValue:function(e){ if(e!=null){ e=e.toString().replace(".",",")}this.callParent(arguments)}});
