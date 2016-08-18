{extends file="parent:frontend/newsletter/index.tpl"}

{* Shopware Bug Fix (already reported) *}
{block name="frontend_newsletter_error_messages"}
    {if $sStatus.code}
        <div class="newsletter--error-messages">
            {if $sStatus.code==3}
                {include file="frontend/_includes/messages.tpl" type='success' content=$sStatus.message}
            {elseif $sStatus.code==5}
                {include file="frontend/_includes/messages.tpl" type='error' content=$sStatus.message}
            {elseif $sStatus.code==2}
                {include file="frontend/_includes/messages.tpl" type='warning' content=$sStatus.message}
            {elseif $sStatus.code != 0}
                {include file="frontend/_includes/messages.tpl" type='error' content=$sStatus.message}
            {/if}
        </div>
    {/if}
{/block}