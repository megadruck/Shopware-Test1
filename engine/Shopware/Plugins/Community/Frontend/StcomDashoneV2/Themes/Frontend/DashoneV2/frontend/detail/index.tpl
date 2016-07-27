{extends file='parent:frontend/detail/index.tpl'}

{* Previous product *}
{block name='frontend_detail_article_back'}
    {/block}
    
    {* Next product *}
{block name='frontend_detail_article_next'}
    {/block}
    
    
{* Product SKU *}
{block name='frontend_detail_data_ordernumber'}
{/block}

{block name='frontend_detail_index_data' prepend}
    <li class="base-info--entry entry--sku">
        <strong class="entry--label">
            {s name="DetailDataId" namespace="frontend/detail/data"}{/s}
        </strong>
        <span class="entry--content">{$sArticle.ordernumber}</span>
    </li>

    {* Delivery informations *}
    {if ($sArticle.sConfiguratorSettings.type != 1 && $sArticle.sConfiguratorSettings.type != 2) || $activeConfiguratorSelection == true}
        {include file="frontend/plugins/index/delivery_informations.tpl" sArticle=$sArticle}
    {/if}

    {* Unit price *}
    {if $sArticle.purchaseunit}
        <div class='product--price price--unit'>

            {* Unit price label *}
            {block name='frontend_detail_data_price_unit_label'}
                <span class="price--label label--purchase-unit">
								{s name="DetailDataInfoContent" namespace='frontend/detail/data'}{/s}
							</span>
            {/block}

            {* Unit price content *}
            {block name='frontend_detail_data_price_unit_content'}
                {$sArticle.purchaseunit} {$sArticle.sUnit.description}
            {/block}

            {* Unit price is based on a reference unit *}
            {if $sArticle.purchaseunit && $sArticle.purchaseunit != $sArticle.referenceunit}

                {* Reference unit price content *}
                {block name='frontend_detail_data_price_unit_reference_content'}
                    ({$sArticle.referenceprice|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
                    / {$sArticle.referenceunit} {$sArticle.sUnit.description})
                {/block}
            {/if}
        </div>
    {/if}
{/block}

{* Tab navigation *}
                {block name="frontend_detail_index_tabs_navigation"}
                    {if count($sArticle.sSimilarArticles) > 0 OR $showAlsoBought OR $sArticle.sRelatedArticles OR $showAlsoViewed}
                    <div class="tab--navigation">
                        {block name="frontend_detail_index_tabs_navigation_inner"}
                            {block name="frontend_detail_index_related_similiar_tabs"}

                                {* Tab navigation - Accessory products *}
                                {block name="frontend_detail_tabs_entry_related"}
                                    {if $sArticle.sRelatedArticles && !$sArticle.crossbundlelook}
                                        <a href="#content--related-products" title="{s namespace="frontend/detail/tabs" name='DetailTabsAccessories'}{/s}" class="tab--link">
                                            {s namespace="frontend/detail/tabs" name='DetailTabsAccessories'}{/s}
                                            <span class="product--rating-count-wrapper">
                                                <span class="product--rating-count">{$sArticle.sRelatedArticles|@count}</span>
                                            </span>
                                        </a>
                                    {/if}
                                {/block}

                                {* Similar products *}
                                {block name="frontend_detail_index_recommendation_tabs_entry_similar_products"}
                                    {if count($sArticle.sSimilarArticles) > 0}
                                        <a href="#content--similar-products" title="{s name="DetailRecommendationSimilarLabel"}{/s}" class="tab--link">{s name="DetailRecommendationSimilarLabel"}{/s}</a>
                                    {/if}
                                {/block}
                            {/block}

                            {* Customer also bought *}
                            {block name="frontend_detail_index_tabs_entry_also_bought"}
                                {if $showAlsoBought}
                                    <a href="#content--also-bought" title="{s name="DetailRecommendationAlsoBoughtLabel"}{/s}" class="tab--link">{s name="DetailRecommendationAlsoBoughtLabel"}{/s}</a>
                                {/if}
                            {/block}

                            {* Customer also viewed *}
                            {block name="frontend_detail_index_tabs_entry_also_viewed"}
                                {if $showAlsoViewed}
                                    <a href="#content--customer-viewed" title="{s name="DetailRecommendationAlsoViewedLabel"}{/s}" class="tab--link">{s name="DetailRecommendationAlsoViewedLabel"}{/s}</a>
                                {/if}
                            {/block}

                            {* Related product streams *}
                            {block name="frontend_detail_index_tabs_entry_related_product_streams"}
                                {foreach $sArticle.relatedProductStreams as $key => $relatedProductStream}
                                    <a href="#content--related-product-streams-{$key}" title="{$relatedProductStream.name}" class="tab--link">{$relatedProductStream.name}</a>
                                {/foreach}
                            {/block}
                        {/block}
                    </div>
                    {/if}
                {/block}


           