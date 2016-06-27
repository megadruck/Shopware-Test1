{extends file='parent:backend/index/header.tpl'}

{block name="backend/base/header/css" append}
	<link type="text/css" media="all" rel="stylesheet" href="{link file='backend/_resources/viison_ups/css/ups-icon-set.css'}" />
	<link type="text/css" media="all" rel="stylesheet" href="{link file='backend/_resources/viison_ups/css/ups-configuration.css'}" />
	<style type="text/css">
		.c-sprite-ups-icon { background: url("{$viisonUPSPluginIconURL}"); }
	</style>
{/block}
