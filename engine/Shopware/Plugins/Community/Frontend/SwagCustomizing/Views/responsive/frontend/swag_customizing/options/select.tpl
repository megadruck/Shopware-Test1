<select id="option{$option.id}" name="customizingValues[{$option.id}]" class="customizing--select-input">
    {block name='customizing_form_fields_wrapper_options_select_value'}
        <option class="select-input--empty-text"
                id="option--default"
                selected="selected"
                disabled="disabled">
            {if $option.emptyText}
                {$option.emptyText}
            {else}
                {s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}
            {/if}
        </option>
        {foreach $option.values as $value}
            <option value="{$value.id}"
                {if $currentSelectedValue == $value.id}
                selected="selected"
                {/if}>
                {$value.value} {if $value.description} ({$value.description}){/if}
            </option>
        {/foreach}
    {/block}
</select>

{if !$option.required}
    <a class="btn option--reset"
       title="{s namespace="frontend/detail/index" name="CustomizingResetSelection"}Reset selection{/s}"
       id="{$option.id}">X
    </a>
{/if}
