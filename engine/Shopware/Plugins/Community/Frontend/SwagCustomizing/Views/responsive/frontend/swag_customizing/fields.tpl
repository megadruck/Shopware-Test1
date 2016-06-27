<div class="customizing--fields block-group">
    {foreach $customizingGroup.options as $option}
        {$currentSelectedValue = null}
        {if $customizingValues}
            {foreach $customizingValues as $group}
                {foreach $group as $optionKey => $optionValue}
                    {if $option.id == $optionKey}
                        {$currentSelectedValue = $optionValue}
                    {/if}
                {/foreach}
            {/foreach}
        {/if}

        {if is_null($currentSelectedValue) && $option.defaultValue}
            {$currentSelectedValue = $option.defaultValue}
        {/if}

        {block name='customizing_form_fields_wrapper block-group'}
            <div class="customizing--field-wrapper{if $option.required} required{/if} block">

                {block name='customizig_form_fields_wrapper_legend'}
                    <label class="customizing--option-label is--strong" for="option{$option.id}">{$option.name}{if $option.required} *{/if}:</label>
                {/block}

                {if $customizingCharges[$option.id]}
                    {block name='customizing_form_fields_wrapper_charges'}
                        {include file='frontend/swag_customizing/charges.tpl' charges=$customizingCharges[$option.id] option=$option}
                    {/block}
                {/if}

                {block name='customizing_form_fields_wrapper_options'}
                    <div class="customizing--option-values customizing--option-values-{$option.type.type|replace:'_':'-'}">
                        {if $option.type.type eq 'text_area'}
                            {block name='customizing_form_fields_wrapper_options_text_area'}
                                <textarea class="customizing--text-area" id="option{$option.id}" name="customizingValues[{$option.id}]" maxlength="{$option.validators.maxValue}" placeholder="{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEnterValue' namespace='frontend/detail/index'}Please enter text{/s}{/if}">{$currentSelectedValue}</textarea>
                            {/block}
                        {elseif $option.type.type eq 'text_field'}
                            {block name='customizing_form_fields_wrapper_options_text_field'}
                                <input class="customizing--text-field" type="text" id="option{$option.id}" name="customizingValues[{$option.id}]" value="{$currentSelectedValue}" maxlength="{$option.validators.maxValue}" placeholder="{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEnterValue' namespace='frontend/detail/index'}Please enter text{/s}{/if}"/>
                            {/block}
                        {elseif $option.type.type eq 'upload_file'}
                            {block name='customizing_form_fields_wrapper_options_file_upload'}
                                {block name="customizing_form_fields_wrapper_options_file_upload_dropzone"}
                                    <div class="customizing--fileupload-dropzone" data-type="file">
                                        <span class="customizing--inline-text">{s name="CustomizingUplaodDragAndDropText" namespace="frontend/detail/index"}Datei per Drag'n'Drop hochladen{/s}</span>
                                    </div>
                                {/block}
                                {block name="customizing_form_fields_wrapper_options_file_upload_inputs"}
                                    <input type="file" id="option{$option.id}" data-optionId="{$option.id}" data-type="file" class="customizing--fileupload-input" name="customizingValues[{$option.id}]" multiple="multiple"/>
                                    <button class="customizing--upload-btn btn is--small" data-type="file">Dateien auswählen</button>
                                {/block}
                            {/block}
                        {elseif $option.type.type eq 'upload_image'}
                            {block name='customizing_form_fields_wrapper_options_image_upload'}
                                {block name="customizing_form_fields_wrapper_options_image_upload_dropzone"}
                                    <div class="customizing--fileupload-dropzone" data-type="image">
                                        <span class="customizing--inline-text">{s name="CustomizingUplaodDragAndDropText" namespace="frontend/detail/index"}Datei per Drag'n'Drop hochladen{/s}</span>
                                    </div>
                                {/block}
                                {block name="customizing_form_fields_wrapper_options_image_upload_inputs"}
                                    <input type="file" id="option{$option.id}" data-optionId="{$option.id}" data-type="image" class="customizing--fileupload-input" name="customizingValues[{$option.id}]" multiple="multiple" accept="image/*"/>
                                    <button class="customizing--upload-btn btn is--small" data-type="image">Dateien auswählen</button>
                                {/block}
                            {/block}
                        {elseif $option.type.type eq 'select'}
                            {block name='customizing_form_fields_wrapper_options_select'}
                                {include file='frontend/swag_customizing/options/select.tpl'}
                            {/block}
                        {elseif $option.type.type eq 'multiple'}
                            {block name='customizing_form_fields_wrapper_options_multiple_select'}
                                {include file='frontend/swag_customizing/options/multiple_select.tpl'}
                            {/block}
                        {elseif $option.type.type eq 'radio'}
                            {block name='customizing_form_fields_wrapper_options_radio'}
                                {include file='frontend/swag_customizing/options/radio.tpl'}
                            {/block}
                        {elseif $option.type.type eq 'color_select'}
                            {block name='customizing_form_fields_wrapper_options_color_select'}
                                {include file='frontend/swag_customizing/options/color_select.tpl'}
                            {/block}
                        {elseif $option.type.type eq 'image_select'}
                            {block name='customizing_form_fields_wrapper_options_image_select'}
                                {include file='frontend/swag_customizing/options/image_select.tpl'}
                            {/block}
                        {elseif $option.type.type eq 'checkbox'}
                            {block name='customizing_form_fields_wrapper_options_checkbox'}
                                {include file='frontend/swag_customizing/options/checkbox.tpl'}
                            {/block}
                        {elseif $option.type.type eq 'date'}
                            {block name='customizing_form_fields_wrapper_options_date_picker'}
                                {include file='frontend/swag_customizing/options/date_picker.tpl'}
                            {/block}
                        {elseif $option.type.type eq 'date_time'}
                            {block name='customizing_form_fields_wrapper_options_date_time_picker'}
                                {include file='frontend/swag_customizing/options/date_time_picker.tpl'}
                            {/block}
                        {elseif $option.type.type eq 'time'}
                            {block name='customizing_form_fields_wrapper_options_time'}
                                {include file='frontend/swag_customizing/options/time.tpl'}
                            {/block}
                        {elseif $option.type.type eq 'color_field'}
                            {block name='customizing_form_fields_wrapper_options_color_field'}
                                <input class="customizing--color-field-input" type="text" placeholder="{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}{/if}" value="{$currentSelectedValue|escape}" id="option{$option.id}" name="customizingValues[{$option.id}]"/>
                            {/block}
                        {elseif $option.type.type eq 'text_html'}
                            {block name='customizing_form_fields_wrapper_options_wysiwyg'}
                                <textarea class="customizing--wysiwyg wysiwyg-input" id="option{$option.id}" name="customizingValues[{$option.id}]" placeholder="{$option.defaultValue|escape}">{$currentSelectedValue}</textarea>
                            {/block}
                        {/if}
                    </div>
                {/block}
            </div>
        {/block}
    {/foreach}
</div>
