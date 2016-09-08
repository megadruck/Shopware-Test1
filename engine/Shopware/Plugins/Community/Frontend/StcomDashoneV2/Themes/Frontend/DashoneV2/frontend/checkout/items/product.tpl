{extends file="parent:frontend/checkout/items/product.tpl"}
{block name='frontend_checkout_cart_item_details_sku'}{/block}
{block name='frontend_checkout_cart_item_delivery_informations'}{/block}

{* Product name *}
{block name='frontend_checkout_cart_item_details_title'}

    <a class="content--title" href="{$detailLink}" title="{$sBasketItem.additional_details.articleName|strip_tags|escape}"
            {if {config name=detailmodal} && {controllerAction|lower} === 'confirm'}
        data-modalbox="true"
        data-content="{url controller="detail" action="productQuickView" ordernumber="{$sBasketItem.ordernumber}" fullPath forceSecure}"
        data-mode="ajax"
        data-width="750"
        data-sizing="content"
        data-title="{$sBasketItem.additional_details.articleName|strip_tags|escape}"
        data-updateImages="true"
            {/if}>
        {if $sBasketItem.additional_details.articleName}
            {$sBasketItem.additional_details.articleName|strip_tags|truncate:60}
        {else}
            {$sBasketItem.articleName|strip_tags|truncate:60}
        {/if}
    </a>
    <ul class="variationList">
        {foreach $sBasketItem.variation as $value}
            <li>{$value.name}: <strong>{$value.value}</strong></li>
        {/foreach}
    </ul>
{/block}



{block name='frontend_checkout_cart_item_delete_article'}
    <div class="panel--td column--actions">
        <a href="{url action='deleteArticle' sDelete=$sBasketItem.id sTargetAction=$sTargetAction}" class="btn is--small column--actions-link" title="{"{s name='CartItemLinkDelete '}{/s}"|escape}">
            <i class="icon--cross"></i>
        </a>
    </div>
{/block}