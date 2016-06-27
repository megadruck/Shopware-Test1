{extends file="parent:frontend/checkout/items/product.tpl"}

{block name="frontend_checkout_cart_item_delivery_informations" prepend}
    {block name="frontend_checkout_customizing_cart_item_delivery_informations"}
        {include file="frontend/swag_customizing/checkout/values.tpl"}
    {/block}
{/block}
