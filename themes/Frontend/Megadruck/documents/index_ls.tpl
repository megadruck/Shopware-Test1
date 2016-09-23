{extends file="documents/index.tpl"}

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
	{$page+1}/{$Pages|@count}
{/block}
{block name="document_index_selectAdress"}
	{assign var="address" value="shipping"}
{/block}
{block name="document_index_table_each"}{if $position.modus == 0 || $position.modus == 1}{$smarty.block.parent}{/if}{/block}
{block name="document_index_head_right"}
	{if $Senderaddress.company} {$Senderaddress.company} <br /> {/if}
	{$Senderaddress.firstName} {$Senderaddress.lastName} <br />
	{$Senderaddress.street} <br />
	{$Senderaddress.zipCode} {$Senderaddress.city} <br />
	{$Senderaddress.country.name}<br /><br />
	{s name="DocumentIndexDate"}Lieferscheindatum{/s} {$Document.date}<br />
	{if $Document.deliveryDate}{s name="DocumentIndexDeliveryDate"}{/s} {$Document.deliveryDate}<br />{/if}
	{if $Document.bid}{s name="DocumentIndexInvoiceID"}{/s} {$Document.bid}<br />{/if}
{/block}
{block name="document_index_table_name"}
	<td align="left" width="48%" valign="top">
		{if $position.name == 'Versandkosten'}
			{s name="DocumentIndexPositionNameShippingCosts"}{$position.name}{/s}
		{else}
			{s name="DocumentIndexPositionNameDefault"}{$position.name|nl2br}{/s}
			{foreach $position.variation as $value}
				- {$value.name}: {$value.value}
			{/foreach}
		{/if}
	</td>
{/block}

{block name="document_index_info_ordercomment"}
	{if $Order._order.comment}
		<div style="font-size:11px;color:#333;">
			{s name="DocumentIndexComment"}{/s}
			{$Order._order.comment|replace:"€":"&euro;"}
		</div>
	{/if}
{/block}
{block name="document_index_info_dispatch"}
	{if $Order._dispatch.name}
		<div style="font-size:11px;color:#333;">
			{s name="DocumentIndexSelectedDispatch"}{/s}
			{$Order._dispatch.name}
		</div>
	{/if}
{/block}