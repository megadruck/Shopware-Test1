{extends file='parent:frontend/checkout/ajax_cart_item.tpl'}

{* Real product *}
{block name='frontend_checkout_ajax_cart_articleimage_product'}
    {if $basketItem.modus == $IS_PRODUCT || $basketItem.modus == $IS_PREMIUM_PRODUCT}
        {$desc = $basketItem.articlename|escape}
        {if $basketItem.additional_details.image.thumbnails}
            {if $basketItem.additional_details.image.description}
                {$desc = $basketItem.additional_details.image.description|escape}
            {/if}
            <img srcset="{$basketItem.additional_details.image.thumbnails[0].sourceSet}" alt="{$desc}" title="{$desc|truncate:25:""}" class="thumbnail--image" />

        {elseif $basketItem.image.src.0}
            <img src="{$basketItem.image.src.0}" alt="{$desc}" title="{$desc|truncate:25:""}" class="thumbnail--image" />
        {/if}

    {/if}

{/block}
{* Article actions *}
{block name='frontend_checkout_ajax_cart_actions'}
    <div class="action--container">
        {if $basketItem.modus != 4}
            <a href="{url controller="checkout" action='ajaxDeleteArticleCart' sDelete=$basketItem.id}" class="btn is--small action--remove" title="{s name="AjaxCartRemoveArticle" namespace="frontend/checkout/ajax_cart"}{/s}">
                <i class="icon--cross3"></i>
            </a>
        {/if}
    </div>
{/block}

{* Article name *}
{block name='frontend_checkout_ajax_cart_articlename'}
    {$useAnchor = ($basketItem.modus != 4 && $basketItem.modus != 2)}
    {if $useAnchor}
        <a class="item--link" href="{$detailLink}" title="{$basketItem.articlename|escape}">
    {else}
        <div class="item--link">
    {/if}
    {block name="frontend_checkout_ajax_cart_articlename_quantity"}
        <span class="item--quantity">{$basketItem.quantity}x</span>
    {/block}
    {block name="frontend_checkout_ajax_cart_articlename_name"}
        <span class="item--name">
					{if $basketItem.modus == 10}
                        {s name='AjaxCartInfoBundle' namespace="frontend/checkout/ajax_cart"}{/s}
                    {else}
                        {if $theme.offcanvasCart}
                            {$basketItem.additional_details.articleName}
                        {else}
                            {$basketItem.additional_details.articleName|truncate:28:"...":true}
                        {/if}
                    {/if}
				</span>
        <ul class="variationList">
            {foreach $sBasketItem.variation as $value}
                <li>{$value.name}: <strong>{$value.value}</strong></li>
            {/foreach}
        </ul>
    {/block}
    {if $useAnchor}
        </a>
    {else}
        </div>
    {/if}

{/block}