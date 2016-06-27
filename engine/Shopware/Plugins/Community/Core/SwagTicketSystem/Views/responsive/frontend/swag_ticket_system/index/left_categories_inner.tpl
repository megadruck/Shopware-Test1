{if $showMenuEntry}
	{include file='frontend/index/sidebar-categories.tpl'}
{else}
	<div class="hide-menu">
		{include file='frontend/index/sidebar-categories.tpl'}
	</div>
{/if}