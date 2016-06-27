{extends file="backend/base/header.tpl"}

{block name="backend/base/header/javascript" append}
	<script type="text/javascript">
		{**
		 * We want to make sure that our extensions to the Shopware backend modules are loaded before the modules
		 * themselves are loaded to make sure that the module is fully functional after it has been loaded. On the
		 * other hand, we do not want to load code before it is actually needed. We can achieve these goals by
		 * registering a preprocessor which loads our extension to the module that is about to be loaded in a
		 * synchronous way to make sure that the extension has been loaded before the actual module is loaded.
		 * This code is executed before the Shopware ExtJS application Shopware.app.Application is launched, because
		 * the launch method of an ExtJS application is only called after the full page is loaded and the DOM is ready.
		 * At this point in time, no sub-applications have been launched. Therefore this is a good place to register
		 * our preprocessor.
		 *}
		Ext.Class.registerPreprocessor('shopware.viisonShippingCommon.loader', function(cls, data, hooks, fn) {
			if (Ext.getClassName(cls) == 'Shopware.apps.Order') {
				Ext.syncRequire('Shopware.apps.ViisonShippingCommonOrder');
			} else if (Ext.getClassName(cls) == 'Shopware.apps.Shipping') {
				Ext.syncRequire('Shopware.apps.ViisonShippingCommonShipping');
			}
			{**
			 * This is a workaround for the Shopware bug that multiple sub-applications listed as requirements are not
			 * being loaded correctly. Manually make sure that the ViisonIntrashipFreeFormLabels module is loaded when opening the
			 * config sub-application.
			 *}
			else if (Ext.getClassName(cls) == "Shopware.apps.ViisonShippingCommonConfig") {
				Ext.syncRequire('Shopware.apps.ViisonShippingCommonFreeFormLabels'); // make country store accessible from the config
			}
		}, true);
	</script>
{/block}
