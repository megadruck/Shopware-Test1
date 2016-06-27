<select class="customizing--multi-select no-fancy" id="option{$option.id}" name="customizingValues[{$option.id}][]" multiple="multiple" size="5" data-no-fancy-select="true">
    {block name='customizing_form_fields_wrapper_options_multiple_select_values'}
        {if !$option.defaultValue}
            <option value="" selected="selected" disabled>{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}{/if}</option>
        {/if}

        {foreach $option.values as $value}
            {$checkedValue = null}
            {if is_array($currentSelectedValue)}
                {foreach $currentSelectedValue as $currValue}
                    {if $currValue == $value.id}
                        {$checkedValue = $currValue}
                    {/if}
                {/foreach}
            {else}
                {$checkedValue = $currentSelectedValue}
            {/if}
            <option value="{$value.id}"{if $checkedValue == $value.id} selected="selected"{/if}>{$value.value} {if $value.description}({$value.description}){/if}</option>
        {/foreach}
    {/block}
</select>
