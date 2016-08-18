{extends file='parent:frontend/detail/data.tpl'}

{block name="frontend_detail_data"}
    {$smarty.block.parent}

    {* Custom products frontend hook *}
    {block name="frontend_detail_data_swagcustomproducts"}
        {include file="frontend/swag_custom_products/detail/wrapper.tpl"}
    {/block}
{/block}
