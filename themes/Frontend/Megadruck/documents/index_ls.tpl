<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; utf-8">
	<meta name="author" content=""/>
	<meta name="copyright" content="" />

	<title></title>
	<style type="text/css">
		body {
		{$Containers.Body.style}
		}

		div#head_logo {
		{$Containers.Logo.style}
		}

		#adressfield {
			padding:4px;

		}

		div#header {
		{$Containers.Header.style}
		}

		div#head_left {
		{$Containers.Header_Box_Left.style}
		}

		div#head_right {
		{$Containers.Header_Box_Right.style}

		}

		div#head_bottom {
		{$Containers.Header_Box_Bottom.style}
		}
		#head_bottom h4{
			padding-bottom: -10px;
		}
		div#content {
		{$Containers.Content.style}
		}

		td {
		{$Containers.Td.style}
		}

		td.name {
		{$Containers.Td_Name.style}
		}

		td.line {
		{$Containers.Td_Line.style}
		}

		td.head  {
		{$Containers.Td_Head.style}
		}

		#footer {
		{$Containers.Footer.style}
		}
		.footerinfo{
			border: 1px solid black;
			background-color: #f3f1f1;
			width: 100%;
			border-collapse: collapse;

		}
		.footerinfo table,
		.footerinfo th,
		.footerinfo td
		{
			border: 1px solid black;
		}
		.footerinfo td{
			padding:5px;
		}
		#amount {
		{$Containers.Content_Amount.style}
		}

		.sender {
		{$Containers.Header_Sender.style}
		}
		.sender p {
			margin:0;
			padding:0;
		}
		.recipient{
		{$Containers.Header_Recipient.style}
		}

		#info {
		{$Containers.Content_Info.style}
		}
	</style>

<body>

