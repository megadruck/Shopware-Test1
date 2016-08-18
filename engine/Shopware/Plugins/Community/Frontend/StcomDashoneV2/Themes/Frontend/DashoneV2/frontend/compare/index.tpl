{extends file='parent:frontend/compare/index.tpl'}

{block name='frontend_top_navigation_menu_entry'}
    <i class="icon--compare"></i>
    {s name="CompareInfoCount"}{/s}
    <span class="badge compare--quantity">{$sComparisons|@count}</span>
{/block}