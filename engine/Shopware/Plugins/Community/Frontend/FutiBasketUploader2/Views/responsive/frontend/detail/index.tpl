{*extends file=parent inside here, because the file is loaded over the file inheritance*}
{extends file="parent:frontend/detail/index.tpl"}

{*block name="frontend_detail_index_detail"}
    {include file="frontend/detail_info.tpl"}
{/block*}

{* Attributes fields *}
{block name='frontend_detail_data_attributes' append}
    {include file="frontend/detail_info.tpl"}
{/block}