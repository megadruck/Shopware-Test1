{block name='frontend_checkout_error_messages_basket_error' append}
    {if $sCustomLicense}
        <div class="error center bold">
            {s name='CustomizingLicenseError' namespace='frontend/checkout/cart_item'}{/s}
        </div>
    {/if}
    {if $customizingBasketError}
        <div class="error center bold">
            {$customizingLastStockErrorMessage}
        </div>
    {/if}
{/block}
