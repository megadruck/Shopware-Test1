{block name='customizing_form_fields_wrapper_options_time_hours'}
    <select class="customizing--time-picker-hours" name="customizingValues[{$option.id}][hours]">
        {block name='customizing_form_fields_wrapper_options_time_hours_option'}
            <option class="emptyText" selected="selected" value="default">{s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}</option>
            <option value="24">00</option>
            {section name=hrs start=1 loop=24 step=1}
                <option value="{$smarty.section.hrs.index}"{if $currentSelectedValue.hours == $smarty.section.hrs.index} selected="selected"{/if}>{if strlen($smarty.section.hrs.index) eq 1}0{/if}{$smarty.section.hrs.index}</option>
            {/section}
        {/block}
    </select>
{/block}

{block name='customizing_form_fields_wrapper_options_time_seperator'}
    <span class="customizing--time-picker-separator">:</span>
{/block}

{block name='customizing_form_fields_wrapper_options_time_minutes'}
    <select class="customizing--time-picker-minutes" name="customizingValues[{$option.id}][minutes]">
        {block name='customizing_form_fields_wrapper_options_time_minutes_options'}
            <option class="emptyText" selected="selected" value="default">{s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}</option>
            {section name=mins start=0 step=1 loop=60}
                <option value="{$smarty.section.mins.index}"{if isset($currentSelectedValue) && $currentSelectedValue.minutes != 'default' && $currentSelectedValue.minutes == $smarty.section.mins.index} selected="selected"{/if}>{if strlen($smarty.section.mins.index) eq 1}0{/if}{$smarty.section.mins.index}</option>
            {/section}
        {/block}
    </select>
{/block}
