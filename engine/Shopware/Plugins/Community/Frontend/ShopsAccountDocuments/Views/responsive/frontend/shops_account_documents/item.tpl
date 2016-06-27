{namespace name='frontend/account/index'}

<div class="order--item panel--tr">

    <div class="document--order panel--td column--id">
        <div class="column--label">
            {s name="ShopsAccountDocumentsTableHeadOrder"}{/s}:
        </div>
        <div class="column--value">
            {$item.ordernumber}
        </div>
    </div>

    <div class="document--type panel--td column--id is--bold">
        <div class="column--label">
            {s name="ShopsAccountDocumentsTableHeadType"}{/s}:
        </div>
        <div class="column--value">
            {if $item.type == 1}
                {s name="ShopsAccountDocumentsItemTypeInvoice"}Rechnung{/s}
            {elseif $item.type == 2}
                {s name="ShopsAccountDocumentsItemTypeDelivery"}Lieferschein{/s}
            {elseif $item.type == 3}
                {s name="ShopsAccountDocumentsItemTypeAdvice"}Gutschrift{/s}
            {else}
                {s name="ShopsAccountDocumentsItemTypeReversal"}Stornorechnung{/s}
            {/if}
        </div>

    </div>

    <div class="document--date panel--td column--id">
        <div class="column--label">
            {s name="ShopsAccountDocumentsTableHeadDate"}{/s}:
        </div>
        <div class="column--value">
            {$item.date|date_format:"%d.%m.%Y"}
        </div>
    </div>

    <div class="document--price panel--td column--id">
        <div class="column--label">
            {s name="ShopsAccountDocumentsTableHeadPrice"}{/s}:
        </div>
        <div class="column--value">
            {$item.amount|currency}
        </div>
    </div>

    <div class="document--actions panel--td column--id is--bold">
        <a class="btn" target="_blank" href="/ShopsAccountDocuments/openPdf?id={$item.hash}">Download als PDF</a>
    </div>

</div>
