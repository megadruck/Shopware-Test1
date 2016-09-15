{extends file="parent:frontend/checkout/cart.tpl"}
{block name="frontend_checkout_actions_confirm"}{/block}
{block name='frontend_index_navigation_categories_top' append}
    {assign var='sBreadcrumb' value=[['name'=>"{s name='CheckoutCartTitle'}Ihr Warenkorb{/s}", 'link' =>{url action='cart'}]]}
    <nav class="content--breadcrumb under--navigation">
        <div class="container">
            {block name='frontend_index_breadcrumb_inner'}
                {include file='frontend/index/breadcrumb.tpl'}
            {/block}
        </div>
    </nav>
{/block}
