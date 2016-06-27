{extends file='parent:frontend/detail/data.tpl'}

{block name="frontend_detail_data"}
    {block name="frontend_detail_customizing_data"}
        {$smarty.block.parent}

        {include file="frontend/swag_customizing/detail/surcharges.tpl"}

        {if $customizingGroup}
            {include file="frontend/swag_customizing/detail/wrapper_form.tpl"}
        {/if}
    {/block}
{/block}

{block name='frontend_detail_data_delivery'}
    {block name='frontend_detail_customizing_data_delivery'}
        {if !$sCustomLicense}
            {$smarty.block.parent}
        {/if}
    {/block}
{/block}
