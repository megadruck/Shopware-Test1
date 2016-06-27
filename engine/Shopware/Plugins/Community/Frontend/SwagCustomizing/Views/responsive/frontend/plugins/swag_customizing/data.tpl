{* Be careful with whitespaces - Source code format impacts rendered text! *}
{foreach from=$sBasket.content item=basketItem}
{if $basketItem.customizingValues}{$basketItem.articlename} ({$basketItem.ordernumber}){": \n"}
{foreach $basketItem.customizingValues as $value}
- {$value.name}:{if $value.type == 'date'}
{$value.value|date:'DATE_LONG'|indent:2}
{elseif $value.type == 'date_time'}
{$value.value|date:'DATETIME'|indent:2}
{elseif $value.type == 'time'}
{$value.value|date:'TIME_LONG'|indent:2}
{elseif $value.type == 'image_select'}
{foreach $value.value as $subValue}
{$value.value|indent:2}
{/foreach}
{elseif $value.type == 'upload_image' || $value.type == 'upload_file'}
{foreach $value.value as $subValue}
{"{$uploadId}basket-{$basketItem.id}/{$subValue}"|indent:2}
{/foreach}
{else}
{$value.value|escape|indent:2}
{/if}
{/foreach}
{/if}
{/foreach}