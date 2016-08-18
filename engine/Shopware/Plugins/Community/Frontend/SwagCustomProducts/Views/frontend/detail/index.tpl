{extends file='parent:frontend/detail/index.tpl'}

{block name='frontend_index_content'}
    {block name='frontend_index_content_swag_custom_products'}
        {include file='frontend/swag_custom_products/detail/index.tpl'}
    {/block}
    {$smarty.block.parent}
{/block}
