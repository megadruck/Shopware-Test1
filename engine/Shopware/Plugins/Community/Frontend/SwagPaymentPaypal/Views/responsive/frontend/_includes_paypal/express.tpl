{if $PaypalShowButton}
    <div class="paypal-express">

        {* PayPal express delimiter *}
        <span class="paypal-express--delimiter">
            {s name='PaypalButtonDelimiter'}{/s}
        </span>

        {* PayPal express button *}
        <a href="{url controller=payment_paypal action=express forceSecure}"
           title="{s name='PaypalButtonLinkTitleText'}{/s}"
           class="paypal-express--btn">
            {if !$PaypalLocale || $PaypalLocale == 'de_DE'}
                <img srcset="{link file='frontend/_public/src/img/paypal-button-express-de.png'}, {link file='frontend/_public/src/img/paypal-button-express-de-2x.png'} 2x"
                     alt="{s name='PaypalButtonAltText'}{/s}">
            {elseif $PaypalLocale|strpos:"en" !== false}
                <img src="{link file='frontend/_public/src/img/paypal-button-express-en.png'}"
                     alt="{s name='PaypalButtonAltText'}{/s}">
            {else}
                <img src="https://www.paypal.com/{$PaypalLocale}/i/btn/btn_xpressCheckout.gif"
                     alt="{s name='PaypalButtonAltText'}{/s}">
            {/if}
        </a>
    </div>
{/if}
