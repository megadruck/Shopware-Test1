{extends file='parent:frontend/checkout/ajax_cart_item.tpl'}

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