{block name='frontend_index_header_css_screen' append}
    <link rel="stylesheet" href="{link file='frontend/_resources/styles/swag_customizing.css'}"/>
{/block}

{block name='frontend_index_header_javascript_jquery' append}
    <script type="text/javascript">
        ;(function ($) {
            $(function () {
                $('.smaller-row').parents('.table_row').css('min-height', 35);
            });
        })(jQuery);
    </script>
{/block}

{block name='frontend_checkout_cart_item_image'}
    {if !$sBasketItem.customizing || $sBasketItem.customizingGroupId}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='frontend_checkout_cart_item_details'}
    {if $sBasketItem.customizing}
        <div class="customizing_checkout">
    {/if}
    {if $sBasketItem.customizing && !$sBasketItem.customizingGroupId}
        <div class="basket_details smaller-row">
            <a class="title" href="{$sBasketItem.linkDetails}" title="{$sBasketItem.articlename|strip_tags}">
                {$sBasketItem.articlename|strip_tags|truncate:60}
            </a>
            <p class="ordernumber">
                {s name='CartItemInfoId' namespace='frontend/checkout/cart_item'}{/s} {$sBasketItem.ordernumber}
            </p>
            {block name='frontend_checkout_cart_item_details_inline'}
                <p>
                    {s name='CheckoutItemPrice' namespace='frontend/checkout/confirm_item'}{/s} {$sBasketItem.price|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
                </p>
            {/block}
        </div>
        <div class="clear">&nbsp;</div>
        {* Main article features *}
        <div class="main-article-features">
            <p>
                {include file="string:{config name=mainfeatures}"}
            </p>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
    {if $sBasketItem.customizing}
        </div>
    {/if}
{/block}

{block name='frontend_checkout_cart_item_total_sum'}
    {if $sBasketItem.customizing && !$sBasketItem.price}
        <div class="grid_2"></div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='frontend_checkout_cart_item_tax_price'}
    {if $sBasketItem.customizing && !$sBasketItem.price}
        <div class="grid_2"></div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='frontend_checkout_cart_item_details_inline' append}
    {if $sBasketItem.customizingValues}
        {$customizingUploadUrl = {url controller=customizing action=upload basketId=$sBasketItem.id forceSecure}}
        <dl>
            {foreach $sBasketItem.customizingValues as $value}
                {if !$value.number}
                    <dt><div>{$value.name}:</div></dt>
                    <dd>{include file='frontend/plugins/swag_customizing/value.tpl'}</dd>
                {/if}
            {/foreach}
        </dl>
        <a href="{url controller=customizing action=load articleId=$sBasketItem.articleID basketId=$sBasketItem.id forceSecure}" class="link-edit-item">
            {s name='CustomizingCartItemLoad' namespace='frontend/checkout/cart_item'}{/s}
        </a>
    {elseif $sBasketItem.customizing}
        {$customizingUploadUrl = {url controller=customizing action=upload basketId=$sBasketItem.articleID forceSecure}}
        {if $sBasketItem.articlename|strpos:': ' === false || $sBasketItem.articlename|strlen > 60 || $sBasketItem.customizing.type == 'image_select'}
            <dl>
                <dd>{include file='frontend/plugins/swag_customizing/value.tpl' value=$sBasketItem.customizing}</dd>
            </dl>
        {/if}
    {/if}
{/block}

{block name='frontend_checkout_cart_item_delete_article'}
    {if $sBasketItem.customizing && !$sBasketItem.customizingGroupId && !$sBasketItem.customizing.required}
        <div class="action">
            <a href="{url action=deleteArticle sDelete=$sBasketItem.id sTargetAction=$sTargetAction}" class="del" title="{s name='CartItemLinkDelete' namespace='frontend/checkout/cart_item'}{/s}">
                &nbsp;
            </a>
            &nbsp;
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
