{extends file='parent:frontend/account/index.tpl'}


        {block name="frontend_account_index_addresses"}
            <div data-panel-auto-resizer="true" class="account-address--container">
                {* Billing addresses *}
                {block name="frontend_account_index_primary_billing"}
                    <div class="account--billing account--box panel has--border is--rounded">

                        {block name="frontend_account_index_primary_billing_headline"}
                            <h2 class="panel--title is--underline">{s name="AccountHeaderPrimaryBilling"}{/s}</h2>
                        {/block}

                        {block name="frontend_account_index_primary_billing_content"}
                            <div class="panel--body is--wide">
                                {if $sUserData.billingaddress.company}
                                    <p>
                                        <span class="address--company">{$sUserData.billingaddress.company}</span>{if $sUserData.billingaddress.department} - <span class="address--department">{$sUserData.billingaddress.department}</span>{/if}
                                    </p>
                                {/if}
                                <p>
                                    <span class="address--salutation">{$sUserData.billingaddress.salutation|salutation}</span>
                                    {if {config name="displayprofiletitle"}}
                                        <span class="address--title">{$sUserData.billingaddress.title}</span><br/>
                                    {/if}
                                    <span class="address--firstname">{$sUserData.billingaddress.firstname}</span> <span class="address--lastname">{$sUserData.billingaddress.lastname}</span><br />
                                    <span class="address--street">{$sUserData.billingaddress.street}</span><br />
                                    {if $sUserData.billingaddress.additional_address_line1}<span class="address--additional-one">{$sUserData.billingaddress.additional_address_line1}</span><br />{/if}
                                    {if $sUserData.billingaddress.additional_address_line2}<span class="address--additional-two">{$sUserData.billingaddress.additional_address_line2}</span><br />{/if}
                                    {if {config name=showZipBeforeCity}}
                                    <span class="address--zipcode">{$sUserData.billingaddress.zipcode}</span> <span class="address--city">{$sUserData.billingaddress.city}</span>
                                    {else}
                                    <span class="address--city">{$sUserData.billingaddress.city}</span> <span class="address--zipcode">{$sUserData.billingaddress.zipcode}</span>
                                    {/if}<br />
                                    {if $sUserData.additional.state.statename}<span class="address--statename">{$sUserData.additional.state.statename}</span><br />{/if}
                                    <span class="address--countryname">{$sUserData.additional.country.countryname}</span>
                                </p>
                            </div>
                        {/block}

                        {block name="frontend_account_index_primary_billing_actions"}
                            <div class="panel--actions is--wide">
                                <p>{s name="ChangeBillingTextInfo"}Um Ihre Rechnungsadresse zu ändern kontaktieren Sie uns bitte telefonisch unter <br/>011111-1111{/s}</p>
                            </div>
                        {/block}
                    </div>
                {/block}


                {* Shipping addresses *}
                {block name="frontend_account_index_primary_shipping"}
                    <div class="account--shipping account--box panel has--border is--rounded">

                        {block name="frontend_account_index_primary_shipping_headline"}
                            <h2 class="panel--title is--underline">{s name="AccountHeaderPrimaryShipping"}{/s}</h2>
                        {/block}

                        {block name="frontend_account_index_primary_shipping_content"}
                            <div class="panel--body is--wide">
                                {if $sUserData.shippingaddress.company}
                                    <p>
                                        <span class="address--company">{$sUserData.shippingaddress.company}</span>{if $sUserData.shippingaddress.department} - <span class="address--department">{$sUserData.shippingaddress.department}</span>{/if}
                                    </p>
                                {/if}
                                <p>
                                    <span class="address--salutation">{$sUserData.shippingaddress.salutation|salutation}</span>
                                    {if {config name="displayprofiletitle"}}
                                        <span class="address--title">{$sUserData.shippingaddress.title}</span><br/>
                                    {/if}
                                    <span class="address--firstname">{$sUserData.shippingaddress.firstname}</span> <span class="address--lastname">{$sUserData.shippingaddress.lastname}</span><br />
                                    <span class="address--street">{$sUserData.shippingaddress.street}</span><br />
                                    {if $sUserData.shippingaddress.additional_address_line1}<span class="address--additional-one">{$sUserData.shippingaddress.additional_address_line1}</span><br />{/if}
                                    {if $sUserData.shippingaddress.additional_address_line2}<span class="address--additional-two">{$sUserData.shippingaddress.additional_address_line2}</span><br />{/if}
                                    {if {config name=showZipBeforeCity}}
                                    <span class="address--zipcode">{$sUserData.shippingaddress.zipcode}</span> <span class="address--city">{$sUserData.shippingaddress.city}</span>
                                    {else}
                                    <span class="address--city">{$sUserData.shippingaddress.city}</span> <span class="address--zipcode">{$sUserData.shippingaddress.zipcode}</span>
                                    {/if}<br />
                                    {if $sUserData.additional.stateShipping.statename}<span class="address--statename">{$sUserData.additional.stateShipping.statename}</span><br />{/if}
                                    <span class="address--countryname">{$sUserData.additional.countryShipping.countryname}</span>
                                </p>
                            </div>
                        {/block}

                        {block name="frontend_account_index_primary_shipping_actions"}
                            <div class="panel--actions is--wide">
                                {if $sUserData.additional.user.default_shipping_address_id != $sUserData.additional.user.default_billing_address_id}
                                <a href="{url controller=address action=edit id=$sUserData.additional.user.default_shipping_address_id sTarget=account}"
                                   title="{s name='AccountLinkChangeBilling'}{/s}"
                                   class="btn">
                                    {s name="AccountLinkChangeShipping"}{/s}
                                </a>
                                <br/>
                                {/if}
                                <a href="{url controller=address}"
                                       data-address-selection="true"
                                       data-setDefaultShippingAddress="1"
                                       data-id="{$sUserData.additional.user.default_shipping_address_id}"
                                       title="{s name='AccountLinkChangeBilling'}{/s}">
                                        {s name="AccountLinkSelectBilling"}{/s}
                                </a>

                            </div>
                        {/block}
                    </div>
                {/block}


                {* Sender addresses *}
                {block name="frontend_account_index_primary_sender"}
                    <div class="account--billing account--box panel has--border is--rounded">

                        {block name="frontend_account_index_primary_sender_headline"}
                            <h2 class="panel--title is--underline">{s name="AccountHeaderPrimarySender"}Primäre Absendeadresse{/s}</h2>
                        {/block}

                        {block name="frontend_account_index_primary_sender_content"}
                            <div class="panel--body is--wide">
                                {if $senderAddress.company}
                                    <p>
                                        <span class="address--company">{$senderAddress.company}</span>{if $senderAddress.department} - <span class="address--department">{$senderAddress.department}</span>{/if}
                                    </p>
                                {/if}
                                <p>
                                    <span class="address--salutation">{$senderAddress.salutation|salutation}</span>
                                    {if {config name="displayprofiletitle"}}
                                        <span class="address--title">{$senderAddress.title}</span><br/>
                                    {/if}
                                    <span class="address--firstname">{$senderAddress.firstname}</span> <span class="address--lastname">{$senderAddress.lastname}</span><br />
                                    <span class="address--street">{$senderAddress.street}</span><br />
                                    {if $senderAddress.additional_address_line1}<span class="address--additional-one">{$senderAddress.additional_address_line1}</span><br />{/if}
                                    {if $senderAddress.additional_address_line2}<span class="address--additional-two">{$senderAddress.additional_address_line2}</span><br />{/if}
                                    {if {config name=showZipBeforeCity}}
                                    <span class="address--zipcode">{$senderAddress.zipcode}</span> <span class="address--city">{$senderAddress.city}</span>
                                    {else}
                                    <span class="address--city">{$senderAddress.city}</span> <span class="address--zipcode">{$senderAddress.zipcode}</span>
                                    {/if}<br />
                                    {if $senderAddress.state.name}<span class="address--statename">{$senderAddress.state.name}</span><br />{/if}
                                    <span class="address--countryname">{$senderAddress.country.name}</span>
                                </p>
                            </div>
                        {/block}

                        {block name="frontend_account_index_primary_sender_actions"}
                            <div class="panel--actions is--wide">
                                {if !$billingSame}
                                    <a href="{url controller=address action=edit id=$senderAddress.id sTarget=account}"
                                       title="{s name='AccountLinkEditSenderTitle'}Absendeadresse ändern{/s}"
                                       class="btn">
                                        {s name="AccountLinkEditSender"}Absendeadresse ändern{/s}
                                    </a>
                                    <br/>
                                {/if}
                                <a href="{url controller=address}"
                                   data-address-selection="true"
                                   data-setDefaultSenderAddress="1"
                                   data-id="{$senderAddress.id}"
                                   title="{s name='AccountLinkChangeSenderTitle'}oder andere Adresse wählen{/s}">
                                    {s name="AccountLinkSelectSender"}oder andere Adresse wählen{/s}
                                </a>
                            </div>
                        {/block}
                    </div>
                {/block}



            </div>
        {/block}
