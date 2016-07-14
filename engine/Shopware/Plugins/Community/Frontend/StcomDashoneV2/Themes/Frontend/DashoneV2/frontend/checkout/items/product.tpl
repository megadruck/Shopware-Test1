{extends file="parent:frontend/checkout/items/product.tpl"}

{* Remove product from basket *}
{block name='frontend_checkout_cart_item_delete_article'}{/block}

{block name='frontend_checkout_cart_item_total_sum' prepend}
    <div class="panel--td column--actions">
        <a href="{url action='deleteArticle' sDelete=$sBasketItem.id sTargetAction=$sTargetAction}" class="btn is--small column--actions-link" title="{"{s name='CartItemLinkDelete '}{/s}"|escape}">
            <i class="icon--cross2"></i>
        </a>
    </div>
{/block}