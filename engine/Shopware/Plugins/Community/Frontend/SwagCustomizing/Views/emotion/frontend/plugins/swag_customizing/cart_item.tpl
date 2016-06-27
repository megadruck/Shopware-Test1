{* include js *}
{block name="frontend_index_header_javascript" append}
    <script src="{link file='frontend/_resources/javascript/swag_customizing_tooltip.js'}"></script>
{/block}

{block name='frontend_checkout_cart_item_small_quantities_details'}
    {if !$sBasketItem.customizing || $sBasketItem.customizingGroupId}
        {$smarty.block.parent}
    {else}
        {if $sBasketItem.customizing.type == 'text_field' || $sBasketItem.customizing.type == 'text_area' || $sBasketItem.customizing.type == 'text_html' }
            <div class="grid_6">
                <div class="basket_details">
                    <strong class="title">{$sBasketItem.customizing.name}:</strong>
                    <div>
                        {if $sBasketItem.customizing.value|count_characters <= 50}
                            {$sBasketItem.customizing.value|strip_tags}
                        {else}
                            <span class="tooltip" data-tooltip="{$sBasketItem.customizing.value|strip_tags}">{$sBasketItem.customizing.value|strip_tags|truncate:50}</span>
                        {/if}
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
            </div>
        {elseif $sBasketItem.customizing.type == 'upload_image' || $sBasketItem.customizing.type == 'upload_file' || $sBasketItem.customizing.type == 'image_select'}
            <div class="grid_6">
                <div class="customizing-basket_details">
                    <div class="customizing-image-option-name">
                        <strong class="title">{$sBasketItem.customizing.name|truncate:150}:</strong>
                    </div>
                    <div class="customizing-image-container">
                        {$value = $sBasketItem.customizing}
                        {$customizingUploadUrl = {url controller=customizing action=upload basketId=$sBasketItem.articleID forceSecure}}
                        {include file='frontend/plugins/swag_customizing/value.tpl'}
                    </div>
                </div>
                <div class="clear">&nbsp;</div>
            </div>
        {else}
            <div class="grid_6">
                <div class="basket_details">
                    <strong class="title">{$sBasketItem.customizing.name}:</strong>
                    {$value = $sBasketItem.customizing}
                    {$customizingUploadUrl = {url controller=customizing action=upload basketId=$sBasketItem.articleID forceSecure}}
                    {include file='frontend/plugins/swag_customizing/value.tpl'}
                </div>
                <div class="clear">&nbsp;</div>
            </div>
        {/if}
    {/if}
{/block}

{block name="frontend_checkout_Cart_item_small_quantities_price" append}
    <div class="clear">&nbsp;</div>
{/block}
