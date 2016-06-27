{extends file="parent:frontend/checkout/ajax_cart_item.tpl"}

{block name="frontend_checkout_ajax_cart_articlename_name" append}
    {block name="frontend_checkout_customizing_ajax_cart_articlename_name"}
        {include file="frontend/swag_customizing/checkout/ajax_cart_item.tpl"}
    {/block}
{/block}
