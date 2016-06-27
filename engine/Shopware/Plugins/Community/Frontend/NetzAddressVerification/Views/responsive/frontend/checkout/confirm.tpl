{extends file="parent:frontend/checkout/confirm.tpl"}
{block name='frontend_checkout_confirm_agb' append}
    {if $showNetzCheckAddressVerifyContinue}
        <li class="block-group row--tos">
            <span class="block column--checkbox">
                <input type="checkbox" required="required" aria-required="true"
                       id="netzCheckAddressVerifyContinue"
                       name="netzCheckAddressVerifyContinue"/>
            </span>

            <span class="block column--label">
                <label for="NetzAddressVerificationConfirm" class="has--error"
                       data-modalbox="true"
                       data-targetSelector="a" data-mode="ajax" data-height="500"
                       data-width="750">{s name="NetzCheckAddressVerifyContinue"}Bestellung trotz fehlender Adressverifizierung fortsetzen{/s}
                </label>
            </span>
        </li>
    {/if}
{/block}