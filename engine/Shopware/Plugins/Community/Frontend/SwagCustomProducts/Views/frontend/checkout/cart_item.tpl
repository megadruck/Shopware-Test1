{extends file="parent:frontend/checkout/cart_item.tpl"}

{block name='frontend_checkout_cart_item_surcharge_discount'}
    {block name='frontend_checkout_cart_item_surcharge_discount_swag_custom_products'}
        {include file="frontend/swag_custom_products/checkout/rebate.tpl"}
    {/block}
{/block}

{* Product *}
{block name='frontend_checkout_cart_item_product'}
    {block name='frontend_checkout_cart_item_product_swag_custom_products'}
        {include file="frontend/swag_custom_products/checkout/product.tpl"}
    {/block}
{/block}
