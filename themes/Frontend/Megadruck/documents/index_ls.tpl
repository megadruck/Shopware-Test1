{extends file="documents/index.tpl"}
<div id="head_logo">
		{$Containers.Logo.value}
                {if $Document.deliveryDate}{s name="DocumentIndexDeliveryDate"}{/s} {$Document.deliveryDate}<br />{/if}
	</div>



{block name="document_index_table_head_tax"}
{/block}
{block name="document_index_table_head_price"}
{/block}
{block name="document_index_table_tax"}
{/block}
{block name="document_index_table_price"}
{/block}
{block name="document_index_amount"}
{/block}

{block name="document_index_head_bottom"}
	<h1>{s name="DocumentIndexShippingNumber"}{/s} {$Document.id}</h1>
	{s name="DocumentIndexPageCounter"}{/s}
{/block}
{block name="document_index_selectAdress"}
	{assign var="address" value="shipping"}
{/block}
{block name="document_index_table_each"}{if $position.modus == 0 || $position.modus == 1}{$smarty.block.parent}{/if}{/block}
{block name="document_index_head_right"}{s name="DocumentIndexDate"}Lieferscheindatum{/s} {$Document.date}<br />

{if $Document.bid}{s name="DocumentIndexInvoiceID"}{/s} {$Document.bid}<br />{/if}
{/block}

<table>
    <tr>
        <td colspan="2"><strong>Versandinfos:</strong></td>
    </tr>
    <tr>
        <td style="width:50%">Inhalt je Karton/Bund:</td><td>Menge auf Palette</td>
    </tr>
    <tr>
        <td>Anzahl Karton gesamt:</td><td>Paleten:</td></tr>
    <tr>
        <td>&nbsp;</td><td>Paletten getauscht:&nbsp;&nbsp; JA &nbsp;&nbsp;NEIN</td>
    </tr>
    <tr>
        <td colspan="2">Lieferung angenommen (Datum, Unterschrift):</td>
    </tr>
    <tr>
        <td coslpan="2"><strong>Die von uns mitgelieferten Europaletten bleiben unser Eigentum.<br>
                Die Rücksendung erbitten wir umgehend franko ab unsere Anschrift.</strong></td></tr>
    
</table>