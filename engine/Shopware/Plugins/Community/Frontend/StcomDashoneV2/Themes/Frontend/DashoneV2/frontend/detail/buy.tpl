{extends file='parent:frontend/detail/buy.tpl'}



{block name='frontend_detail_buy_quantity_select'}
    {if $sArticle.attr6 == "1"} {* Check for setting manual input *}
        <input maxlength="6" type="number" id="sQuantity" name="sQuantity" class="js--fancy-input" value="{$sArticle.minpurchase}">{if $sArticle.packunit} {$sArticle.packunit}{/if}
    {else}
        <select id="sQuantity" name="sQuantity" class="quantity--select">
                {section name="i" start=$sArticle.minpurchase loop=$maxQuantity step=$sArticle.purchasesteps}
                        <option value="{$smarty.section.i.index}">{$smarty.section.i.index}{if $sArticle.packunit} {$sArticle.packunit}{/if}</option>
                {/section}
        </select>
    {/if}     
{/block}

{* "Buy now" button *}
{block name="frontend_detail_buy_button"}
   {if $sArticle.sConfigurator && !$activeConfiguratorSelection}
        <button class="buybox--button block btn is--disabled is--icon-right" disabled="disabled" aria-disabled="true"
                name="{s name="DetailBuyActionAdd"}{/s}"{if $buy_box_display} style="{$buy_box_display}"{/if}>
            {s name="DetailBuyActionAdd"}{/s} <i class="icon--arrow-right"></i>
        </button>
    {else}
        <button class="buybox--button block btn is--primary is--icon-right is--center"
                name="{s name="DetailBuyActionAdd"}{/s}"{if $buy_box_display} style="{$buy_box_display}"{/if}>
            {s name="DetailBuyActionAdd"}{/s} <i class="icon--arrow-right"></i>
        </button>
    {/if}
{/block}

