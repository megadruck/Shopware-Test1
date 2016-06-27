{block name="frontend_detail_data_price_info" prepend}
    <div class="customizingSurcharge" {if !($customizingGroup && $customizingSurcharge)}style="display: none;"{/if}>
        <h5 class="bold">{s name="CustomizingChargeTableHeadValue" namespace="frontend/customizing/charges"}Aufschlag{/s}</h5>
        <table width="220" border="0" cellspacing="0" cellpadding="0" class="text">
            <thead>
            <tr>
                <td width="160">
                    <strong>{s name="CustomizingChargeTableOptionName" namespace="frontend/customizing/charges"}Option name{/s}</strong>
                </td>
                <td width="140">
                    <strong>{s name="CustomizingChargeTablePrice" namespace="frontend/customizing/charges"}Price{/s}</strong>
                </td>
            </tr>
            </thead>
            <tfoot>
            <tr id="customizingTotal">
                <td width="160">{s name="CustomizingChargeTableTotal" namespace="frontend/customizing/charges"}Total{/s}</td>
                <td width="140"><strong>{$customizingSurcharge.total|currency}*</strong></td>
            </tr>
            </tfoot>
            <tbody>
            {foreach from=$customizingSurcharge.surcharge key=valueId item=surcharge}
                <tr id="surchargeOptionId-{$valueId}">
                    <td class="name">{$surcharge.name}</td>
                    <td class="surcharge"><strong>{$surcharge.surcharge|currency}*</strong></td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/block}

