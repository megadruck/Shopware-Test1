{namespace name='frontend/customizing/charges'}

{$image = $value.value}

{if !$isShopware51}
    {$image = $value.value|pathinfo}
    {$image = "{$image.dirname}/thumbnail/{$image.filename}_{$customizingThumbnailSize}.{$image.extension}"}
{/if}

<img class="customizing--image-surcharge-image" src="{link file=$image}"/>
