{extends file='parent:frontend/checkout/confirm.tpl'}


{block name='frontend_checkout_confirm_tos_revocation_notice' prepend}
<input type="text" name="md_reference" class="is--hidden user-reference--hidden">
{/block}


{block name="frontend_checkout_confirm_additional_features"}
    <div class="panel has--border additional--features">
        {block name="frontend_checkout_confirm_additional_features_headline"}
            <div class="panel--title is--underline">
                {s name="ConfirmHeadlineAdditionalOptions"}{/s}
            </div>
        {/block}

        {block name="frontend_checkout_confirm_additional_features_content"}
            <div class="panel--body is--wide block-group">




                {* Additional feature - Add voucher *}
                {block name="frontend_checkout_confirm_additional_features_add_voucher"}
                    <div class="feature--group block">

                        {* Additional feature - Add reference / o_attr_1 *}
                        {block name="frontend_checkout_confirm_additional_features_add_product"}
                            <div class="feature--add-product">
                                {s name='ConfirmAttribute1Text' namespace='frontend/checkout/confirm'}Dies erscheint auf Ihrer Rechnung{/s}
                                {block name='frontend_checkout_confirm_add_product_field'}
                                    <input type="text" class="user-reference--field add-product--field block" placeholder="{s name='ConfirmAttribute1Field' namespace='frontend/checkout/confirm'}Stichwort/Referenz{/s}" data-pseudo-text="true" data-selector=".user-reference--hidden">
                                {/block}
                                {block name='frontend_checkout_confirm_add_product_button'}{/block}
                            </div>

                        {/block}
                        <div class="feature--voucher">
                            <form method="post" action="{url action='addVoucher' sTargetAction=$sTargetAction}" class="table--add-voucher add-voucher--form">
                                {block name='frontend_checkout_table_footer_left_add_voucher_agb'}
                                    {if !{config name='IgnoreAGB'}}
                                        <input type="hidden" class="agb-checkbox" name="sAGB"
                                               value="{if $sAGBChecked}1{else}0{/if}"/>
                                    {/if}
                                {/block}

                                {block name='frontend_checkout_confirm_add_voucher_field'}
                                    <input type="text" class="add-voucher--field block" name="sVoucher" placeholder="{"{s name='CheckoutFooterAddVoucherLabelInline' namespace='frontend/checkout/cart_footer'}{/s}"|escape}" />
                                {/block}

                                {block name='frontend_checkout_confirm_add_voucher_button'}
                                    <button type="submit" class="add-voucher--button btn is--primary is--small block">
                                        <i class="icon--arrow-right"></i>
                                    </button>
                                {/block}
                            </form>
                        </div>

                    </div>
                {/block}
                {* Additional customer comment for the order *}
                {block name='frontend_checkout_confirm_comment'}
                    <div class="feature--user-comment block">
                        <textarea class="user-comment--field" rows="5" cols="20" placeholder="{s name="ConfirmPlaceholderComment" namespace="frontend/checkout/confirm"}{/s}" data-pseudo-text="true" data-selector=".user-comment--hidden">{$sComment|escape}</textarea>
                    </div>
                {/block}
            </div>
        {/block}
    </div>
{/block}