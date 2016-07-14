{extends file='parent:frontend/index/index.tpl'}

{block name='frontend_index_no_script_message' prepend}
    {action module=widgets controller=StcomDashone action=index}
{/block}