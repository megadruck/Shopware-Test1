{extends file="parent:frontend/checkout/ajax_cart.tpl"}
{namespace name="frontend/checkout/ajax_cart_custom"}

{block name="frontend_checkout_ajax_cart_button_container" append}
    {block name="frontend_checkout_customizing_ajax_cart_button_container"}
        {include file="frontend/swag_customizing/checkout/ajax_cart.tpl"}
    {/block}
{/block}
