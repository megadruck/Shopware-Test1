{$image = $value.value}

{if !$isShopware51}
    {$image = $value.value|pathinfo}
    {$image = "{$image.dirname}/thumbnail/{$image.filename}_{$customizingThumbnailSize}.{$image.extension}"}
{/if}

<img class="select-image-surcharge" src="{link file=$image}" title="{$option.description|escape}"/>
<strong>{$value.description}</strong>
