{if $sBasketItem.customizing}
    {if $sBasketItem.customizingValues}
        {$customizingUploadUrl = {url controller=customizing action=upload basketId=$sBasketItem.id forceSecure}}
        <dl class="customizing--options">
            {foreach $sBasketItem.customizingValues as $value}
                {if !$value.number}
                    <dt class="options--name">{$value.name}:</dt>
                    <dd class="options--value">{include file='frontend/plugins/swag_customizing/value.tpl'}</dd>
                {/if}
            {/foreach}
        </dl>
        <div class="table--content">
            <i class="icon--arrow-right"></i>
            {block name="frontend_checkout_cart_item_details_inline_edit"}
                <a href="{url controller=customizing action=load articleId=$sBasketItem.articleID basketId=$sBasketItem.id forceSecure}" class="link-edit-item">
                    {s name="CustomizingCartItemLoad" namespace="frontend/checkout/cart_item"}Auf Artikelseite laden{/s}
                </a>
            {/block}
        </div>
    {elseif $sBasketItem.customizing}
        {$customizingUploadUrl = {url controller=customizing action=upload basketId=$sBasketItem.articleID forceSecure}}
        {if $sBasketItem.articlename|strpos:': ' === false || $sBasketItem.articlename|strlen > 60 || $sBasketItem.customizing.type == 'image_select'}
            <dl class="customizing--options">
                <dd class="options--value">{include file='frontend/plugins/swag_customizing/value.tpl' value=$sBasketItem.customizing}</dd>
            </dl>
        {/if}
    {/if}
{/if}
