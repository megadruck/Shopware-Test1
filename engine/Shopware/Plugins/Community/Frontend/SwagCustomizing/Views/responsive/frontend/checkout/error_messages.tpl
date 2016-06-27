{extends file="parent:frontend/checkout/error_messages.tpl"}

{block name='frontend_checkout_error_messages_basket_error' append}
    {block name='frontend_checkout_customizing_error_messages_basket_error'}
        {include file="frontend/swag_customizing/checkout/error_messages.tpl"}
    {/block}
{/block}
