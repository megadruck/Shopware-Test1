{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_detail_index_buy_container_inner" prepend}
  <div class="prems-price-for-value-config premsPiecesPrice">
    <input type="hidden" name="requestUrl" value="{url controller='PremsPriceForValue' action='calculate'}" />
    <input type="hidden" name="liveShoppingEnabled" value="{$premsLiveShoppingEnabled}" />
    <input type="hidden" name="premsUseTextfieldForQuantity" value="{$premsUseTextfieldForQuantity}" />
    <input type="hidden" name="premsReloadPrices" value="{$premsReloadPrices}" />
    <input type="hidden" name="purchaseSteps" value="{$sArticle.purchasesteps}" />
  </div>
{/block}

{block name="frontend_detail_data_block_prices_table_body_cell_price"}
  <td class="block-prices--cell">
    {assign var="sArticlePriceNumeric" value=$blockPrice.price|replace:",":"."|floatval}
    {assign var="sArticleTaxFactor" value=$sArticle.tax+100}
    {assign var="sArticlePriceNettoNumeric" value=$sArticlePriceNumeric/$sArticleTaxFactor*100}
    {assign var="sArticlePriceNetto" value=$sArticlePriceNettoNumeric|number_format:2:",":""}

    {$blockPrice.price|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
    {if $premsNettoPriceOnBlockPrices}
      <span class="prems-netto-price-on-block-prices">
      ( {$sArticlePriceNetto|currency} {s name='PremsPriceForValueNetto'}Netto{/s} )
      </span>
    {/if}
    {if $premsPseudoPriceOnBlockPrices}
      <span class="prems-pseudo-price-on-block-prices">
        {$blockPrice.pseudoprice|currency}
      </span>
    {/if}
  </td>
{/block}

{* Price - Starting from *}
{block name='frontend_detail_data_price_configurator_starting_from_content' append}
  {if $sArticle.minpurchase > 1}
    {assign var="sArticlePriceNumeric" value=$sArticle.priceStartingFrom|replace:",":"."|floatval}

    {if !$calculateCheapestPriceWithMinPurchase || !$sArticle.priceStartingFrom}
      {assign var="sArticlePriceNumeric" value=$sArticlePriceNumeric*$sArticle.minpurchase}
      {assign var="sArticlePiecesPriceBrutto" value=$sArticle.price}

      {if $sArticle.pseudoprice}
        {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
        {assign var="sArticlePseudopriceNumeric" value=$sArticlePseudopriceNumeric*$sArticle.minpurchase}
      {/if}
    {/if}
    {if $calculateCheapestPriceWithMinPurchase && $sArticle.priceStartingFrom}
      {assign var="sArticlePiecesPriceBrutto" value=round($sArticle.price/$sArticle.minpurchase, 3)}

      {if $sArticle.pseudoprice}
        {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
        {assign var="sArticlePseudopriceNumeric" value=$sArticlePseudopriceNumeric}
      {/if}
    {/if}

  {else}
    {assign var="sArticlePriceNumeric" value=$sArticle.priceStartingFrom|replace:",":"."|floatval}
    {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
  {/if}
  {assign var="sArticleTaxFactor" value=$sArticle.tax+100}
  {assign var="sArticlePriceNettoNumeric" value=$sArticlePriceNumeric/$sArticleTaxFactor*100}
  {assign var="sArticlePriceVatNumeric" value=$sArticlePriceNumeric-$sArticlePriceNettoNumeric}
  {assign var="sArticlePriceVat" value=$sArticlePriceVatNumeric|number_format:2:",":""}
  {assign var="sArticlePriceNetto" value=$sArticlePriceNettoNumeric|number_format:2:",":""}
  {if $premsShowNettoPrice || $premsShowVat}
    <br />
    <span class="prems-price-for-value-extra-prices">
    {if $premsShowNettoPrice}{s name='PremsPriceForValueNettoPrice'}Nettopreis:{/s} {$sArticlePriceNetto|currency}<br />{/if}
    {if $premsShowVat}{s name='PremsPriceForValueVat'}Mehrwertsteuer:{/s} {$sArticlePriceVat|currency}{/if}
    </span>
  {/if}
  {if $premsShowPiecesPrice && $sArticlePiecesPriceBrutto}
    <span class="prems-price-for-value-pieces-prices">
    <br />{s name='PremsPriceForValuePiecesPrice'}Stückpreis:{/s} {$sArticlePiecesPriceBrutto|currency} {s name='PremsRequiredStar'}*{/s}
  </span>
    <br /><br />
  {/if}
{/block}

{block name='frontend_detail_data_pseudo_price'}
  {if $sArticle.pseudoprice}
    {if $sArticle.minpurchase > 1}
      {assign var="sArticlePriceNumeric" value=$sArticle.price|replace:",":"."|floatval}

      {if !$calculateCheapestPriceWithMinPurchase || !$sArticle.priceStartingFrom}
        {assign var="sArticlePriceNumeric" value=$sArticlePriceNumeric*$sArticle.minpurchase}
        {assign var="sArticlePiecesPriceBrutto" value=$sArticle.price}

        {if $sArticle.pseudoprice}
          {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
          {assign var="sArticlePseudopriceNumeric" value=$sArticlePseudopriceNumeric*$sArticle.minpurchase}
        {/if}
      {/if}
      {if $calculateCheapestPriceWithMinPurchase && $sArticle.priceStartingFrom}
        {assign var="sArticlePiecesPriceBrutto" value=round($sArticle.price/$sArticle.minpurchase, 3)}

        {if $sArticle.pseudoprice}
          {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
          {assign var="sArticlePseudopriceNumeric" value=$sArticlePseudopriceNumeric}
        {/if}
      {/if}

    {else}
      {assign var="sArticlePriceNumeric" value=$sArticle.price|replace:",":"."|floatval}
      {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
    {/if}
    {assign var="sArticleTaxFactor" value=$sArticle.tax+100}
    {assign var="sArticlePriceNettoNumeric" value=$sArticlePriceNumeric/$sArticleTaxFactor*100}
    {assign var="sArticlePriceVatNumeric" value=$sArticlePriceNumeric-$sArticlePriceNettoNumeric}
    {assign var="sArticlePriceVat" value=$sArticlePriceVatNumeric|number_format:2:",":""}
    {assign var="sArticlePriceBrutto" value=$sArticlePriceNumeric|number_format:2:",":""}

    {assign var="sArticlePriceNetto" value=$sArticlePriceNettoNumeric|number_format:2:",":""}

    <div class="premsDynPriceArea">
      <span class="price--content content--default">
        <meta content="{$sArticle.price|replace:',':'.'}" itemprop="price">
        {if $sArticle.priceStartingFrom && !$sArticle.liveshoppingData}{s name='ListingBoxArticleStartsAt' namespace="frontend/listing/box_article"}{/s} {/if}{$sArticlePriceBrutto|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
      </span>

      {block name='frontend_detail_data_pseudo_price_discount_icon'}
        <span class="price--discount-icon">
                                  <i class="icon--percent2"></i>
                              </span>
      {/block}

      {* Discount price content *}
      {block name='frontend_detail_data_pseudo_price_discount_content'}
        <span class="content--discount">
           <span class="price--line-through">{$sArticlePseudopriceNumeric|number_format:2:",":""|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}</span>
          {* Percentage discount *}
          {block name='frontend_detail_data_pseudo_price_discount_content_percentage'}
            {if $sArticle.pseudopricePercent.float}
              <span class="price--discount-percentage">({$sArticle.pseudopricePercent.float}% {s name="PremsDetailDataInfoSavePercent"}gespart{/s})</span>
            {/if}
          {/block}
          </span>
      {/block}

      {if $premsShowNettoPrice || $premsShowVat}
        <br />
        <span class="prems-price-for-value-extra-prices">
          {if $premsShowNettoPrice}{s name='PremsPriceForValueNettoPrice'}Nettopreis:{/s} {$sArticlePriceNetto|currency}<br />{/if}
          {if $premsShowVat}{s name='PremsPriceForValueVat'}Mehrwertsteuer:{/s} {$sArticlePriceVat|currency}{/if}
      </span>
      {/if}
      {if $premsShowPiecesPrice && $sArticlePiecesPriceBrutto}
        <span class="prems-price-for-value-pieces-prices">
          <br />{s name='PremsPriceForValuePiecesPrice'}Stückpreis:{/s} {$sArticlePiecesPriceBrutto|currency} {s name='PremsRequiredStar'}*{/s}
        </span>
      {/if}
    </div>
  {/if}
{/block}

{* Regular price *}
{block name='frontend_detail_data_price_default'}
  {if $liveShopping}
    {assign var="price" value=$liveShopping.currentPrice}
  {else}
    {assign var="price" value=$sArticle.price}
  {/if}

  {if !$sArticle.pseudoprice}
    {if $sArticle.minpurchase > 1}
      {assign var="sArticlePriceNumeric" value=$price|replace:",":"."|floatval}

      {if !$calculateCheapestPriceWithMinPurchase || !$sArticle.priceStartingFrom}
        {assign var="sArticlePriceNumeric" value=$sArticlePriceNumeric*$sArticle.minpurchase}
        {assign var="sArticlePiecesPriceBrutto" value=$sArticle.price}

        {if $sArticle.pseudoprice}
          {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
          {assign var="sArticlePseudopriceNumeric" value=$sArticlePseudopriceNumeric*$sArticle.minpurchase}
        {/if}
      {/if}
      {if $calculateCheapestPriceWithMinPurchase && $sArticle.priceStartingFrom}
        {assign var="sArticlePiecesPriceBrutto" value=round($sArticle.price/$sArticle.minpurchase, 2)}

        {if $sArticle.pseudoprice}
          {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
          {assign var="sArticlePseudopriceNumeric" value=$sArticlePseudopriceNumeric}
        {/if}
      {/if}

    {else}
      {assign var="sArticlePriceNumeric" value=$price|replace:",":"."|floatval}
      {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
    {/if}
    {assign var="sArticleTaxFactor" value=$sArticle.tax+100}

      {assign var="sArticlePriceNettoNumeric" value=$sArticlePriceNumeric/$sArticleTaxFactor*100}
      {assign var="sArticlePriceVatNumeric" value=$sArticlePriceNumeric-$sArticlePriceNettoNumeric}
      {assign var="sArticlePriceVat" value=$sArticlePriceVatNumeric|number_format:2:",":""}
      {assign var="sArticlePriceBrutto" value=$sArticlePriceNumeric|number_format:2:",":""}

    {assign var="sArticlePriceNetto" value=$sArticlePriceNettoNumeric|number_format:2:",":""}

    <div class="premsDynPriceArea">
    <span class="price--content content--default">
      <meta content="{$sArticle.price|replace:',':'.'}" itemprop="price">
      {if $sArticle.priceStartingFrom && !$sArticle.liveshoppingData}{s name='ListingBoxArticleStartsAt' namespace="frontend/listing/box_article"}{/s} {/if}{$sArticlePriceBrutto|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
    </span>

    {if $premsShowNettoPrice || $premsShowVat}
    <br />
    <span class="prems-price-for-value-extra-prices">
      {if $premsShowNettoPrice}{s name='PremsPriceForValueNettoPrice'}Nettopreis:{/s} {$sArticlePriceNetto|currency}<br />{/if}
      {if $premsShowVat}{s name='PremsPriceForValueVat'}Mehrwertsteuer:{/s} {$sArticlePriceVat|currency}{/if}
    </span>
    {/if}
    {if $premsShowPiecesPrice && $sArticlePiecesPriceBrutto}
      <span class="prems-price-for-value-pieces-prices">
        <br />{s name='PremsPriceForValuePiecesPrice'}Stückpreis:{/s} {$sArticlePiecesPriceBrutto|currency} {s name='PremsRequiredStar'}*{/s}
      </span>
    {/if}
    </div>
  {/if}
{/block}

{block name="frontend_detail_data_block_price_include" prepend}
    <div class="product--price price--default premsDynPriceArea">
      {if $sArticle.minpurchase > 1}
        {assign var="sArticlePriceNumeric" value=$sArticle.price|replace:",":"."|number_format:2:".":""|floatval}

        {if !$calculateCheapestPriceWithMinPurchase || !$sArticle.priceStartingFrom}
          {assign var="sArticlePriceNumeric" value=$sArticlePriceNumeric*$sArticle.minpurchase}
          {assign var="sArticlePiecesPriceBrutto" value=$sArticle.price}

          {if $sArticle.pseudoprice}
            {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
            {assign var="sArticlePseudopriceNumeric" value=$sArticlePseudopriceNumeric*$sArticle.minpurchase}
          {/if}
        {/if}
        {if $calculateCheapestPriceWithMinPurchase && $sArticle.priceStartingFrom}
          {assign var="sArticlePiecesPriceBrutto" value=round($sArticle.price/$sArticle.minpurchase, 2)}

          {if $sArticle.pseudoprice}
            {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
            {assign var="sArticlePseudopriceNumeric" value=$sArticlePseudopriceNumeric}
          {/if}
        {/if}

      {else}
        {assign var="sArticlePriceNumeric" value=$sArticle.price|replace:",":"."|floatval}
        {assign var="sArticlePseudopriceNumeric" value=$sArticle.pseudoprice|replace:",":"."|floatval}
      {/if}
      {assign var="sArticleTaxFactor" value=$sArticle.tax+100}
      {assign var="sArticlePriceNettoNumeric" value=$sArticlePriceNumeric/$sArticleTaxFactor*100}
      {assign var="sArticlePriceVatNumeric" value=$sArticlePriceNumeric-$sArticlePriceNettoNumeric}
      {assign var="sArticlePriceVat" value=$sArticlePriceVatNumeric|number_format:2:",":""}
      {assign var="sArticlePriceBrutto" value=$sArticlePriceNumeric|number_format:2:",":""}

      {assign var="sArticlePriceNetto" value=$sArticlePriceNettoNumeric|number_format:2:",":""}

      <span class="price--content content--default">
        <meta content="{$sArticle.price|replace:',':'.'}" itemprop="price">
        {$sArticlePriceBrutto|currency} {s name='PremsRequiredStar'}*{/s}
      </span>

      {* Discount price *}
      {block name='prems_price_for_value_pseudo_price'}
        {if $sArticlePseudopriceNumeric}

          {block name='frontend_detail_data_pseudo_price_discount_icon'}
            <span class="price--discount-icon">
                                    <i class="icon--percent2"></i>
                                </span>
          {/block}

          {* Discount price content *}
          {block name='frontend_detail_data_pseudo_price_discount_content'}
            <span class="content--discount">
               <span class="price--line-through">{$sArticlePseudopriceNumeric|number_format:2:",":""|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}</span>
            </span>
          {/block}
        {/if}
      {/block}

      {if $premsShowNettoPrice || $premsShowVat}
        <br />
        <span class="prems-price-for-value-extra-prices">
        {if $premsShowNettoPrice}{s name='PremsPriceForValueNettoPrice'}Nettopreis:{/s} {$sArticlePriceNetto|currency}<br />{/if}
          {if $premsShowVat}{s name='PremsPriceForValueVat'}Mehrwertsteuer:{/s} {$sArticlePriceVat|currency}{/if}
      </span>
      {/if}

      {if $premsShowPiecesPrice && $sArticlePiecesPriceBrutto}
        <span class="prems-price-for-value-pieces-prices">
        <br />{s name='PremsPriceForValuePiecesPrice'}Stückpreis:{/s} {$sArticlePiecesPriceBrutto|currency} {s name='PremsRequiredStar'}*{/s}
        </span>
      {/if}

    </div>
{/block}