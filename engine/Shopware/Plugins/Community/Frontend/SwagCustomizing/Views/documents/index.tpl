{extends file="parent:documents/index.tpl"}

{block name="document_index_table_name"}
    {if $position.customizingValues}
        <td align="left" width="48%" valign="top">
            {$position.name|nl2br}
            {foreach $position.customizingValues as $value}
                {if !$value.number}
                    <br>
                    <strong>{$value.name}:</strong>
                    {include file='frontend/plugins/swag_customizing/value.tpl'}
                {/if}
            {/foreach}
        </td>
    {elseif $position.customizing}
        <td align="left" width="48%" valign="top">
            {$position.name|nl2br}
            {if $position.name|strpos:': ' === false || $position.name|strlen > 60}
                <p>{include file='frontend/plugins/swag_customizing/value.tpl' value=$position.customizing}</p>
            {/if}
        </td>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
