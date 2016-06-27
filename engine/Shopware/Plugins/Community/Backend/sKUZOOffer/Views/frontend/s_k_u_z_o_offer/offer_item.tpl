{namespace name="frontend/sKUZOOffer/account/offers"}
{block name="frontend_account_order_item_overview_row"}
    <div class="order--item panel--tr">
{* Offer date *}
{block name="frontend_account_order_item_date"}
    <div class="order--date panel--td column--date">

        {block name="frontend_account_order_item_date_label"}
            <div class="column--label">
                {s name="OfferColumnDate" namespace="frontend/s_k_u_z_o_offer/offers"}{/s}:
            </div>
        {/block}

        {block name="frontend_account_order_item_date_value"}
            <div class="column--value">
                {$offerPosition.offertime|date}
            </div>
        {/block}
    </div>
{/block}

{* Offer id *}
{block name="frontend_account_order_item_number"}
    <div class="order--number panel--td column--id is--bold">

        {block name="frontend_account_order_item_number_label"}
            <div class="column--label">
                {s name="OfferColumnId" namespace="frontend/s_k_u_z_o_offer/offers"}{/s}:
            </div>
        {/block}

        {block name="frontend_account_order_item_number_value"}
            <div class="column--value">
                {$offerPosition.offerNumber}
            </div>
        {/block}
    </div>
{/block}

{* Dispatch type *}
{block name="frontend_account_order_item_dispatch"}
    <div class="order--dispatch panel--td column--dispatch">

        {block name="frontend_account_order_item_dispatch_label"}
            <div class="column--label">
                {s name="OrderColumnDispatch" namespace="frontend/s_k_u_z_o_offer/offers"}{/s}:
            </div>
        {/block}

        {block name="frontend_account_order_item_dispatch_value"}
            <div class="column--value">
                {if $offerPosition.dispatch.name}
                    {$offerPosition.dispatch.name}
                {else}
                    {s name="OfferInfoNoDispatch"}{/s}
                {/if}
            </div>
        {/block}
    </div>
{/block}

{* Offer status *}
{block name="frontend_account_order_item_status"}
    <div class="order--status panel--td column--status">

        {block name="frontend_account_order_item_status_label"}
            <div class="column--label">
                {s name="OfferColumnStatus" namespace="frontend/s_k_u_z_o_offer/offers"}{/s}:
            </div>
        {/block}

        {block name="frontend_account_order_item_status_value"}
            <div class="column--value">
                <span class="order--status-icon status--{$offerPosition.status}">&nbsp;</span>
                {if $offerPosition.status==1}
                    {s name="OfferOpen"}Open{/s}
                {elseif $offerPosition.status==2}
                    {s name="OfferProcessed"}Processed{/s}
                {elseif $offerPosition.status==3}
                    {s name="OfferSent"}Sent{/s}
                {elseif $offerPosition.status==4}
                    {s name="OfferAccepted"}Accepted{/s}
                {elseif $offerPosition.status==5}
                    {s name="OfferConfirmed"}Confirmed{/s}
                {/if}
            </div>
        {/block}
    </div>
{/block}

{* Offer actions *}
{block name="frontend_account_order_item_actions"}
    <div class="order--actions panel--td column--actions">
        <a href="#order{$offerPosition.offerNumber}"
           title="{"{s name="OfferActionSlide"}Details{/s}"|escape} {$offerPosition.offerNumber}"
           class="btn is--small"
           data-collapse-panel="true"
           data-collapseTarget="#order{$offerPosition.offerNumber}">
            {s name="OfferActionSlide"}Details{/s}
        </a>
    </div>
{/block}
</div>
{/block}

