{extends file='frontend/index/index.tpl'}

{* Breadcrumb *}
{block name='frontend_index_start' append}
    {$sBreadcrumb = [['name'=>"{s name='TicketTitle'}{/s}", 'link'=>{url controller='ticket' action='listing'}]]}
{/block}

{* Empty sidebar left *}
{block name='frontend_index_content_left'}{/block}

{* Main contents *}
{block name='frontend_index_content'}
<div class="grid_16">

{if !$ticketDetails.id}
	<div class="error">
	{s name='TicketDetailInfoEmpty'}{/s}
	</div>
{else}
	
	{* Ticket headline *}
	{block name='frontend_ticket_headline'}
	<h2>{s name='TicketDetailInfoTicket'}{/s} #{$ticketDetails.id}</h2>
	{/block}
	
    {if $userAttachments}
        <div class="table grid_16">
            <div class="table_head">
                <div class="grid_7">{s name='TicketAttachment'}Attachment{/s}</div>
                <div class="grid_3">{s name='TicketAttachmentDate'}Attachment date{/s}</div>
                <div class="grid_4 textright">
                    <div class="textright">{s name='TicketAttachmentAction'}action{/s}</div>
                </div>
            </div>
            {foreach from=$userAttachments item=userAttachment}
            <div class="table_row">
                <div class="grid_7">{$userAttachment.filename}</div>
                <div class="grid_3">{$userAttachment.date|date_format:"%d.%m.%Y %H:%M:%S"}</div>
                <div class="grid_5">
                    <div class="textright">
                        <strong>
                            <a href="{url controller=ticket action=download attachment=$userAttachment.hash}" title="" class="button-middle small">{s name="TicketDetailAttachmentDownload"}Download{/s}</a>
                            <a href="{url controller=ticket action=delete attachment=$userAttachment.hash}" title="" class="button-middle small">{s name="TicketDetailAttachmentDelete"}Delete{/s}</a>
                        </strong>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
        <div class="clear">&nbsp;</div>
    {/if}
    
	{* Error messages *}
	{if $error!=""}
		<div class="error">{$error}</div>
	{/if}
	
	{if $accept!=""}
		{* Ticket status *}
		<div>{$accept}</div>
	{/if}

	{* Ticket closed *}
	{if $ticketDetails.closed}
	 	<div class="success">
		{s name='TicketDetailInfoStatusClose'}{/s}
		</div>
	{* Ticket in process *}
	{elseif !$ticketDetails.responsible}
		<div class="notice bold center">{s name='TicketDetailInfoStatusProgress'}{/s}</div>
	{* Ticket answer *}
	{/if}

    {if $ticketDetails.responsible || $ticketDetails.showAnswer == 1}
        {block name='frontend_ticket_answer'}
        {assign var="tickeranswer" value=true}
        <div class="tickeranswer">
            <form action="" method="POST">
                <h2>{s name='TicketDetailInfoAnswer'}{/s}:</h2>
                <textarea name="sAnswer"></textarea>

                <input class="button-right large" type="submit" value="Senden" name="sSubmit"/>
            </form>
            {block name='frontend_ticket_answer_upload'}
                {if {config name=allowUploads}}
                    <form name="fileuploadOption" method="post" action="" class="answerUpload">
                        <div class="option_values option_values_upload_file">
                            <input type="hidden" name="ticket" value="{$ticketDetails.id}" />
                            <input type="hidden" name="answer" value="-1" />
                            <input type="file" id="option" class="fileupload-input" name="fileupload" multiple="multiple" />
                        </div>
                    </form>
                {/if}
            {/block}
        </div>
        {/block}
    {/if}

    {foreach from=$ticketHistoryDetails item=historyItem name=history}
        <label class="ticketdetail_lbl">
            {$historyItem.receipt|date_format:"%d.%m.%Y %H:%M:%S"} |
            {if $historyItem.direction == "OUT"}
                {s name='TicketDetailInfoShopAnswer'}{/s}
                {else}
                {s name='TicketDetailInfoAnswer'}{/s}
            {/if}:</label>
        {* Your message *}
        {block name='frontend_ticket_history_your_message'}
            <div class="ticketdetail_txtbox">
                {$historyItem.message}
                {block name='frontend_ticket_history_attachments'}
                    {if $historyItem.attachment}
                        {foreach from=$historyItem.attachment key=key item=attachment}
                            <a href="{url controller=ticket action=download attachment=$attachment.hash}" class="button-left small" target="_blank">{s name="TicketDetailAttachment"}Download attachment{/s}</a>
                        {/foreach}
                    {/if}
                {/block}
            </div>
        {/block}
    {/foreach}

	{* Ticket meta data *}
	{block name='frontend_ticket_meta_data'}
		<label class="ticketdetail_lbl">{$ticketDetails.receipt|date_format:"%d.%m.%Y %H:%M:%S"} | {s name='TicketDetailInfoQuestion'}{/s} {$ticketDetails.subject}</label>
		<div class="ticketdetail_txtbox">{$ticketDetails.message}</div>
		<h2 class="heading">{s name='TicketDetailAdditionalData'}{/s}</h2>
		{foreach $ticketDetails.additional as $additionalData}
			{if $additionalData.value}
			<label class="ticketdetail_lbl">{$additionalData.label}:</label>
			<div class="ticketdetail_txtbox">
				{$additionalData.value}
			</div>
			{/if}
		{/foreach}
	{/block}

{/if}
	<a href="{url controller='ticket' action='listing'}" class="button-left large">{s name='TicketDetailLinkBack'}{/s}</a>
	<div class="space">&nbsp;</div>
</div>
{/block}
{block name='frontend_index_content_right'}
<div id="right_account" class="grid_4 last">
	{include file='frontend/ticket/navigation.tpl'}
</div>
{/block}