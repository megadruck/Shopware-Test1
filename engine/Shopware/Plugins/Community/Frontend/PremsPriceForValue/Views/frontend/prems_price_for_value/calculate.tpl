{block name="prems_price_for_value_block_price_include"}
<div class="product--price price--default">
  <span class="price--content content--default">
    {*<meta content="{$prices.finalPriceBrutto|replace:',':'.'}" itemprop="price">*}
    {if $prices.priceFromActive}{s name='PremsPriceForValueFrom'}ab{/s} {/if}{$prices.finalPriceBrutto|currency} {s name='PremsRequiredStar'}*{/s}
  </span>
  {* Discount price *}
  {block name='prems_price_for_value_pseudo_price'}
    {if $prices.pseudoPrice}

      {block name='frontend_detail_data_pseudo_price_discount_icon'}
        <span class="price--discount-icon">
                                <i class="icon--percent2"></i>
                            </span>
      {/block}

      {* Discount price content *}
      {block name='frontend_detail_data_pseudo_price_discount_content'}
        <span class="content--discount">
           <span class="price--line-through">{$prices.pseudoPrice|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}</span>
				</span>
      {/block}
    {/if}
  {/block}

  {if $premsShowNettoPrice || $premsShowVat}
  <br />
  <span class="prems-price-for-value-extra-prices">
    {if $premsShowNettoPrice}{s name='PremsPriceForValueNettoPrice'}Nettopreis:{/s} {$prices.finalPriceNetto|currency}<br />{/if}
    {if $premsShowVat}{s name='PremsPriceForValueVat'}Mehrwertsteuer:{/s} {$prices.finalPriceTax|currency}{/if}
  </span>
  {/if}
  {if $premsShowPiecesPrice}
  <span class="prems-price-for-value-pieces-prices">
    <br />{s name='PremsPriceForValuePiecesPrice'}Stückpreis:{/s} {$prices.piecesPriceBrutto|currency} {s name='PremsRequiredStar'}*{/s}
  </span>
  {/if}
</div>
{/block}