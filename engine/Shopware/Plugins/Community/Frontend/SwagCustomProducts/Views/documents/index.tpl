{extends file='parent:documents/index.tpl'}

{* customize the order documents to point out the custom products options and values *}
{* CustomProducts modes: 2 = option, 3 = value *}

{block name="document_index_table_nr"}
    {if $position.attributes.swag_custom_products_mode === '2'}
        {block name="document_index_table_nr_swag_custom_products_option"}
            <td align="left" width="10%" valign="top" style="padding-left: 10px">
                {$position.articleordernumber|truncate:14:""}
            </td>
        {/block}
    {elseif $position.attributes.swag_custom_products_mode === '3'}
        {block name="document_index_table_nr_swag_custom_products_value"}
            <td align="left" width="10%" valign="top" style="padding-left: 20px">
                <i>{$position.articleordernumber|truncate:14:""}</i>
            </td>
        {/block}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="document_index_table_name"}
    {if $position.attributes.swag_custom_products_mode === '2'}
        {block name="document_index_table_name_swag_custom_products_option"}
            <td align="left" width="48%" valign="top" style="padding-left: 10px">
                {s name="DocumentIndexPositionNameDefault"}{$position.name|nl2br}{/s}:&nbsp;
                {block name="document_index_table_name_swag_custom_products_option_values"}
                    {if $customProductOptionValues[$position.id][$position.articleID]}
                        {$customProductOptionValues[$position.id][$position.articleID]|strip_tags|truncate: 40}
                    {/if}
                {/block}
            </td>
        {/block}
    {elseif $position.attributes.swag_custom_products_mode === '3'}
        {block name="document_index_table_name_swag_custom_products_value"}
            <td align="left" width="48%" valign="top" style="padding-left: 20px">
                <i>{s name="DocumentIndexPositionNameDefault"}{$position.name|nl2br}{/s}</i>
            </td>
        {/block}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
