{extends file="parent:frontend/index/footer.tpl"}

{block name="frontend_index_header_css_screen" append}
    <style>
	    .footer--logo-img {
	    	margin: 0 auto;
	    }
	    #copyright_icon{
	    	margin-right: 3px;
	    }
    </style>
{/block}

{* Shopware footer *}
{block name="frontend_index_shopware_footer"}

	{* Copyright *}
	{block name="frontend_index_shopware_footer_copyright"}
		<div class="footer--copyright">
			<a href="{$link}">{$text}<a/>
		</div>
	{/block}

	{* Logo *}
	{block name="frontend_index_shopware_footer_logo"}
		<div class="footer--logo">
			<a href="{$link}"><img src="{$logo}" alt="{$alt}" title="{$title}" class="footer--logo-img"/></a>
		</div>
	{/block}
{/block}