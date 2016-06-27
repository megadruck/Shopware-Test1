{if $sCustomLicense}
    {include file="frontend/_includes/messages.tpl" type="error" content="{s name='CustomizingLicenseError' namespace='frontend/checkout/cart_item'}{/s}"}
{/if}

{if $customizingBasketError}
    {include file="frontend/_includes/messages.tpl" type="error" content=$customizingLastStockErrorMessage}
{/if}
