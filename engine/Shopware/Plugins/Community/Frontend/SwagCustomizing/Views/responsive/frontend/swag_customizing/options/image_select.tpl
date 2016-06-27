{block name="customizing_form_options_image_empty"}
    <div class="customizing--image-empty customizing--is-description">
        {if $option.emptyText}
            {$option.emptyText}
        {else}
            {s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}
        {/if}
    </div>
{/block}

{block name='customizing_form_fields_wrapper_options_image_select_input'}
    <input id="option{$option.id}" class="customizing--image-select" type="hidden" name="customizingValues[{$option.id}]"{if $currentSelectedValue} value="{$currentSelectedValue}"{/if}/>
{/block}

{block name='customizing_form_fields_wrapper_options_image_select_values'}
    {foreach $option.values as $radio}
        {block name='customizing_form_fields_wrapper_options_image_select_wrapper'}
            <div class="customizing--image-selection-wrapper{if $radio.id == $currentSelectedValue} is-active{/if}">

                {block name='customizing_form_fields_wrapper_options_image_select_wrapper_checkbox'}
                    <input class="customizing--image-select-checkbox-input" id="value{$radio.id}"{if $radio.id == $currentSelectedValue} checked="checked"{/if} type="checkbox" value="{$radio.id}"/>
                {/block}

                {block name='customizing_form_fields_wrapper_options_image_select_wrapper_image'}
                    {$image = $radio.value}
                    {if !$isShopware51}
                        {$image = $radio.value|pathinfo}
                        {$image = "{$image.dirname}/thumbnail/{$image.filename}_{$customizingThumbnailSize}.{$image.extension}"}
                    {/if}
                    <img class="customizing--image-select-image" src="{link file=$image}" title="{$radio.description|escape}"/>
                {/block}
            </div>
        {/block}
    {/foreach}
{/block}
