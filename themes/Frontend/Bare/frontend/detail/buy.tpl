{block name="frontend_detail_buy"}
	<form name="sAddToBasket" method="post" action="{url controller=checkout action=addArticle}" class="buybox--form" data-add-article="true" data-eventName="submit"{if $theme.offcanvasCart} data-showModal="false" data-addArticleUrl="{url controller=checkout action=ajaxAddArticleCart}"{/if}>
		{if $sArticle.sConfigurator&&$sArticle.sConfiguratorSettings.type==3}
			{foreach $sArticle.sConfigurator as $group}
				<input type="hidden" name="group[{$group.groupID}]" value="{$group.selected_value}"/>
			{/foreach}
		{/if}

		<input type="hidden" name="sActionIdentifier" value="{$sUniqueRand}"/>
		<input type="hidden" name="sAddAccessories" id="sAddAccessories" value=""/>

		{* @deprecated - Product variants block *}
		{block name='frontend_detail_buy_variant'}{/block}

		<input type="hidden" name="sAdd" value="{$sArticle.ordernumber}"/>

		{* Article accessories *}
		{if $sArticle.sAccessories}
			{block name='frontend_detail_buy_accessories'}
				<div class="buybox--accessory">
					{foreach $sArticle.sAccessories as $sAccessory}

						{* Group name *}
						<h2 class="accessory--title">{$sAccessory.groupname}</h2>
						<div class="accessory--group">

							{* Group description *}
							<p class="group--description">
								{$sAccessory.groupdescription}
							</p>

							{foreach $sAccessory.childs as $sAccessoryChild}
								<input type="checkbox" class="sValueChanger chkbox" name="sValueChange"
									   id="CHECK{$sAccessoryChild.ordernumber}" value="{$sAccessoryChild.ordernumber}"/>
								<label for="CHECK{$sAccessoryChild.ordernumber}">{$sAccessoryChild.optionname|truncate:35}
									({s name="DetailBuyLabelSurcharge"}{/s}
									: {$sAccessoryChild.price} {$sConfig.sCURRENCYHTML})
								</label>
								<div id="DIV{$sAccessoryChild.ordernumber}" class="accessory--overlay">
									{include file="frontend/detail/accessory.tpl" sArticle=$sAccessoryChild.sArticle}
								</div>
							{/foreach}
						</div>
					{/foreach}
				</div>
			{/block}
		{/if}

		{$sCountConfigurator=$sArticle.sConfigurator|@count}

		{if (!isset($sArticle.active) || $sArticle.active)}
			{if $sArticle.isAvailable}
				{block name="frontend_detail_buy_button_container"}
				<div class="buybox--button-container block-group{if $NotifyHideBasket && $sArticle.notification && $sArticle.instock <= 0} is--hidden{/if}">

					{* Quantity selection *}
					{block name='frontend_detail_buy_quantity'}
						<div class="buybox--quantity block">
							{$maxQuantity=$sArticle.maxpurchase+1}
							{if $sArticle.laststock && $sArticle.instock < $sArticle.maxpurchase}
								{$maxQuantity=$sArticle.instock+1}
							{/if}

							{block name='frontend_detail_buy_quantity_select'}
								<select id="sQuantity" name="sQuantity" class="quantity--select">
									{section name="i" start=$sArticle.minpurchase loop=$maxQuantity step=$sArticle.purchasesteps}
										<option value="{$smarty.section.i.index}">{$smarty.section.i.index}{if $sArticle.packunit} {$sArticle.packunit}{/if}</option>
									{/section}
								</select>
							{/block}
						</div>
					{/block}

					{* "Buy now" button *}
					{block name="frontend_detail_buy_button"}
						{if $sArticle.sConfigurator && !$activeConfiguratorSelection}
							<button class="buybox--button block btn is--disabled is--icon-right is--large" disabled="disabled" aria-disabled="true" name="{s name="DetailBuyActionAdd"}{/s}"{if $buy_box_display} style="{$buy_box_display}"{/if}>
								{s name="DetailBuyActionAdd"}{/s} <i class="icon--arrow-right"></i>
							</button>
						{else}
							<button class="buybox--button block btn is--primary is--icon-right is--center is--large" name="{s name="DetailBuyActionAdd"}{/s}"{if $buy_box_display} style="{$buy_box_display}"{/if}>
								{s name="DetailBuyActionAdd"}{/s} <i class="icon--arrow-right"></i>
							</button>
						{/if}
					{/block}
				</div>
				{/block}
			{/if}
		{/if}
	</form>
{/block}
