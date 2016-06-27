{block name="customizing_form_options_checkbox_empty"}
    <div class="customizing--checkbox-empty customizing--is-description">
        {if $option.emptyText}
            {$option.emptyText}
        {else}
            {s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}
        {/if}
    </div>
{/block}

{foreach $option.values as $check}
    {$checkedValue = null}
    {if is_array($currentSelectedValue)}
        {foreach $currentSelectedValue as $currValue}
            {if $currValue == $check.id}
                {$checkedValue = $currValue}
            {/if}
        {/foreach}
    {else}
        {$checkedValue = $currentSelectedValue}
    {/if}

    {block name='customizing_form_options_checkbox_wrapper'}
        <div class="customizing--check-wrapper">
            {block name='customizing_form_options_checkbox_wrapper_input'}
                <input id="value{$check.id}" type="checkbox" name="customizingValues[{$option.id}][]" value="{$check.id}" {if $checkedValue == $check.id} checked="checked"{/if}/>
            {/block}

            {block name='customizing_form_options_checkbox_wrapper_text'}
                <label for="value{$check.id}" class="check-wrapper--check-label">{$check.value}</label>
                {block name="customizing_form_options_checkbox_wrapper_text_description"}
                    <span class="check-wrapper--description">
						{$check.description|escape}
					</span>
                {/block}
            {/block}
        </div>
    {/block}
{/foreach}
