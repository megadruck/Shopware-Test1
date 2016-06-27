{extends file="parent:frontend/detail/index.tpl"}

{block name="frontend_index_content" prepend}
    {block name="frontend_index_customizing_content"}
        {include file="frontend/swag_customizing/detail/content.tpl"}
    {/block}
{/block}

{block name="frontend_index_header_javascript_jquery_lib" append}
    {block name="frontend_index_customizing_header_javascript_jquery_lib"}
        {include file="frontend/swag_customizing/index/jquery_lib.tpl"}
    {/block}
{/block}

{block name='frontend_detail_buy_laststock'}
    {block name='frontend_detail_customizing_buy_laststock'}
        {if $sCustomLicense}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name='DetailBuyInfoNotAvailable' namespace='frontend/detail/buy'}{/s}"}
        {else}
            {$smarty.block.parent}
        {/if}
    {/block}
{/block}
