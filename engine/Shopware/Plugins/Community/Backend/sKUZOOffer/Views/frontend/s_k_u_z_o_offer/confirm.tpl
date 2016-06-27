{namespace name="frontend/sKUZOOffer/checkout/confirm"}
{extends file="frontend/checkout/confirm.tpl"}


{* Step box *}
{block name='frontend_index_navigation_categories_top'}
{/block}

{block name='frontend_checkout_confirm_tos_panel'}

{/block}


{if {config name=commentvoucherarticle}||{config name=premiumarticles}||{config name=bonussystem} && {config name=bonus_system_active} && {config name=displaySlider}}
    <div class="additional-options grid_16 first">

        {if {config name=commentvoucherarticle}}
            <h2 class="headingbox">{s name="ConfirmHeadlineAdditionalOptions"}Weitere Optionen{/s}</h2>
        {/if}

        <div class="inner_container">

            {* Voucher and add article *}
            {if {config name=commentvoucherarticle}}
                <div class="voucher-add-article">

                    {block name='frontend_checkout_table_footer_left_add_voucher'}
                        <div class="vouchers">
                            <form method="post" action="{url action='addVoucher' sTargetAction=$sTargetAction}">
                                {block name='frontend_checkout_table_footer_left_add_voucher_agb'}
                                    {if !{config name='IgnoreAGB'}}
                                        <input type="hidden" class="agb-checkbox" name="sAGB"
                                               value="{if $sAGBChecked}1{else}0{/if}"/>
                                    {/if}
                                {/block}
                                <label for="basket_add_voucher">{s name="CheckoutFooterLabelAddVoucher" namespace="frontend/checkout/cart_footer_left"}{/s}</label>
                                <input type="text" class="text" id="basket_add_voucher" name="sVoucher"
                                       onfocus="this.value='';"
                                       value="{s name='CheckoutFooterAddVoucherLabelInline' namespace="frontend/checkout/cart_footer_left"}{/s}"/>
                                <input type="submit"
                                       value="{s name='CheckoutFooterActionAddVoucher' namespace="frontend/checkout/cart_footer_left"}{/s}"
                                       class="box_send"/>
                            </form>
                        </div>
                    {/block}
                    <div class="clear"></div>
                    <div class="space"></div>
                    {block name='frontend_checkout_table_footer_left_add_article'}
                        <div class="add_article">
                            <form method="post" action="{url action='addArticle' sTargetAction=$sTargetAction}">
                                <label for="basket_add_article">{s name='CheckoutFooterLabelAddArticle' namespace="frontend/checkout/cart_footer_left"}{/s}:</label>
                                <input id="basket_add_article" name="sAdd" type="text" value="{s name='CheckoutFooterIdLabelInline' namespace="frontend/checkout/cart_footer_left"}{/s}" onfocus="this.value='';" class="ordernum text" />
                                <input type="submit" class="box_send" value="{s name='CheckoutFooterActionAdd' namespace="frontend/checkout/cart_footer_left"}{/s}" />
                            </form>
                        </div>
                    {/block}
                </div>

                {* Comment functionality *}
                {block name='frontend_checkout_confirm_comment'}
                    <div class="feature--user-comment block">
                        {*<label for="sComment">{s name="ConfirmLabelComment" namespace="frontend/checkout/confirm"}{/s}</label>*}
                        <textarea class="user-comment--field" rows="5" cols="20"  placeholder="{s name="ConfirmPlaceholderComment" namespace="frontend/checkout/confirm"}{/s}" data-pseudo-text="true" data-selector=".user-comment--hidden">{$sComment|escape}</textarea>
                    </div>
                    <div class="clear"></div>
                {/block}
                {* Additional customer comment for the order *}
                {*{block name='frontend_checkout_confirm_comment'}

                    <div class="feature--user-comment block">
                        <textarea class="user-comment--field" rows="5" cols="20" placeholder="{s name="ConfirmPlaceholderComment" namespace="frontend/checkout/confirm"}{/s}" data-pseudo-text="true" data-selector=".user-comment--hidden">{$sComment|escape}</textarea>
                    </div>
                {/block}*}
                <div class="space"></div>
            {/if}

            {* Premiums articles *}
            {block name='frontend_checkout_confirm_premiums'}
                {if $sPremiums}
                    {if {config name=premiumarticles}}
                        <h2 class="headingbox">{s name="sCartPremiumsHeadline" namespace="frontend/checkout/premiums"}{/s}</h2>
                        {include file='frontend/checkout/premiums.tpl'}
                    {/if}
                {/if}
            {/block}
        </div>
    </div>
    <div class="space"></div>
{/if}


{block name='frontend_checkout_confirm_submit'}
<form id="confirmOffer--form" method="post" action="{url action='finish'}">
    <textarea class="is--hidden user-comment--hidden" rows="1" cols="1" name="sComment">{$sComment|escape}</textarea>
        <button type="submit" class="btn is--primary is--large right is--icon-right" form="confirmOffer--form" data-preloader-button="true">
            {s name='ConfirmActionSubmit'}Confirm Offer{/s}<i class="icon--arrow-right"></i>
        </button>
    </form>

{/block}

{block name="frontend_checkout_confirm_left_billing_address_actions"}
        <div class="panel--actions">
            <a href="{url controller=account action=billing sTarget=sKUZOOffer}" class="btn is--small">
                {s name="ConfirmLinkChangeBilling" namespace="frontend/checkout/confirm_left"}{/s}
            </a>
            <a href="{url controller=account action=selectBilling sTarget=sKUZOOffer}" class="btn is--small">
                {s name="ConfirmLinkSelectBilling" namespace="frontend/checkout/confirm_left"}{/s}
            </a>
        </div>
{/block}

{block name="frontend_checkout_confirm_left_shipping_address_actions"}
        <div class="panel--actions">
            <a href="{url controller=account action=shipping sTarget=sKUZOOffer}" class="btn is--small">
                {s name="ConfirmLinkChangeShipping" namespace="frontend/checkout/confirm_left"}{/s}
            </a>

            <a href="{url controller=account action=selectShipping sTarget=sKUZOOffer}" class="btn is--small">
                {s name="ConfirmLinkSelectShipping" namespace="frontend/checkout/confirm_left"}{/s}
            </a>
        </div>
{/block}

{block name='frontend_checkout_confirm_left_payment_method_actions'}
         <div class="panel--actions payment--actions">
            <a href="{url controller=sKUZOOffer action=shippingPayment sTarget=sKUZOOffer}" class="btn is--small btn--change-payment">
                {s name="ConfirmLinkChangePayment" namespace="frontend/checkout/confirm_left"}{/s}
            </a>
        </div>
{/block}


