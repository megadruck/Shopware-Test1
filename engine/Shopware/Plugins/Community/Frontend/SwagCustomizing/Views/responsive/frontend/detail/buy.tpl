{extends file='parent:frontend/detail/buy.tpl'}

{block name="frontend_detail_buy_quantity" append}
    {block name="frontend_detail_customizing_buy_quantity"}
        {include file="frontend/swag_customizing/detail/quantity.tpl"}
    {/block}
{/block}

{block name="frontend_detail_buy_button_container"}
    {block name="frontend_detail_customizing_buy_button_container"}
        {if !$sCustomLicense}
            {$smarty.block.parent}
        {/if}
    {/block}
{/block}
