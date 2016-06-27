{extends file='parent:frontend/account/index.tpl'}

{* Breadcrumb *}
{block name='frontend_index_start' append}
	{block name="frontend_index_swag_ticket_system_breadcrumb"}
		{$sBreadcrumb = [['name'=>"{s name='TicketTitle'}{/s}", 'link'=>{url}]]}
	{/block}
{/block}

{* Main content *}
{block name='frontend_index_content'}
	{block name="frontend_index_swag_ticket_system_content"}
	<div class="content block ticketsystem--overview-content">
		{block name='frontend_index_content_ticketsystem'}
			<div class='ticketsytem'>
				{block name='frontend_index_content_ticketsystem_headline'}
					<h1 class="ticketsystem--listing-headline">{s name='TicketHeadline'}{/s}</h1>
				{/block}
				{block name='frontend_index_content_ticketsystem_panel'}
					<div class="ticketsystem panel">

						{block name='frontend_ticketsystem_table'}
							<div class="panel--table">

								{block name='frontend_ticket_table_head'}
									<div class="ticketsystem--table-header panel--tr">
										{include file='frontend/ticket/listing_header.tpl'}
									</div>
								{/block}

								{block name='frontend_ticketsystem_table_content'}
									{foreach $entrys as $ticketItem}
										{include file='frontend/ticket/listing_content.tpl'}
									{/foreach}
								{/block}
							</div>
						{/block}

						{block name="frontend_ticketsystem_actions_paging"}
							{if $sNumberPages > 1}
								<div class="ticketsytem--pagination">
									<div class="panel--paging">
										{* link first page *}
										<a href="{url controller='ticket' action='listing' sPage="1"}" class="paging--link "><i class="icon--arrow-left"></i><i class="icon--arrow-left"></i></a>

										{* link page back *}
										<a href="{url controller='ticket' action='listing' sPage="{if $sPage != 1}{$sPage - 1}{else}{$sPage}{/if}"}" class="paging--link "><i class="icon--arrow-left"></i></a>

										{* link current page *}
										<a href="{url controller='ticket' action='listing' sPage="{$sPage}"}" class="paging--link is--active">{$sPage}</a >

										{* link page forward *}
										<a href="{url controller='ticket' action='listing' sPage="{if $sPage != $sNumberPages}{$sPage + 1}{else}{$sPage}{/if}"}" class="paging--link paging--next"><i class="icon--arrow-right"></i></a>

										{* link last page *}
										<a href="{url controller='ticket' action='listing' sPage="{$sNumberPages}"}" class="paging--link paging--next"><i class="icon--arrow-right"></i><i class="icon--arrow-right"></i></a>

										{* page entries *}
										<span class="paging--display">von <strong>{$sNumberPages}</strong></span>
									</div>
								</div>
							{/if}
						{/block}
					</div>
				{/block}
			</div>
		{/block}
	<div>
	{/block}
{/block}