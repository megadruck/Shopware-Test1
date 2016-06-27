{if $value.type == 'date'}
    <div>{$value.description}</div>
    {$value.value|date:'DATE_LONG'}
{elseif $value.type == 'date_time'}
    <div>{$value.description}</div>
    {$value.value|date:'DATETIME'}
{elseif $value.type == 'time'}
    <div>{$value.description}</div>
    {$value.value|date:'TIME_LONG'}
{elseif $value.type == 'image_select'}
    <div>{$value.description}</div>
    {if $customizingThumbnailSize}
        {$image = $value.value}

        {if !$isShopware51}
            {$image = $value.value|pathinfo}
            {$image = "{$image.dirname}/thumbnail/{$image.filename}_{$customizingThumbnailSize}.{$image.extension}"}
        {/if}
        <img class="customizing-checkout-image-selector" src="{link file=$image}" title="{$value.description|escape}"/>
    {else}
        {$value.description|escape}
    {/if}
{elseif $value.type == 'upload_file' || $value.type == 'upload_image'}
    <div>{$value.description}</div>
    {if $customizingUploadUrl}
        {foreach $value.value as $subValue}
            {$image = "{$customizingUploadUrl}?download=1&version=thumbnail&file={$subValue|escape}"}
            {$imageTemp = "{$customizingUploadUrl}?download=1&version=thumbnail&file={$subValue|escape}"|regex_replace:"{$customizingAllowedImages|escape}":""}
            <div class="customizing-checkout-single-image">
                {if $image !== $imageTemp}<img src="{$image}"/><br/>{/if}
                <a href="{$customizingUploadUrl}?download=1&file={$subValue|escape}">{$subValue|escape}</a>
            </div>
        {/foreach}
    {else}
        {$value.value|implode:', '|escape}
    {/if}
{elseif $value.type == 'color_select' || $value.type == 'color_field'}
    <div style="color:{$value.value}">
        {$value.value|escape}
        {if $value.description != ''}
            <span style="color:#111111;">:</span>
            <span style="color:#999999;">&nbsp;{$value.description|escape}</span>
        {/if}
    </div>
{else}
    <div>
        {$value.value|strip_tags|truncate:80|nl2br}
        {if $value.description != ''}
            <span style="color:#111111;">:</span>
            <span style="color:#999999;">&nbsp;{$value.description|strip_tags|truncate:80|nl2br}</span>
        {/if}
    </div>
{/if}
