{block name="customizing_form_options_radio_empty"}
    <div class="customizing--checkbox-empty customizing--is-description">
        {if $option.emptyText}
            {$option.emptyText}
        {else}
            {s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}
        {/if}
    </div>
{/block}

{block name='customizing_form_fields_wrapper_options_radio_values'}
    {foreach $option.values as $radio}
        {block name='customizing_form_fields_wrapper_options_radio_values_wrapper'}
            <div class="customizing--radio-wrapper">
                {block name='customizing_form_fields_wrapper_options_radio_values_wrapper_input'}
                    <input id="value{$radio.id}" type="radio" name="customizingValues[{$option.id}]" value="{$radio.id}" {if $currentSelectedValue == $radio.id} checked="checked"{/if}/>
                {/block}

                {block name='customizing_form_fields_wrapper_options_radio_values_wrapper_label'}
                    <label for="value{$radio.id}" class="customizing--radio-label">{$radio.value}</label>
                    {block name="customizing_form_options_radio_wrapper_text_description"}
                        <span class="check-wrapper--description">{$radio.description}</span>
                    {/block}
                {/block}
            </div>
        {/block}
    {/foreach}
{/block}
