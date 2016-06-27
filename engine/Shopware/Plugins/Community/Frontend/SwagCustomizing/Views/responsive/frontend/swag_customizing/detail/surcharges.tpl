{block name='frontend_detail_data_customizing_surcharges'}
    <div class="customizing--charges-popup customizing--charges-panel panel has--border{if !($customizingGroup && $customizingSurcharge)} is--hidden{/if}">
        <h3 class="charges-popup--headline panel--title">{s name="CustomizingChargeTableHeadValue" namespace="frontend/customizing/charges"}Aufschlag{/s}</h3>
        <div class="customizing--charges-popup panel--table">
            <div class="charges-popup--table-head panel--tr">
                <div class="table-head--quantity panel--th">
                    {s name="CustomizingChargeTableOptionName" namespace="frontend/customizing/charges"}Option name{/s}
                </div>
                <div class="table-head--value panel--th">
                    {s name="CustomizingChargeTablePrice" namespace="frontend/customizing/charges"}Price{/s}
                </div>
            </div>
            {foreach from=$customizingSurcharge.surcharge key=valueId item=surcharge}
                <div class="customizing--table-row panel--tr">
                    <div class="customizing--row-name panel--td">
                        {$surcharge.name}
                    </div>
                    <div class="customizing--table-row-value panel--td">
                        <strong>
                            {$surcharge.surcharge|currency}*
                        </strong>
                    </div>
                </div>
            {/foreach}
            <div class="customizing--table-row panel--tr total">
                <div class="customizing--row-name panel--td">
                    {s name="CustomizingChargeTableTotal" namespace="frontend/customizing/charges"}Total{/s}
                </div>
                <div class="customizing--table-row-value panel--td">
                    <strong>
                        {$customizingSurcharge.total|currency}*
                    </strong>
                </div>
            </div>
        </div>
    </div>
{/block}
