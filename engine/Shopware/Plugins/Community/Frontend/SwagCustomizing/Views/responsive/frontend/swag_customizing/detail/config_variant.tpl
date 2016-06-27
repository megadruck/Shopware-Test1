<input type="radio"
       class="option--input"
       id="group[{$option.groupID}]"
       name="group[{$option.groupID}]"
       value="{$option.optionID}"
       title="{$option.optionname}"
       {if $customizingGroup.options || !$isShopware51 || !$theme.ajaxVariantSwitch}data-auto-submit="true" {else}data-ajax-select-variants="true"{/if}
       {if !$option.selectable}disabled="disabled"{/if}
       {if $option.selected && $option.selectable}checked="checked"{/if} />
