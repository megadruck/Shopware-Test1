{namespace name='frontend/customizing/charges'}

{block name="frontend_detail_customizing_charges_icon_container"}
    <div class="customizing--charges-popup-icon">
        {block name="frontend_detail_customizing_charges_icon_text"}
            <span class="customizing--charges-info">{s name="CustomizingChargeInfoText"}Aufschläge{/s}</span>
        {/block}

        {block name="frontend_detail_customizing_charges_icon"}
            <i class="customizing--charges-info-icon icon--info2"></i>
        {/block}
    </div>
{/block}

{block name="frontend_detail_customizing_charges_panel"}
    <div class="customizing--charges-popup panel is--hidden has--border">
        {block name="frontend_detail_customizing_charges_panel_headline"}
            <h3 class="charges-popup--headline panel--title">{s name="CustomizingChargeHeadline"}Aufschläge{/s}</h3>
        {/block}

        {block name="frontend_detail_customizing_charges_panel_table"}
            <div class="customizing--charges-popup panel--table">
                {block name="frontend_detail_customizing_charges_panel_table_head"}
                    <div class="charges-popup--table-head panel--tr">
                        {block name="frontend_detail_customizing_charges_panel_table_head_quantity"}
                            <div class="table-head--quantity panel--th">
                                {s name="CustomizingChargeTableHeadQuantity"}Menge{/s}
                            </div>
                        {/block}
                        {block name="frontend_detail_customizing_charges_panel_table_head_value"}
                            <div class="table-head--value panel--th">
                                {s name="CustomizingChargeTableHeadValue"}Aufschlag{/s}
                            </div>
                        {/block}
                    </div>
                {/block}

                {foreach $charges as $valueId => $chargeItems}
                    {if $valueId}
                        <div class="customizing--table-row panel--tr">
                            <div class="customizing--row-quantity panel--td">
                                {foreach $option.values as $value}
                                    {if $value.id == $valueId}
                                        {* Adds the image for the image_select for surcharges *}
                                        {if $option.type.type eq 'image_select'}
                                            {include file="frontend/swag_customizing/image_selector_value.tpl"}
                                        {else}
                                            <span class="is--bold">{$value.description|default:$value.value}</span>
                                        {/if}
                                    {/if}
                                {/foreach}
                            </div>
                        </div>
                    {/if}
                    {foreach $chargeItems as $charge}
                        {block name="frontend_detail_customizing_charges_panel_table_row"}
                            <div class="customizing--table-row panel--tr">
                                {block name="frontend_detail_customizing_charges_panel_table_row_quantity"}
                                    <div class="customizing--row-quantity panel--td">
                                        {s name="CustomizingChargeTableTextFrom"}ab{/s} {$charge.from}
                                    </div>
                                {/block}

                                {block name="frontend_detail_customizing_charges_panel_table_row_value"}
                                    <div class="customizing--table-row-value panel--td">
                                        <strong>
                                            {if $charge.percentage}
                                                {$charge.value|number} %
                                            {else}
                                                {$charge.value|currency} {s name="Star" namespace="frontend/listing/box_article"}{/s}
                                            {/if}
                                        </strong>
                                    </div>
                                {/block}
                            </div>
                        {/block}
                    {/foreach}
                {/foreach}
            </div>
        {/block}
    </div>
{/block}
