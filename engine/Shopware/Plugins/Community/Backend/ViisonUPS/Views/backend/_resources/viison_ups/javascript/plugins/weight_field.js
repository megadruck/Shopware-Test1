Ext.define("Ext.form.field.Weight",{ extend:"Ext.form.field.Text",alias:"widget.x-custom-field-weight",maskRe:/[\d\.,]/,defaultSubmitValue:null,initComponent:function(){ this.on("change",function(t,e,i){ //{literal}
			var matcher = /^\d+((\.|,)\d{0,3})?$/i;
//{/literal}
if(!matcher.test(e)&&e.length>0){ t.setValue(i)}});this.callParent(arguments)},getSubmitData:function(){ var t={ },e=typeof this.value=="string"?parseFloat(this.value.replace(",",".")):this.value;t[this.name]=e||this.defaultSubmitValue;return t}});
