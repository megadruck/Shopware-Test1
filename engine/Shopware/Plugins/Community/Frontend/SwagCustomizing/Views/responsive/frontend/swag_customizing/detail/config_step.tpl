<div class="field--select{if $groupID gt 0 && empty($sArticle.sConfigurator[$pregroupID].user_selected)} is--disabled{/if}">
    <span class="arrow"></span>
    <select{if $groupID gt 0 && empty($sArticle.sConfigurator[$pregroupID].user_selected)} disabled="disabled"{/if} name="group[{$sConfigurator.groupID}]"{if $customizingGroup.options || !$isShopware51 || !$theme.ajaxVariantSwitch} data-auto-submit="true"{else} data-ajax-select-variants="true"{/if}>

        {* Please select... *}
        {if empty($sConfigurator.user_selected)}
            <option value="" selected="selected">{s namespace="frontend/detail/article_config_step" name="DetailConfigValueSelect"}{/s}</option>
        {/if}

        {foreach from=$sConfigurator.values item=configValue name=option key=optionID}
            <option {if !$configValue.selectable}disabled{/if} {if $configValue.selected && $sConfigurator.user_selected} selected="selected"{/if} value="{$configValue.optionID}">
                {$configValue.optionname}{if $configValue.upprice && !$configValue.reset} {/if}
                {if !$configValue.selectable}{s name="DetailConfigValueNotAvailable"}{/s}{/if}
            </option>
        {/foreach}
    </select>
</div>
