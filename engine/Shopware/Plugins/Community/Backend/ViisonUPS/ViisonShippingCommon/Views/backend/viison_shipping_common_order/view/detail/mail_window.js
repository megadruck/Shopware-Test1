//{namespace name="backend/viison_shipping_common_order/order"}
Ext.define("Shopware.apps.ViisonShippingCommonOrder.view.detail.MailWindow",{ extend:"Shopware.apps.ViisonShippingCommonMail.view.MailWindow",alias:"widget.order-viison-shipping-common-mail-window",snippets:{ title:'{s name=mail_window/title}{/s}',loadMaskMessage:'{s name=mail_window/load_mask_message}{/s}'},initComponent:function(){ this.title=this.snippets.title;this.items=[this.createMailPanel()];this.loadMask=new Ext.LoadMask(this,{ msg:this.snippets.loadMaskMessage});this.loadMask.hide();this.callParent(arguments)},createPanel:function(){ return Ext.create("Shopware.apps.ViisonShippingCommonOrder.view.detail.Mail",{ mail:this.mail,trackingCode:this.trackingCode,cls:this.cls})}});
