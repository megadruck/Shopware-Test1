{block name='customizing_form_fields_wrapper_options_date_time_picker_text'}
    {assign var=getTime value=" "|explode:$option.defaultValue}
    {assign var=defaultTime value=":"|explode:$getTime.1}
    <input class="customizing--date-time-picker-text" type="text" id="option{$option.id}" name="customizingValues[{$option.id}][date]-show"{if $option.defaultValue} value="{$option.defaultValue|date:"DATE_SHORT"}"{/if} class="date-picker" data-date-picker="true" data-date-format="{$option.type.dateFormat}"/>
{/block}

{block name='customizing_form_fields_wrapper_options_date_time_picker_hidden'}
    <input type="hidden" id="option{$option.id}-submit" name="customizingValues[{$option.id}][date]"{if $option.defaultValue} value="{$option.defaultValue|date:"DATE_MEDIUM"}"{/if} />
{/block}

{block name="customizing_form_fields_wrapper_options_date_time_picker_container"}
    <div class="customizing--date-time-picker-select-time-container">
        {block name='customizing_form_fields_wrapper_options_date_time_picker_hours'}
            <select class="customizing--date-time-picker-hours" name="customizingValues[{$option.id}][hours]">

                {block name='customizing_form_fields_wrapper_options_date_time_picker_hours_options'}
                    <option value="24">00</option>
                    {section name=hrs start=1 loop=24 step=1}
                        <option value="{$smarty.section.hrs.index}"{if $defaultTime.0 == $smarty.section.hrs.index} selected="selected"{/if}>{if strlen($smarty.section.hrs.index) eq 1}0{/if}{$smarty.section.hrs.index}</option>
                    {/section}
                {/block}

            </select>
        {/block}

        {block name='customizing_form_fields_wrapper_options_date_time_picker_seperator'}
            <span class="customizing--date-time-picker-separator">:</span>
        {/block}

        {block name='customizing_form_fields_wrapper_options_date_time_picker_minutes'}
            <select class="customizing--date-time-picker-minutes" name="customizingValues[{$option.id}][minutes]">

                {block name='customizing_form_fields_wrapper_options_date_time_picker_minutes_options'}
                    <option value="60">00</option>
                    {section name=mins start=1 step=1 loop=60}
                        <option value="{$smarty.section.mins.index}"{if $defaultTime.1 == $smarty.section.mins.index} selected="selected"{/if}>{if strlen($smarty.section.mins.index) eq 1}0{/if}{$smarty.section.mins.index}</option>
                    {/section}
                {/block}

            </select>
        {/block}
    </div>
{/block}
