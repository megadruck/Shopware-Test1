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



           