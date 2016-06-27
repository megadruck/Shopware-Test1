{block name="frontend_checkout_customizing_ajax_custom_row"}
    <div class="ajax-values-container--customizing-row">
        {block name="frontend_checkout_customizing_ajax_custom_row_name"}
            <b><span class="customizing-row--name">
				{$ac.articlename|truncate:37|strip_tags}
			</span></b>
        {/block}
        {block name="frontend_checkout_customizing_ajax_custom_row_value"}
            <span class="customizing-row--value">
				{$ac.amount|currency}
			</span>
            <br>
            <div class="customizing--row-detail">
                {if $ac.customizing.type == "upload_file" ||
                $ac.customizing.type == "upload_image"}
                    {foreach from=$ac.customizing.value item=item}
                        {$item|truncate:74|strip_tags}
                        <br>
                    {/foreach}
                {elseif $ac.customizing.type == "image_select"}
                    {$image = $ac.customizing.value}

                    {if !$isShopware51}
                        {$image = $ac.customizing.value|pathinfo}
                        {$image = "{$image.dirname}/thumbnail/{$image.filename}_{$customizingThumbnailSize}.{$image.extension}"}
                    {/if}
                    <img src="{$image}" style="max-width: 80px; max-height= 80px;">
                {else}
                    {$ac.customizing.value|truncate:74|strip_tags}
                {/if}
            </div>
        {/block}
    </div>
{/block}