{foreach from=$Pages item=postions name="pagingLoop" key=page}

	{block name="document_index_selectAdress"}
		{assign var="address" value="shipping"}
	{/block}
	<div id="head_bottom" style="clear:both">
		{block name="document_index_head_bottom"}
			<h4>{s name="DocumentIndexShippingNumber"}Lieferschein Nr.{/s} {$Document.id}</h4>
			<p>{s name="DocumentIndexOrderID"}Auftrags-Nr.:{/s} {$Order._order.ordernumber}</p>
		{/block}
	</div>
	<div id="header">
		<div id="head_left">
			{if $smarty.foreach.pagingLoop.first}

				<div id="adressfield">
					<div class="sender">
					<p class="sender">{$Containers.Header_Sender.value}</p>
						{if $Senderaddress.company} {$Senderaddress.company} <br /> {/if}
						{$Senderaddress.firstName} {$Senderaddress.lastName} <br />
						{$Senderaddress.street} <br />
						{$Senderaddress.zipCode} {$Senderaddress.city} <br />
						{$Senderaddress.country.name}<br /><br />
					</div>
					<div class="recipient">
						<p class="sender">{$Containers.Header_Recipient.value}</p>
						{$User.$address.company}<br />
						{$User.$address.salutation|salutation}
						{if {config name="displayprofiletitle"}}
							{$User.$address.title}<br/>
						{/if}
						{$User.$address.firstname} {$User.$address.lastname}<br />
						{$User.$address.street}<br />
						{block name="document_index_address_additionalAddressLines"}
							{if {config name=showAdditionAddressLine1}}
								{$User.$address.additional_address_line1}<br />
							{/if}
							{if {config name=showAdditionAddressLine2}}
								{$User.$address.additional_address_line2}<br />
							{/if}
						{/block}
						{block name="document_index_address_cityZip"}
							{if {config name=showZipBeforeCity}}
								{$User.$address.zipcode} {$User.$address.city}<br />
							{else}
								{$User.$address.city} {$User.$address.zipcode}<br />
							{/if}
						{/block}
						{block name="document_index_address_countryData"}
							{if $User.$address.state.shortcode}{$User.$address.state.shortcode} - {/if}{$User.$address.country.countryen}<br />
						{/block}
					</div>
				</div>
			{/if}
		</div>
		<div id="head_right">

			{block name="document_index_head_right"}
				{s name="DocumentIndexDate"}Datum:{/s} {$Document.date}<br />
				{if $Document.deliveryDate}{s name="DocumentIndexDeliveryDate"}Lieferdatum:{/s} <strong>{$Document.deliveryDate}</strong><br />{/if}
				{s name="DocumentIndexCustomerID"}{/s} {$User.billing.customernumber|string_format:"%06d"}<br />
				{if $User.billing.ustid}{s name="DocumentIndexUstID"}{/s} {$User.billing.ustid|replace:" ":""|replace:"-":""}<br />{/if}
				{$page+1}/{$Pages|@count}<br /><br />


			{/block}
			{block name="document_index_info_net"}{/block}
			{block name="document_index_info_dispatch"}{/block}
		</div>

	</div>



	<div id="content">
		<table cellpadding="0" cellspacing="0" width="98%" align="center">
			<tbody valign="top">
			<tr>
				{block name="document_index_table_head_pos"}
					<td align="left" width="5%" class="head">
						<strong>{s name="DocumentIndexHeadPosition"}Pos.{/s}</strong>
					</td>
				{/block}
				{block name="document_index_table_head_nr"}
					<td align="left" width="10%" class="head">
						<strong>{s name="DocumentIndexHeadArticleID"}Artikel-Nr.{/s}</strong>
					</td>
				{/block}
				{block name="document_index_table_head_name"}
					<td align="left" width="48%" class="head">
						<strong>{s name="DocumentIndexHeadName"}Artikel{/s}</strong>
					</td>
				{/block}
				{block name="document_index_table_head_quantity"}
					<td align="right" width="5%" class="head">
						<strong>{s name="DocumentIndexHeadQuantity"}Menge{/s}</strong>
					</td>
				{/block}
				{block name="document_index_table_head_tax"}{/block}
				{block name="document_index_table_head_price"}{/block}
			</tr>
			{foreach from=$postions item=position key=number}
				{if $position.name != 'Versandkosten' AND $position.articleID != '0'}
				{block name="document_index_table_each"}
					<tr>
						{block name="document_index_table_pos"}
							<td align="left" width="5%" valign="top">{assign var="z" value=$number}
								&nbsp;{$number+1}
							</td>
						{/block}
						{block name="document_index_table_nr"}
							<td align="left" width="10%" valign="top">
								{$position.articleordernumber|truncate:14:""}
							</td>
						{/block}
						{block name="document_index_table_name"}
							<td align="left" width="48%" valign="top">
									{s name="DocumentIndexPositionNameDefault"}<strong>{$position.meta.articleName|nl2br}{/s}</strong>
										{if $position.attributes.attribute6}<br />
											{foreach $position.attributes.attribute6|json_decode:true as $value}
												{$value.name}: <strong>{$value.value}</strong><br />
											{/foreach}
										{/if}
								<br />
								{if $Barcode.$z}
									<img src="{$Barcode.$z}"><br />
									{$position.articleordernumber}/{$Order._order.ordernumber}
								{/if}
							</td>
						{/block}
						{block name="document_index_table_quantity"}
							<td align="center" width="5%" valign="top">
								{$position.quantity}

							</td>
						{/block}
						{block name="document_index_table_tax"}{/block}
						{block name="document_index_table_price"}{/block}
					</tr>
				{/block}
				{/if}
			{/foreach}
			</tbody>
		</table>

		{if $smarty.foreach.pagingLoop.last}
			{block name="document_index_amount"}{/block}
			{if $Document.comment || $Document.voucher || $Order._currency.factor > 1 || $Order._order.attributes.md_reference}
				{block name="document_index_info"}
					<div id="info">
						<strong>Information:</strong><br /></strong>
						{if $Order._order.attributes.md_reference}
						<table><tr><td>{s name="DocumentReferenceText"}Objekt/Referenz:{/s} {$Order._order.attributes.md_reference}</td></tr></table>
						{/if}
						{block name="document_index_info_comment"}
							{if $Document.comment}
								<div style="font-size:11px;color:#333;font-weight:bold">
									{$Document.comment|replace:"€":"&euro;"}
								</div>
							{/if}
						{/block}

						{block name="document_index_info_voucher"}
							{if $Document.voucher}
								<div style="font-size:11px;color:#333;">
									{s name="DocumentIndexVoucher"}
										Für den nächsten Einkauf schenken wir Ihnen einen {$Document.voucher.value} {$Document.voucher.prefix} Gutschein
										mit dem Code "{$Document.voucher.code}".<br />
									{/s}
								</div>
							{/if}
						{/block}
						{block name="document_index_info_ordercomment"}{/block}


						{$Containers.Content_Info.value}
						{block name="document_index_info_currency"}
							{if $Order._currency.factor > 1}{s name="DocumentIndexCurrency"}
								<br>Euro Umrechnungsfaktor: {$Order._currency.factor|replace:".":","}
							{/s}
							{/if}
						{/block}
					</div>
				{/block}
			{/if}
		{/if}
	</div>




	{if !$smarty.foreach.pagingLoop.last}
		<pagebreak />
	{/if}
	{if $smarty.foreach.pagingLoop.last}
	<div id="footer">
		{$Containers.Footer.value}
	</div>
	{/if}
{/foreach}
</body>
</html>