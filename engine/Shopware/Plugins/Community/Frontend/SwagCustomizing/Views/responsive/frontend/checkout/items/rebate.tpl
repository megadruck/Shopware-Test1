{extends file="parent:frontend/checkout/items/rebate.tpl"}

{block name="frontend_checkout_cart_item_rebate_details_title"}
    {block name="frontend_checkout_customizing_cart_item_rebate_details_title"}
        {if $sBasketItem.customizing.name}
            <span class="content--title">
				{$sBasketItem.customizing.name|strip_tags|truncate:60}:
			</span>
        {else}
            {$smarty.block.parent}
        {/if}
    {/block}
{/block}

{block name="frontend_checkout_cart_item_rebate_details_inline" append}
    {block name="frontend_checkout_customizing_cart_item_rebate_details_inline"}
        {if !$sBasketItem.customizing || $sBasketItem.customizingGroupId}
            {$smarty.block.parent}
        {else}
            {include file='frontend/plugins/swag_customizing/value.tpl' value=$sBasketItem.customizing}
        {/if}
    {/block}
{/block}
