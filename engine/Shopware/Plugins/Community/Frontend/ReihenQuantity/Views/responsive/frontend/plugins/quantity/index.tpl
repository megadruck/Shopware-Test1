
{block name='frontend_detail_buy_quantity_select'}
    {if $sArticle.attr6}
<div class="buybox--quantityNum buybox--quantityNum-style-third">
	
		<input id="sQuantity"  name="sQuantity" type="text" value="{$sArticle.minpurchase}" style="margin: 0px 0px 4px 0;padding:8px  !important;">
				
	{if $sArticle.packunit}<span class="quantity--packunit">{$sArticle.packunit}</span>{/if}
        &nbsp;&nbsp;&nbsp;<strong>X</strong>
</div>
{else}
    {$smarty.block.parent}
        {/if}
{/block}