{* Offer details *}
{block name="frontend_account_order_item_detail"}

    {* Offer details *}
    <div id="order{$offerPosition.offerNumber}" class="order--details panel--table">

    {block name="frontend_account_order_item_detail_table"}

        {block name="frontend_account_order_item_detail_id"}
            <input type="hidden" name="sAddAccessories" value="{$offerNumber|escape}" />
        {/block}

        {block name="frontend_account_order_item_detail_table_head"}
            <div class="orders--table-header panel--tr is--secondary">

                {block name="frontend_account_order_item_detail_table_head_name"}
                    <div class="panel--th column--name" style="width: 40%;">{s name="OfferItemColumnName"}Article{/s}</div>
                {/block}

                {block name="frontend_account_order_item_detail_table_head_quantity"}
                    <div class="panel--th column--quantity is--align-center">{s name="OfferItemColumnQuantity"}Quantity{/s}</div>
                {/block}

                {block name="frontend_account_order_item_detail_table_head_tax"}
                    <div class="panel--th column--tax is--align-center" style="width: 10%;">{s name="OfferItemColumnTax"}Tax{/s}</div>
                {/block}

                {block name="frontend_account_order_item_detail_table_head_price"}
                    <div class="panel--th column--price is--align-right">{s name="OfferItemColumnPrice"}Price{/s}</div>
                {/block}

                {block name="frontend_account_order_item_detail_table_head_total"}
                    <div class="panel--th column--total is--align-right">{s name="OfferItemColumnTotal"}Total{/s}</div>
                {/block}
            </div>
        {/block}

        {block name="frontend_account_order_item_detail_table_rows"}
            {foreach $offerPosition.details as $article}

                {block name="frontend_account_order_item_detail_table_row"}
                    <div class="panel--tr">

                        {block name="frontend_account_order_item_info"}
                            <div class="panel--td order--info column--name" style="width: 40%;">

                                {* Name *}
                                {block name="frontend_account_order_item_name"}
                                    <p class="order--name is--strong">
                                        {* Mode 10 = Bundle Product *}
                                        {if $article.modus == 10}
                                            {s name="OrderItemInfoBundle"}{/s}
                                        {else}
                                            {$article.name}
                                        {/if}
                                    </p>
                                {/block}
                             </div>
                        {/block}

                        {* Offer item quantity *}
                        {block name='frontend_account_order_item_quantity'}
                            <div class="panel--td order--quantity column--quantity">

                                  {block name='frontend_account_order_item_quantity_value'}
                                    <div class="column--value">{$article.quantity} {$article.packUnit}</div>
                                {/block}

                                {block name="frontend_account_order_item_unit"}
                                    <p class="order--name" style="font-size: 10px;width: 100px;line-height: 10px;">
                                        {if $article.unitName}
                                            ({$article.totalUnit} {$article.unitName})
                                        {/if}
                                    </p>
                                {/block}
                                {block name="frontend_account_order_item_base_price"}
                                    <p class="order--name" style="font-size: 10px;width: 100px;line-height: 10px;">
                                        {if $article.pricePerUnit && $article.unitName}
                                            {$article.pricePerUnit|currency}/{$article.referenceUnit} {$article.unitName}
                                        {/if}
                                    </p>
                                {/block}


                            </div>
                        {/block}

                        {* Offer item tax *}
                        {block name='frontend_account_order_item_tax'}
                            <div class="panel--td order--quantity column--quantity"  style="width: 10%;">

                                {block name='frontend_account_order_item_tax_value'}
                                    <div class="column--value">
                                        {if $article.tax_rate}
                                            {$article.tax_rate} %
                                        {else}
                                            {s name="OrderItemInfoFree"}{/s}
                                        {/if}
                                    </div>
                                {/block}
                            </div>
                        {/block}

                        {* Offer item price *}
                        {block name='frontend_account_order_item_price'}
                            <div class="panel--td order--price column--price">

                                {block name='frontend_account_order_item_price_value'}
                                    <div class="column--value">
                                        {if $article.price}
                                            {$article.price|currency} {$offerPosition.currency_html}
                                        {else}
                                            {s name="OrderItemInfoFree"}{/s}
                                        {/if}
                                    </div>
                                {/block}
                            </div>
                        {/block}

                        {* Offer item total amount *}
                        {block name='frontend_account_order_item_amount'}
                            <div class="panel--td order--amount column--total">

                                {block name='frontend_account_order_item_amount_value'}
                                    <div class="column--value">
                                        {if $article.price && $article.quantity}
                                            {($article.price * $article.quantity)|currency} {$offerPosition.currency_html}
                                        {else}
                                            {s name="OrderItemInfoFree"}{/s}
                                        {/if}
                                    </div>
                                {/block}
                            </div>
                        {/block}
                    </div>
                {/block}
            {/foreach}
        {/block}

        <div class="panel--tr is--odd">

            {block name="frontend_account_order_item_detail_info_labels"}
                <div class="panel--td column--info-labels">
                    {* Offer date label *}
                    {block name="frontend_account_order_item_label_date"}
                        <p class="is--strong">{s name="OfferItemColumnDate"}Date{/s}</p>
                    {/block}

                    {* Offer number label *}
                    {block name="frontend_account_order_item_label_ordernumber"}
                        <p class="is--strong">{s name="OfferItemColumnId"}OfferNumber{/s}</p>
                    {/block}

                    {* Shipping method label  *}
                    {block name="frontend_account_order_item_label_dispatch"}
                        {if $offerPosition.dispatch}
                            <p class="is--strong">{s name="OfferItemColumnDispatch"}Dispatch{/s}</p>
                        {/if}
                    {/block}

                    {* Payment method label  *}
                    {block name="frontend_account_order_item_label_payment"}
                        {if $offerPosition.payment}
                            <p class="is--strong">{s name="OfferItemColumnPayment"}Payment{/s}</p>
                        {/if}
                    {/block}


                </div>
            {/block}

            {block name="frontend_account_order_item_detail_info_data"}
                <div class="panel--td column--info-data">
                    {* Offer date *}
                    {block name='frontend_account_order_item_date'}
                        <p>{$offerPosition.offertime|date}</p>
                    {/block}

                    {* Offer number *}
                    {block name='frontend_account_order_item_ordernumber'}
                        <p>{$offerPosition.offerNumber}</p>
                    {/block}

                    {* Shipping method *}
                    {block name='frontend_account_order_item_dispatch'}
                        {if $offerPosition.dispatch}
                            <p>{$offerPosition.dispatch.name}</p>
                        {/if}
                    {/block}

                    {* Payment method *}
                    {block name='frontend_account_order_item_payment'}
                        {if $offerPosition.payment}
                            <p>{$offerPosition.payment.description}</p>
                        {/if}
                    {/block}



                </div>
            {/block}

            {block name="frontend_account_order_item_detail_summary_labels"}
                <div class="panel--td column--summary-labels">

                    {block name="frontend_account_order_item_detail_shipping_costs"}

                        <p class="is--strong">{s name="OfferItemNetTotal"}NetTotal{/s}</p>
                        {* Shipping costs label *}
                        {block name="frontend_account_order_item_detail_shipping_costs_label"}
                            <p class="is--strong">{s name="OfferItemShippingcosts"}Shipping{/s}</p>
                        {/block}
                        <p class="is--bold">{s name="OfferItemTotal"}Total{/s}</p>
                        <p class="is--strong"  style="width: 190px;">{s name="OfferItemTotalPriceWithoutTax"}priceWithoutTax{/s}</p>

                        {foreach from=$offerPosition.taxRate item=taxRate key=key}
                            {if $taxRate}
                                <p class="is--strong">{s name="DocumentIndexTaxs"}zzgl. {$key} % MwSt:{/s}</p>
                            {/if}
                        {/foreach}

                    {/block}
                </div>
            {/block}

            {block name="frontend_account_order_item_detail_summary_data"}
                <div class="panel--td column--summary-data">

                    {block name="frontend_acccount_order_item_amount"}

                        <p>{$offerPosition.discount_amount|currency} {$offerPosition.currency_html}</p>
                        {* Shopping costs *}
                        {block name="frontend_account_order_item_shippingamount"}
                            <p>{$offerPosition.invoice_shipping|currency} {$offerPosition.currency_html}</p>
                        {/block}
                        <p class="is--bold">{$offerPosition.invoice_amount_net|currency} {$offerPosition.currency_html}</p>
                        <p>{$offerPosition.priceWithoutTax|currency} {$offerPosition.currency_html}</p>

                        {foreach from=$offerPosition.taxRate item=taxRate key=key}
                            {if $taxRate}
                                <p>{$taxRate|currency}{$offerPosition.currency_html}</p>
                            {/if}
                        {/foreach}

                    {/block}
                </div>
            {/block}
        </div>


        {* Repeat order *}
        {block name="frontend_account_order_item_repeat_order"}
            <div class="order--repeat panel--tr">


                    <form method="post" action="{url controller='sKUZOOffer' action='downloadPdf'}" id="offer-download-btn">

                        <input name="offerId" type="hidden" value="{$offerPosition.id|escape}" />
                        <input type="submit" class="btn is--primary is--small" value="{s name='OfferLinkDownload'}Download Offer{/s}" />

                    </form>

                <form method="post" action="{if $directPayment}{url controller='sKUZOOffer' action='payment'}{else}{url controller='sKUZOOffer' action='acceptOffer'}{/if}" id="offer-accept-btn">

                    <input name="offerId" type="hidden" value="{$offerPosition.id|escape}" />
                    {if $offerPosition.status != 4 && $offerPosition.status != 5}
                        {block name="frontend_account_order_item_repeat_button"}
                            {if $directPayment}
                                <input type="submit" class="btn is--primary is--small" value="{s name='OfferDoPayment'}Accept Offer{/s}" />
                            {else}
                                <input type="submit" class="btn is--primary is--small" value="{s name='OfferLinkRepeat'}Accept Offer{/s}" />
                            {/if}
                        {/block}
                    {/if}

                </form>

            </div>
        {/block}

    {/block}
    </div>

{/block}
