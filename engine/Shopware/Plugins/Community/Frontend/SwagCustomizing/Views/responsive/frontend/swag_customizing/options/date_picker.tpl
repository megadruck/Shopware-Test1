{block name='customizing_form_fields_wrapper_options_date_picker_text'}
    <input class="customizing--date-picker-text" type="text" id="option{$option.id}" placeholder="{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEmptyDate' namespace='frontend/detail/index'}Please select{/s}{/if}"
           name="customizingValues[{$option.id}][date]-show"{if $currentSelectedValue} value="{$currentSelectedValue.date}"{/if}
           data-date-picker="true" data-date-format="{$option.type.dateFormat}"/>
{/block}
{block name='customizing_form_fields_wrapper_options_date_picker_hidden'}
    <input type="hidden" id="option{$option.id}-submit"
           name="customizingValues[{$option.id}][date]"{if $currentSelectedValue} value="{$currentSelectedValue.date}"{/if} />
{/block}
