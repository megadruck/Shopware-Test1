Ext.form.Checkbox.prototype.validate=function(){ var i=true;if(this.validator){ i=this.validator();if(i===true){ Ext.form.Field.prototype.clearInvalid.call(this);if(this.boxLabelEl){ this.boxLabelEl.removeCls("x-form-invalid-field");this.boxLabelEl.dom.removeAttribute("data-errorqtip")}}else{ Ext.form.Field.prototype.markInvalid.call(this,i);if(this.boxLabelEl){ this.boxLabelEl.addCls("x-form-invalid-field");this.boxLabelEl.dom.setAttribute("data-errorqtip",i)}}}return i===true};//{namespace name="backend/viison_shipping_common_config/config"}
Ext.define("Shopware.apps.ViisonShippingCommonConfig.view.Panel",{ extend:"Ext.form.Panel",xtype:"viison-shipping-common-config-panel",requires:"Shopware.apps.Config",commonSnippets:{ countryNames:{ AT:'{s name=countryNames/AT}{/s}',BE:'{s name=countryNames/BE}{/s}',BG:'{s name=countryNames/BG}{/s}',CH:'{s name=countryNames/CH}{/s}',CY:'{s name=countryNames/CY}{/s}',CZ:'{s name=countryNames/CZ}{/s}',DE:'{s name=countryNames/DE}{/s}',DK:'{s name=countryNames/DK}{/s}',EE:'{s name=countryNames/EE}{/s}',ES:'{s name=countryNames/ES}{/s}',FI:'{s name=countryNames/FI}{/s}',FR:'{s name=countryNames/FR}{/s}',GB:'{s name=countryNames/GB}{/s}',GR:'{s name=countryNames/GR}{/s}',HU:'{s name=countryNames/HU}{/s}',IE:'{s name=countryNames/IE}{/s}',IT:'{s name=countryNames/IT}{/s}',LT:'{s name=countryNames/LT}{/s}',LU:'{s name=countryNames/LU}{/s}',LV:'{s name=countryNames/LV}{/s}',NL:'{s name=countryNames/NL}{/s}',PT:'{s name=countryNames/PT}{/s}',PL:'{s name=countryNames/PL}{/s}',RO:'{s name=countryNames/RO}{/s}',SE:'{s name=countryNames/SE}{/s}',SI:'{s name=countryNames/SI}{/s}',SK:'{s name=countryNames/SK}{/s}'}},postProcessItems:function(i,e){ var I=this;var l=[];Ext.each(e,function(e){ e=Ext.applyIf({ shopId:i.getId()},e);if(e.name){ e=Ext.applyIf({ origName:e.name,name:"values["+i.getId()+"]["+e.name+"]",listeners:{ change:I.fieldChange}},e)}l.push(e)});return l},initForm:function(i){ this.shopStore=i;this.add(this.getItems());this.doLayout()},getItems:function(){ var i=this;var e=[],I=[];i.shopStore.each(function(e){ I.push({ xtype:"config-fieldset",title:e.get("name"),items:i.postProcessItems(e,i.createItems(e))})});if(I.length>1){ e.push({ xtype:"tabpanel",bodyStyle:"background-color: transparent !important",border:false,activeTab:0,enableTabScroll:true,deferredRender:false,items:I})}else{ if(I.length>0){ delete I[0].title}e.push({ xtype:"panel",bodyStyle:"background-color: transparent !important",border:false,layout:"fit",items:I})}return e},initComponent:function(){ this.incotermData=[["-"],["EXW"],["FCA"],["CPT"],["CIP"],["DAT"],["DAP"],["DDP"]];this.callParent(arguments)},groupHasNonBlankValues:function(i,e,I){ var l=this;var S=i.query('[group="'+e+'"][shopId="'+I+'"]');var O=false;Ext.each(S,function(i){ if(!l.isFieldBlank(i)){ O=true;return false}});return O},groupCompletelyFilled:function(i,e,I){ var l=this;var S=i.query('[group="'+e+'"][shopId="'+I+'"]');var O=true;Ext.each(S,function(i){ if(l.isFieldBlank(i)){ O=false;return false}});return O},groupValidator:function(i){ var e=this;var I=e.up("viison-shipping-common-config-panel");var l=e.up("form");if(!e.hasOwnProperty("group")){ throw new Error("groupValidator(): Group attribute missing for field '"+e.name+"'")}var S=!I.groupHasNonBlankValues(l,e.group,e.shopId);if(!I.isFieldBlank(e)||e.optionalGroupField){ return true}else if(S){ var O=true;Ext.each(I.groupDependencies[e.group],function(i){ if(I.groupHasNonBlankValues(l,i,e.shopId)){ O=I.snippets.groupDependencyValidationFailed;return false}});return O}else{ return I.snippets.groupValidationFailed}},fieldChange:function(i,e,I,l){ if(i.group){ var S=i.up("form").query('[group="'+i.group+'"][shopId="'+i.shopId+'"]');Ext.each(S,function(e){ if(e.name===i.name){ return}e.validate()})}if(i.influencesValidationOf){ Ext.each(i.influencesValidationOf,function(e){ i.up("form").down('[origName="'+e+'"][shopId="'+i.shopId+'"]').validate()})}},isFieldBlank:function(i){ return i.getValue()===null||i.getValue()===""||i.emptyText&&i.getValue()===i.emptyText},disallowBlankIfCheckedValidator:function(i){ var e=this;var I=e.up("viison-shipping-common-config-panel");if(!e.hasOwnProperty("checkboxName")){ throw new Error("disallowBlankIfCheckedValidator(): checkboxName attribute missing for field '"+e.name+"'")}var l=e.up("form").down('[origName="'+e.checkboxName+'"][shopId="'+e.shopId+'"]');var S=l.getValue();var O=!(S&&I.isFieldBlank(e));if(O){ return true}else{ return I.snippets.disallowBlankIfCheckedValidationFailed}},allowOnlyIfGroupFilledValidator:function(i){ var e=this;var I=e.up("viison-shipping-common-config-panel");var l=e.up("form");if(!e.hasOwnProperty("referencedGroup")){ throw new Error("allowOnlyIfGroupFilledValidator(): referencedGroup attribute missing for field '"+e.name+"'")}var S=I.groupCompletelyFilled(l,e.referencedGroup,e.shopId);var O=e.getValue();if(!O){ return true}if(S){ return true}else{ return e.validationErrorMessage}}});
