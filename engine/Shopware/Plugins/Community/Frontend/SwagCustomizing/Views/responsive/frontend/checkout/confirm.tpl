{extends file="parent:frontend/checkout/confirm.tpl"}

{block name='frontend_checkout_confirm_submit'}
    {block name='frontend_checkout_customizing_confirm_submit'}
        {if $sCustomLicense}
            <button type="submit" class="btn is--primary is--large right is--icon-right is--disabled" form="confirm--form" data-preloader-button="true" disabled="disabled">
                {s name='ConfirmDoPayment'}{/s}<i class="icon--arrow-right"></i>
            </button>
        {else}
            {$smarty.block.parent}
        {/if}
    {/block}
{/block}
