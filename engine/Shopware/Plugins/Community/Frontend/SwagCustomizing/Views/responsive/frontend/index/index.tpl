{extends file='parent:frontend/index/index.tpl'}

{block name='frontend_index_header_javascript_jquery' prepend}
    {block name='frontend_index_customizing_header_javascript_jquery'}
        {include file="frontend/swag_customizing/index/jquery.tpl"}
    {/block}
{/block}
