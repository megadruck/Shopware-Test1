{extends file='parent:frontend/note/item.tpl'}

{* Remove article *}
{block name="frontend_note_item_delete"}
    <a href="{url controller='note' action='delete' sDelete=$sBasketItem.id}" title="{"{s name='NoteLinkDelete'}{/s}"|escape}" class="note--delete">
        <i class="icon--cross3"></i>
    </a>
{/block}