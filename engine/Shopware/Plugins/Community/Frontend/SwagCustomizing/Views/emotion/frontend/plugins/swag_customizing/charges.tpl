<span class="icn-info">{s name="CustomizingChargeInfoText" namespace="frontend/customizing/charges"}Aufschläge{/s}</span>
<div class="customizing-charges-popup">
    <div class="arrow"></div>
    <div class="customizing-charges-inner-popup">
        <h3 class="customizing-charges-popup-headline">{s name="CustomizingChargeHeadline" namespace="frontend/customizing/charges"}Aufschläge{/s}</h3>

        <table>
            <thead>
            <tr>
                <th>{s name="CustomizingChargeTableHeadQuantity" namespace="frontend/customizing/charges"}Menge{/s}</th>
                <th>{s name="CustomizingChargeTableHeadValue" namespace="frontend/customizing/charges"}Aufschlag{/s}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $charges as $valueId => $chargeItems}
                {if $valueId}
                    <tr>
                        <td colspan="2">
                            {foreach $option.values as $value}{if $value.id == $valueId}
                                {if $option.type.type eq 'image_select'}
                                    {include file="frontend/plugins/swag_customizing/image_selector_value.tpl"}
                                {else}
                                    <strong>{$value.description|default:$value.value}</strong>
                                {/if}
                            {/if}{/foreach}
                        </td>
                    </tr>
                {/if}
                {foreach $chargeItems as $charge}
                    <tr>
                        <td>{s name="CustomizingChargeTableTextFrom" namespace="frontend/customizing/charges"}ab{/s} {$charge.from}
                        </td>
                        <td>
                            <strong>
                                {if $charge.percentage}
                                    {$charge.value|number} %
                                {else}
                                    {$charge.value|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
                                {/if}
                            </strong>
                        </td>
                    </tr>
                {/foreach}
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
