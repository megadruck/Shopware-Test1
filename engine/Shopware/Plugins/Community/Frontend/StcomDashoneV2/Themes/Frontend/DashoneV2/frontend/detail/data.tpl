{extends file='parent:frontend/detail/data.tpl'}

{block name="frontend_detail_data_delivery"}
{/block}

{block name='frontend_detail_data_price'}
{/block}

{* Default price *}
{block name='frontend_detail_data_price_configurator'}
    {if $sArticle.priceStartingFrom && !$sArticle.sConfigurator && $sView}
        {* Price - Starting from *}
        {block name='frontend_detail_data_price_configurator_starting_from_content'}
            <span class="price--content content--starting-from">
								{s name="DetailDataInfoFrom"}{/s} {$sArticle.priceStartingFrom|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
							</span>
        {/block}
    {else}
        {if $sArticle.pseudoprice}
            <span class="content--discount">
                                <span class="price--line-through">{$sArticle.pseudoprice|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}</span>
							</span>
        {/if}
        {* Regular price *}
        {block name='frontend_detail_data_price_default'}
            <span class="price--content content--default">
								<meta itemprop="price" content="{$sArticle.price|replace:',':'.'}">
                {if $sArticle.priceStartingFrom && !$sArticle.liveshoppingData}{s name='ListingBoxArticleStartsAt' namespace="frontend/listing/box_article"}{/s} {/if}{$sArticle.price|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
							</span>
        {/block}
    {/if}
{/block}

{* Discount price *}
{block name='frontend_detail_data_pseudo_price'}
    {if $sArticle.pseudoprice}
        <p>
            {block name='frontend_detail_data_pseudo_price_discount_icon'}
                <span class="price--discount-icon">
                                <i class="icon--percent2"></i>
                            </span>
            {/block}

            {* Discount price content *}
            {block name='frontend_detail_data_pseudo_price_discount_content'}
                <span class="content--discount">
                {* Percentage discount *}
                    {block name='frontend_detail_data_pseudo_price_discount_content_percentage'}
                        {if $sArticle.pseudopricePercent.float}
                            <span class="price--discount-percentage">({$sArticle.pseudopricePercent.float}% {s name="DetailDataInfoSavePercent"}{/s})</span>
                        {/if}
                    {/block}
							</span>
            {/block}
        </p>
    {/if}
{/block}