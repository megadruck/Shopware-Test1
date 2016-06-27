{block name='frontend_checkout_confirm_submit'}
    {if $sCustomLicense}
        <div class="actions">
            <input type="submit" class="button-right large" id="basketButton" disabled="disabled" value="{s namespace="frontend/checkout/confirm" name='ConfirmActionSubmit'}{/s}"/>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
