{block name='customizing_form_fields_wrapper_options_color_select_input'}
    <input id="option{$option.id}" class="customizing--color-select" type="hidden" name="customizingValues[{$option.id}]" value="{$currentSelectedValue}"/>
{/block}

{block name='customizing_form_fields_wrapper_options_color_select_values'}
    {foreach $option.values as $radio}
        {block name='customizing_form_fields_wrapper_options_color_select_values_value'}
            <div id="value{$radio.id}" class="customizing--color-select-swatch{if $radio@first} swatch-first{/if}{if $radio@last} swatch-last{/if} {if $currentSelectedValue == $radio.id} is-active{/if}" style="background-color:{$radio.value|escape}" data-value="{$radio.id}"{if $radio.description} data-tiptip="{$radio.description|escape}"{/if}></div>
        {/block}
    {/foreach}
{/block}
