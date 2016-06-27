{namespace name="frontend/sKUZOOffer/detail/index"}
{extends file="frontend/detail/index.tpl"}

{block name="frontend_detail_buy_button" append}
    {if $sArticle.sConfigurator && !$activeConfiguratorSelection}
        <button style="margin-top:5px;" class="buybox--button block btn is--disabled is--icon-right is--large" disabled="disabled" aria-disabled="true" title="{s name='detailOfferLink'}Ask for Offer{/s}">
            {s name='detailOfferLink'}Ask for Offer{/s} <i class="icon--arrow-right"></i>
        </button>
    {else}
        <a id="offerButton" style="margin-top:5px;" class="buybox--button block btn is--icon-right is--center is--large" href="#" title="{s name='detailOfferLink'}Ask for Offer{/s}">
            {s name='detailOfferLink'}Ask for Offer{/s} <i class="icon--arrow-right"></i>
        </a>
    {/if}
    <input type="hidden" name="sOpenOffer" id="sOpenOffer">
{/block}

{block name="frontend_index_header_javascript_jquery" append}

{*    <script type="text/javascript" src="{link file='frontend/s_k_u_z_o_offer/src/js/offer.js'}"></script> *}
    <script type="text/javascript">
        function addOfferButtonAction() {
            $("#offerButton").click(function(event) {
                event.preventDefault();
                $("#sOpenOffer").val(true);
                $('form[name=sAddToBasket]').unbind();
                $('form[name=sAddToBasket]').submit();
            });
        }
        jQuery(document).ready(function ($) {
            addOfferButtonAction();
        });
        //for sw5.1 support
        $( document ).ajaxComplete(function( event, xhr, settings ) {
            addOfferButtonAction();
        });
    </script>
{/block}