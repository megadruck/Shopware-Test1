{extends file="parent:frontend/register/billing_fieldset.tpl"}

{block name='frontend_register_billing_fieldset_input_zip_and_city'}{/block}
{block name='frontend_register_billing_fieldset_input_country'}{/block}
{block name='frontend_register_billing_fieldset_input_country_states'}{/block}

{* Street gets prepended by all other fields to make autocomplete easier and supply better data *}
{block name='frontend_register_billing_fieldset_input_street' prepend}
    {* COUNTRY *}
    <div class="register--country field--select">
        <select name="register[billing][country]" id="country" required="required" aria-required="true"
                class="select--country is--required{if $error_flags.country} has--error{/if}">
            <option disabled="disabled" value=""
                    selected="selected">{s name='RegisterBillingPlaceholderCountry'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
            {foreach $country_list as $country}
                <option value="{$country.id}"
                isocode="{if $country.countryiso eq "US"}US*{else}{$country.countryiso}{/if}"
                        {if $country.id eq $form_data.country}selected="selected"{/if} {if $country.states}stateSelector="country_{$country.id}_states"{/if}>
                    {$country.countryname}
                </option>
            {/foreach}
        </select>
    </div>
    {* STATE *}
    <div class="country-area-state-selection">
        {foreach $country_list as $country}
            {if $country.states}
                <div id="country_{$country.id}_states"
                     class="register--state-selection field--select{if $country.id != $form_data.country} is--hidden{/if}">
                    <select {if $country.id != $form_data.country}disabled="disabled"{/if}
                            name="register[billing][country_state_{$country.id}]"{if $country.force_state_in_registration} required="required" aria-required="true"{/if}
                            class="select--state {if $country.force_state_in_registration}is--required{/if}{if $error_flags.stateID} has--error{/if}">
                        <option value=""
                                selected="selected"{if $country.force_state_in_registration} disabled="disabled"{/if}>{s name='RegisterBillingLabelState'}{/s}{if $country.force_state_in_registration}{s name="RequiredField" namespace="frontend/register/index"}{/s}{/if}</option>
                        {assign var="stateID" value="country_state_`$country.id`"}
                        {foreach $country.states as $state}
                            <option value="{$state.id}" {if $state.id eq $form_data[$stateID]}selected="selected"{/if}>
                                {$state.name}
                            </option>
                        {/foreach}
                    </select>
                </div>
            {/if}
        {/foreach}
    </div>

    {* ZIP AND CITY *}
    <div class="register--zip-city">
        {if {config name=showZipBeforeCity}}
            <input autocomplete="section-billing billing postal-code" name="register[billing][zipcode]" type="text"
                   required="required" aria-required="true"
                   placeholder="{s name='RegisterBillingPlaceholderZipcode'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                   id="zipcode" value="{$form_data.zipcode|escape}"
                   class="register--field register--spacer register--field-zipcode is--required{if $error_flags.zipcode} has--error{/if}"/>
            <input autocomplete="section-billing billing address-level2" name="register[billing][city]" type="text"
                   required="required" aria-required="true"
                   placeholder="{s name='RegisterBillingPlaceholderCity'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                   id="city" value="{$form_data.city|escape}" size="25"
                   class="register--field register--field-city is--required{if $error_flags.city} has--error{/if}"/>
        {else}
            <input autocomplete="section-billing billing address-level2" name="register[billing][city]" type="text"
                   required="required" aria-required="true"
                   placeholder="{s name='RegisterBillingPlaceholderCity'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                   id="city" value="{$form_data.city|escape}" size="25"
                   class="register--field register--spacer register--field-city is--required{if $error_flags.city} has--error{/if}"/>
            <input autocomplete="section-billing billing postal-code" name="register[billing][zipcode]" type="text"
                   required="required" aria-required="true"
                   placeholder="{s name='RegisterBillingPlaceholderZipcode'}{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                   id="zipcode" value="{$form_data.zipcode|escape}"
                   class="register--field register--field-zipcode is--required{if $error_flags.zipcode} has--error{/if}"/>
        {/if}
    </div>
{/block}