{block name="frontend_detail_data"}
    {$valueMaxLength = {config name=valueStringMaxLength} + 4}
    {$smarty.block.parent}
    {if $customizingGroup}
        <div class="customizing-data-wrapper">
            <form name="customizingOptions" method="post" action="">
                <fieldset class="customizing-fieldset">
                    {if $recalculateProductPrice}
                        <input type="hidden" name="price" value="{$sArticle.price}">
                        <input type="hidden" name="currentQuantity" value="1">
                    {/if}
                    <legend class="customizing-legend">
                        {if $customizingGroup.showName}
                            {$customizingGroup.name}
                        {/if}
                    </legend>
                    {if $customizingGroup.showGroupImage}
                        <img width="100%" src="{$customizingGroup.imagePath}"/>
                    {/if}
                    {if $customizingGroup.description && $customizingGroup.showDescription}
                        <p class="customizing-description">{$customizingGroup.description}</p>
                    {/if}
                    <ul class="customizing-fields">
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
                            <li class="customizing-field-wrapper{if $option.required} required{/if}">
                                <label class="option_label" for="option{$option.id}">{$option.name}{if $option.required}*{/if}:</label>
                                {if $customizingCharges[$option.id]}
                                    {include file='frontend/plugins/swag_customizing/charges.tpl' charges=$customizingCharges[$option.id]}
                                {/if}
                                <div class="option_values option_values_{$option.type.type}" id="option_value_{$option.id}">
                                    {* Text area *}
                                    {if $option.type.type eq 'text_area'}
                                        <textarea id="option{$option.id}" name="customizingValues[{$option.id}]" maxlength="{$option.validators.maxValue}" placeholder="{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEnterValue' namespace='frontend/detail/index'}Please enter text{/s}{/if}">{$currentSelectedValue}</textarea>
                                        {* Normal text field *}
                                    {elseif $option.type.type eq 'text_field'}
                                        <input type="text" id="option{$option.id}" name="customizingValues[{$option.id}]" value="{$currentSelectedValue}" maxlength="{$option.validators.maxValue}" placeholder="{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEnterValue' namespace='frontend/detail/index'}Please enter text{/s}{/if}"/>
                                        {* File upload *}
                                    {elseif $option.type.type eq 'upload_file'}
                                        <div class="fileupload-dropzone" data-type="file" data-value="{$allowedFilesExtensions}">
                                            <div class="inline-text">
                                                {s name='CustomizingUploadDragAndDropText' namespace='frontend/detail/index'}{/s}
                                            </div>
                                        </div>
                                        <input type="file" data-type="file" data-value="{$allowedFilesExtensions}" id="option{$option.id}" class="fileupload-input" name="customizingValues[{$option.id}]" multiple="multiple"/>
                                        {* Image upload *}
                                    {elseif $option.type.type eq 'upload_image'}
                                        <div class="fileupload-dropzone" data-type="image" data-value="{$allowedImageExtensions}">
                                            <div class="inline-text">
                                                {s name='CustomizingUploadDragAndDropText' namespace='frontend/detail/index'}{/s}
                                            </div>
                                        </div>
                                        <input type="file" data-type="image" data-value="{$allowedImageExtensions}" id="option{$option.id}" class="fileupload-input" name="customizingValues[{$option.id}]" multiple="multiple" accept="image/*"/>
                                        {* Normal select box *}
                                    {elseif $option.type.type eq 'select'}
                                        <div class="customizing-separator"></div>
                                        <select id="option{$option.id}" name="customizingValues[{$option.id}]" class="select-input">
                                            <option value="" selected="selected" id="option-default">{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}{/if}</option>

                                            {foreach $option.values as $value}
                                                {if $value.description}
                                                    {$displayValue="{$value.value} ({$value.description})"|truncate:$valueMaxLength:"...":true}
                                                {else}
                                                    {$displayValue="{$value.value|truncate:$valueMaxLength:"...":true}" }
                                                {/if}
                                                <option value="{$value.id}"{if $currentSelectedValue == $value.id} selected="selected"{/if} title="{$value.value}{if $value.description} ({$value.description}){/if}">{$displayValue}</option>
                                            {/foreach}
                                        </select>
                                        {if !$option.required}
                                            <input type="button" class="button-middle small bold option-reset"
                                                   title="{s namespace="frontend/detail/index" name="CustomizingResetSelection"}Reset selection{/s}"
                                                   id="{$option.id}" value="X">
                                        {/if}
                                        {* Multiple select box *}
                                    {elseif $option.type.type eq 'multiple'}
                                        <select class="no-fancy" id="option{$option.id}" name="customizingValues[{$option.id}][]" multiple="multiple" size="5">
                                            {if !$option.defaultValue}
                                                <option value="" selected="selected" disabled>{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}{/if}</option>
                                            {/if}
                                            {foreach $option.values as $value}
                                                {if $value.description}
                                                    {$displayValue="{$value.value} ({$value.description})"|truncate:$valueMaxLength:"...":true}
                                                {else}
                                                    {$displayValue="{$value.value|truncate:$valueMaxLength:"...":true}" }
                                                {/if}
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
                                                <option value="{$value.id}"{if $checkedValue == $value.id} selected="selected"{/if} title="{$value.value}{if $value.description} ({$value.description}){/if}">{$displayValue}</option>
                                            {/foreach}
                                        </select>
                                        {* Radio boxes *}
                                    {elseif $option.type.type eq 'radio'}
                                        {if !$option.defaultValue}
                                            <div class="radio-wrapper">
                                                {if $option.emptyText}
                                                    {$option.emptyText}
                                                {else}
                                                    {s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}
                                                {/if}
                                            </div>
                                        {/if}
                                        {foreach $option.values as $radio}
                                            {if $radio.description}
                                                {$displayValue="{$radio.value} ({$radio.description})"|truncate:$valueMaxLength:"...":true}
                                            {else}
                                                {$displayValue="{$radio.value|truncate:$valueMaxLength:"...":true}"}
                                            {/if}
                                            <div class="radio-wrapper">
                                                <input id="value{$radio.id}" type="radio" name="customizingValues[{$option.id}]" value="{$radio.id}"{if $currentSelectedValue == $radio.id} checked="checked"{/if}/>
                                                <label for="value{$radio.id}" class="radio-label" title="{$radio.value}{if $radio.description} ({$radio.description}){/if}">{$displayValue}</label>
                                            </div>
                                        {/foreach}
                                        <div class="clear"></div>
                                        {* Color select *}
                                    {elseif $option.type.type eq 'color_select'}
                                        {if !$option.defaultValue}
                                            <div class="empty">
                                                {if $option.emptyText}
                                                    {$option.emptyText}
                                                {else}
                                                    {s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}
                                                {/if}
                                            </div>
                                        {/if}
                                        <input id="option{$option.id}" class="color-select" type="hidden" name="customizingValues[{$option.id}]" value="{$currentSelectedValue}"/>
                                        {foreach $option.values as $radio}
                                            <div id="value{$radio.id}" class="color-select-swatch{if $radio@first} swatch-first{/if}{if $radio@last} swatch-last{/if}{if $currentSelectedValue == $radio.id} is-active{/if}" style="background-color:{$radio.value|escape}" data-value="{$radio.id}"{if $radio.description} data-tiptip="{$radio.description|escape}"{/if}></div>
                                        {/foreach}
                                        <div class="clear"></div>
                                        {* Image selection *}
                                    {elseif $option.type.type eq 'image_select'}
                                        {assign var="divSize" value="x"|explode:$customizingThumbnailSize}
                                        {if !$option.defaultValue}
                                            <div class="empty">
                                                {if $option.emptyText}
                                                    {$option.emptyText}
                                                {else}
                                                    {s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}
                                                {/if}
                                            </div>
                                        {/if}
                                        <input id="option{$option.id}" class="image-select" type="hidden" name="customizingValues[{$option.id}]" value="{$currentSelectedValue}"/>
                                        {foreach $option.values as $radio}
                                            {* Set the new size, if an description is available (see if-clause in the styling attribute)*}
                                            <div class="image-selection-wrapper{if $currentSelectedValue == $radio.id} is-active{/if}"{if $radio.description} data-tiptip="{$radio.description|escape}"{/if} style="float:left; {if $displayImageSelectionDescription == 1}height: {$divSize[1] + 100}px;{/if}">
                                                <div class="checkbox-selection-wrapper">
                                                    <input id="value{$radio.id}" type="checkbox" value="{$radio.id}"{if $radio.id == $currentSelectedValue} checked="checked"{/if} />
                                                </div>

                                                {$image = $radio.value}
                                                {if !$isShopware51}
                                                    {$image = $radio.value|pathinfo}
                                                    {$image = "{$image.dirname}/thumbnail/{$image.filename}_{$customizingThumbnailSize}.{$image.extension}"}
                                                {/if}

                                                <img src="{link file=$image}" title="{$radio.description|escape}"/>

                                                {*Check wether the description should be shown or not*}
                                                {if $displayImageSelectionDescription == 1}
                                                    {*Append the image description.*}
                                                    <div style="padding-left:5%; padding-right: 5%">
                                                        <br/>
                                                        <h5>{s name='CustomizingImageSelectionDescription' namespace='frontend/detail/index'}{/s}</h5>
                                                        <br/>
                                                        {if $radio.description|count_characters:true > 0}
                                                            <p>{$radio.description|escape}</p>
                                                        {else}
                                                            {* No description provided, now use the empty description *}
                                                            <p style="font-style: italic"></p>
                                                        {/if}
                                                        <br/>
                                                    </div>
                                                {/if}
                                            </div>
                                        {/foreach}
                                        <div class="clear"></div>
                                        {* Check boxes *}
                                    {elseif $option.type.type eq 'checkbox'}
                                        <div class="empty">
                                            {if $option.emptyText}
                                                {$option.emptyText}
                                            {else}
                                                {s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}
                                            {/if}
                                        </div>
                                        {foreach $option.values as $check}
                                            {if $check.description}
                                                {$displayValue="{$check.value} ({$check.description})"|truncate:$valueMaxLength:"...":true}
                                            {else}
                                                {$displayValue="{$check.value|truncate:$valueMaxLength:"...":true}"}
                                            {/if}
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
                                            <div class="check-wrapper">
                                                <input id="value{$check.id}" type="checkbox" name="customizingValues[{$option.id}][]" value="{$check.id}"{if $checkedValue == $check.id} checked="checked"{/if} />
                                                <label for="value{$check.id}" class="check-label" title="{$check.value}{if $check.description} ({$check.description}){/if}">{$displayValue}</label>
                                            </div>
                                        {/foreach}

                                        {* Date picker *}
                                    {elseif $option.type.type eq 'date'}
                                        <input type="text" id="option{$option.id}" name="customizingValues[{$option.id}][date]-show" {if $currentSelectedValue}value="{$currentSelectedValue.date}"{else} placeholder="{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEmptyDate' namespace='frontend/detail/index'}Please select{/s}{/if}"{/if} class="date-picker" data-date-picker="true" data-date-format="{$option.type.dateFormat}"/>
                                        <input type="hidden" id="option{$option.id}-submit" name="customizingValues[{$option.id}][date]" {if $currentSelectedValue}value="{$currentSelectedValue.date}"{/if}/>
                                        {* Date and time fields *}
                                    {elseif $option.type.type eq 'date_time'}
                                        <input type="text" id="option{$option.id}" name="customizingValues[{$option.id}][date]-show"{if $option.defaultValue} value="{$option.defaultValue|date:"DATE_SHORT"}"{/if} class="date-picker" data-date-picker="true" data-date-format="{$option.type.dateFormat}"/>
                                        <input type="hidden" id="option{$option.id}-submit" name="customizingValues[{$option.id}][date]"{if $option.defaultValue} value="{$option.defaultValue|date:"dd.MM.yyyy"}"{/if} />
                                        <div class="clear"></div>
                                        {assign var=getTime value=" "|explode:$option.defaultValue}
                                        {assign var=defaultTime value=":"|explode:$getTime.1}
                                        <select name="customizingValues[{$option.id}][hours]">
                                            <option value="24">00</option>
                                            {section name=hrs start=1 loop=24 step=1}
                                                <option value="{$smarty.section.hrs.index}"{if $defaultTime.0 == $smarty.section.hrs.index} selected="selected"{/if}>{if strlen($smarty.section.hrs.index) eq 1}0{/if}{$smarty.section.hrs.index}</option>
                                            {/section}
                                        </select>
                                        <span class="sep">:</span>
                                        <select name="customizingValues[{$option.id}][minutes]">
                                            {section name=mins start=0 step=1 loop=60}
                                                <option value="{$smarty.section.mins.index}"{if $defaultTime.1 == $smarty.section.mins.index} selected="selected"{/if}>{if strlen($smarty.section.mins.index) eq 1}0{/if}{$smarty.section.mins.index}</option>
                                            {/section}
                                        </select>
                                        {* Time field *}
                                    {elseif $option.type.type eq 'time'}
                                        <select name="customizingValues[{$option.id}][hours]">
                                            <option class="emptyText" selected="selected" value="default">{s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}</option>
                                            <option value="24">00</option>
                                            {section name=hrs start=1 loop=24 step=1}
                                                <option value="{$smarty.section.hrs.index}"{if $currentSelectedValue.hours == $smarty.section.hrs.index} selected="selected"{/if}>{if strlen($smarty.section.hrs.index) eq 1}0{/if}{$smarty.section.hrs.index}</option>
                                            {/section}
                                        </select>
                                        <span class="sep">:</span>
                                        <select name="customizingValues[{$option.id}][minutes]">
                                            <option class="emptyText" selected="selected" value="default">{s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}</option>
                                            {section name=mins start=0 step=1 loop=60}
                                                <option value="{$smarty.section.mins.index}"{if isset($currentSelectedValue) && $currentSelectedValue.minutes != 'default' && $currentSelectedValue.minutes == $smarty.section.mins.index} selected="selected"{/if}>{if strlen($smarty.section.mins.index) eq 1}0{/if}{$smarty.section.mins.index}</option>
                                            {/section}
                                        </select>
                                        {* Color field *}
                                    {elseif $option.type.type eq 'color_field'}
                                        <div class="inline-content">#</div>
                                        <input type="text" value="{$currentSelectedValue|escape}" id="option{$option.id}" name="customizingValues[{$option.id}]" placeholder="{if $option.emptyText}{$option.emptyText}{else}{s name='CustomizingEmptyValue' namespace='frontend/detail/index'}Please choose{/s}{/if}"/>
                                        {* WYSIWYG editor *}
                                    {elseif $option.type.type eq 'text_html'}
                                        <textarea class="wysiwyg-input" id="option{$option.id}" name="customizingValues[{$option.id}]">{$currentSelectedValue}</textarea>
                                    {/if}
                                </div>
                            </li>
                        {/foreach}
                    </ul>
                </fieldset>
            </form>
        </div>
    {/if}
{/block}

{block name='frontend_detail_buy_button' append}
    <div class="ajax_loader"></div>
{/block}
