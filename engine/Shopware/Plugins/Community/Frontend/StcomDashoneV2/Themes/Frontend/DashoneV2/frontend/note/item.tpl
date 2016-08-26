{extends file='parent:frontend/note/item.tpl'}


{* Article name *}
{block name="frontend_note_item_details_name"}
    <a class="note--title" href="{$detailLink}" title="{$sBasketItem.articleName|escape}">
        {$sBasketItem.articleName|truncate:40}
    </a>
{/block}
{block name="frontend_note_item_details_supplier"}{/block}
{block name="frontend_note_item_delivery"}{/block}