<select name="group[{$sConfigurator.groupID}]"{if $customizingGroup.options || !$isShopware51 || !$theme.ajaxVariantSwitch} data-auto-submit="true"{else} data-ajax-select-variants="true"{/if}>
    {foreach $sConfigurator.values as $configValue}
        {if !{config name=hideNoInStock} || ({config name=hideNoInStock} && $configValue.selectable)}
            <option{if $configValue.selected} selected="selected"{/if} value="{$configValue.optionID}">
                {$configValue.optionname}{if $configValue.upprice} {if $configValue.upprice > 0}{/if}{/if}
            </option>
        {/if}
    {/foreach}
</select>
