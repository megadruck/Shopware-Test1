{extends file='parent:frontend/note/item.tpl'}


{* Article name *}
{block name="frontend_note_item_details_name"}
    <a class="note--title" href="{$detailLink}" title="{$sBasketItem.articleName|escape}">
        {$sBasketItem.articleName|truncate:40}
    </a>
{/block}
{block name="frontend_note_item_details_supplier"}{/block}
{block name="frontend_note_item_delivery"}{/block}

{* Remove article *}
{block name="frontend_note_item_delete"}
    <form action="{url controller='note' action='delete' sDelete=$sBasketItem.id}" method="post">
        <button type="submit" title="{"{s name='NoteLinkDelete'}{/s}"|escape}" class="note--delete">
            <i class="icon--cross"></i>
        </button>
    </form>
{/block}