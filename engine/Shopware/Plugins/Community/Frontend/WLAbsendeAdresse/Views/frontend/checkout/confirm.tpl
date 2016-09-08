{extends file="parent:frontend/checkout/confirm.tpl"}

{block name='frontend_checkout_confirm_information_payment'}{/block}
{block name='frontend_checkout_confirm_information_addresses'}


		{* Separate Billing & Shipping *}
		{block name='frontend_checkout_confirm_information_addresses_billing'}
		<div class="information--panel-item information--panel-item-billing">
			{* Billing address *}
			{block name='frontend_checkout_confirm_information_addresses_billing_panel'}
			<div class="panel has--border block information--panel billing--panel  graybg">

				{* Headline *}
				{block name='frontend_checkout_confirm_information_addresses_billing_panel_title'}
				<div class="panel--title is--underline">
					{s name="ConfirmHeaderBilling" namespace="frontend/checkout/confirm"}{/s}
				</div>
				{/block}

				{* Content *}
				{block name='frontend_checkout_confirm_information_addresses_billing_panel_body'}
				<div class="panel--body is--wide">
					{if $sUserData.billingaddress.company}
					<span class="address--company is--bold">{$sUserData.billingaddress.company}</span>{if $sUserData.billingaddress.department}<br /><span class="address--department is--bold">{$sUserData.billingaddress.department}</span>{/if}
					<br />
					{/if}
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
					{if $sUserData.additional.state.name}<span class="address--statename">{$sUserData.additional.state.name}</span><br />{/if}
				<span class="address--countryname">{$sUserData.additional.country.countryname}</span>

					{block name="frontend_checkout_confirm_information_addresses_billing_panel_body_invalid_data"}
						{if $invalidBillingAddress}
							{include file='frontend/_includes/messages.tpl' type="warning" content="{s name='ConfirmAddressInvalidBillingAddress'}{/s}"}
                                                        {else}
                                                            {block name="frontend_checkout_confirm_information_addresses_billing_panel_body_set_as_default"}
                                                                {if $activeBillingAddressId != $sUserData.additional.user.default_billing_address_id}
                                                                    <div class="set-default">
                                                                        <input type="checkbox" name="setAsDefaultBillingAddress" id="set_as_default_billing" value="1" /> <label for="set_as_default_billing">{s name="ConfirmUseForFutureOrders"}{/s}</label>
                                                                    </div>
                                                                {/if}
                                                            {/block}
                                                        {/if}
                                                    {/block}
                                                </div>
                                            {/block}

 {block name='frontend_checkout_confirm_left_payment_content'}
                <div class="panel--body">
                    {block name='frontend_checkout_confirm_left_payment_method'}
                    <strong class="payment--title">{s name="ConfirmInfoPaymentMethod" namespace="frontend/checkout/confirm"}{/s}</strong>
                    <span class="payment--description"><a style="text-decoration:underline;" href="{url controller=checkout action=shippingPayment sTarget=checkout}">{$sUserData.additional.payment.description}</a></span>
                       {if !$sUserData.additional.payment.esdactive && {config name="showEsd"}}
                        <p class="payment--confirm-esd">{s name="ConfirmInfoInstantDownload" namespace="frontend/checkout/confirm"}{/s}</p>
                        {/if}
                    {/block}

                    {block name='frontend_checkout_confirm_left_shipping_method'}
                    <br><strong class="shipping--title">{s name="ConfirmHeadDispatch"}{/s}</strong>
                    <span class="shipping--description" title="{$sDispatch.name}"><a style="text-decoration:underline;" href="{url controller=checkout action=shippingPayment sTarget=checkout}">{$sDispatch.name|truncate:25:"...":true}</a></span>

                    {/block}
                </div>
                {/block}
                                            {block name="frontend_checkout_confirm_information_addresses_billing_panel_actions"}{/block}

                                                    {block name="frontend_checkout_confirm_information_addresses_billing_panel_actions_change"}

                                                            {block name="frontend_checkout_confirm_information_addresses_billing_panel_actions_change_address"}
                                                                                                           			{/block}
			                                        {/block}
				                                    {block name="frontend_checkout_confirm_information_addresses_billing_panel_actions_select_address"}{/block}




			</div>
			{/block}
		</div>
		{/block}



		{block name='frontend_checkout_confirm_information_addresses_sender'}
				<div class="information--panel-item information--panel-item-sender">
					{block name='frontend_checkout_confirm_information_addresses_sender_panel'}
					<div class="panel has--border block information--panel sender--panel graybg">

						{block name='frontend_checkout_confirm_information_addresses_sender_panel_title'}
						<div class="panel--title is--underline">
							{s name="ConfirmHeaderSender" namespace="frontend/checkout/confirm"}Absendeadresse{/s}
						</div>
						{/block}

						{block name='frontend_checkout_confirm_information_addresses_sender_panel_body'}
						<div class="panel--body is--wide bgArrowRight">
							{if $senderAddress.company}
							<span class="address--company is--bold">{$senderAddress.company}</span>{if $senderAddress.department}<br /><span class="address--department is--bold">{$senderAddress.department}</span>{/if}
							<br />
							{/if}

						<span class="address--salutation">{$senderAddress.salutation|salutation}</span>
							{if {config name="displayprofiletitle"}}
							<span class="address--title">{$senderAddress.title}</span><br/>
							{/if}
						<span class="address--firstname">{$senderAddress.firstname}</span> <span class="address--lastname">{$senderAddress.lastname}</span><br/>
						<span class="address--street">{$senderAddress.street}</span><br />
							{if $senderAddress.additional_address_line1}<span class="address--additional-one">{$senderAddress.additional_address_line1}</span><br />{/if}
							{if $senderAddress.additional_address_line2}<span class="address--additional-one">{$senderAddress.additional_address_line2}</span><br />{/if}
							{if {config name=showZipBeforeCity}}
							<span class="address--zipcode">{$senderAddress.zipcode}</span> <span class="address--city">{$senderAddress.city}</span>
							{else}
							<span class="address--city">{$senderAddress.city}</span> <span class="address--zipcode">{$senderAddress.zipcode}</span>
							{/if}<br />
							{if $senderAddress.state.name}<span class="address--statename">{$senderAddress.state.name}</span><br />{/if}
						<span class="address--countryname">{$senderAddress.country.name}</span>

							{block name="frontend_checkout_confirm_information_addresses_sender_panel_body_invalid_data"}
																		{block name="frontend_checkout_confirm_information_addresses_sender_panel_body_set_as_default"}
																		{/block}
																{/block}
															</div>
														{/block}

														{block name="frontend_checkout_confirm_information_addresses_sender_panel_actions"}
															<div class="panel--actions">
																{block name="frontend_checkout_confirm_information_addresses_sender_panel_actions_change"}
																	<div class="address--actions-change">
																		{block name="frontend_checkout_confirm_information_addresses_shipping_panel_actions_change_address"}
																			{if !$billingSame}
																				<a href="{url controller=address action=edit id=$senderAddress.id sTarget=checkout sTargetAction=confirm}"
																			   title="{s name="ConfirmAddressSelectButton"}Change address{/s}"
																			   data-title="{s name="ConfirmAddressSelectButton"}Change address{/s}"
																			   data-address-editor="true"
																			   data-id="{$activeShippingAddressId}"
																			   data-sessionKey="checkoutShippingAddressId"
																			   class="btn is--primary is--large">
																				{s name="ConfirmAddressSelectButton"}Change address{/s}
																			{/if}

		</a>
		{/block}
		</div>
		{/block}


			{block name="frontend_checkout_confirm_information_addresses_sender_panel_actions_select_address"}
			<a class="open-senderaddress btn is--primary is--large" href="{url controller=address}"
																	   data-address-selection="true"
																	   data-sessionKey="checkoutShippingAddressId"
																	   data-id="{$senderAddress.id}"
																	   title="{s name="ConfirmAddressSelectLink"}{/s}">
																		{s name="ConfirmAddressSelectLink"}{/s}
			</a>
			{/block}
		</div>
		{/block}
		</div>
		{/block}
		</div>
		{/block}







		{block name='frontend_checkout_confirm_information_addresses_shipping'}
		<div class="information--panel-item information--panel-item-shipping">
			{block name='frontend_checkout_confirm_information_addresses_shipping_panel'}
			<div class="panel has--border block information--panel shipping--panel graybg">


				{block name='frontend_checkout_confirm_information_addresses_shipping_panel_title'}
				<div class="panel--title is--underline">
					{s name="ConfirmHeaderShipping" namespace="frontend/checkout/confirm"}{/s}
				</div>
				{/block}


				{block name='frontend_checkout_confirm_information_addresses_shipping_panel_body'}
				<div class="panel--body is--wide">
					{if $sUserData.shippingaddress.company}
					<span class="address--company is--bold">{$sUserData.shippingaddress.company}</span>{if $sUserData.shippingaddress.department}<br /><span class="address--department is--bold">{$sUserData.shippingaddress.department}</span>{/if}
					<br />
					{/if}

				<span class="address--salutation">{$sUserData.shippingaddress.salutation|salutation}</span>
					{if {config name="displayprofiletitle"}}
					<span class="address--title">{$sUserData.shippingaddress.title}</span><br/>
					{/if}
				<span class="address--firstname">{$sUserData.shippingaddress.firstname}</span> <span class="address--lastname">{$sUserData.shippingaddress.lastname}</span><br/>
				<span class="address--street">{$sUserData.shippingaddress.street}</span><br />
					{if $sUserData.shippingaddress.additional_address_line1}<span class="address--additional-one">{$sUserData.shippingaddress.additional_address_line1}</span><br />{/if}
					{if $sUserData.shippingaddress.additional_address_line2}<span class="address--additional-one">{$sUserData.shippingaddress.additional_address_line2}</span><br />{/if}
					{if {config name=showZipBeforeCity}}
					<span class="address--zipcode">{$sUserData.shippingaddress.zipcode}</span> <span class="address--city">{$sUserData.shippingaddress.city}</span>
					{else}
					<span class="address--city">{$sUserData.shippingaddress.city}</span> <span class="address--zipcode">{$sUserData.shippingaddress.zipcode}</span>
					{/if}<br />
					{if $sUserData.additional.stateShipping.name}<span class="address--statename">{$sUserData.additional.stateShipping.name}</span><br />{/if}
				<span class="address--countryname">{$sUserData.additional.countryShipping.countryname}</span>

					{block name="frontend_checkout_confirm_information_addresses_shipping_panel_body_invalid_data"}
						{if $invalidShippingAddress}
							{include file='frontend/_includes/messages.tpl' type="warning" content="{s name='ConfirmAddressInvalidShippingAddress'}{/s}"}
                                                        {else}
                                                            {block name="frontend_checkout_confirm_information_addresses_shipping_panel_body_set_as_default"}
                                                                {if $activeShippingAddressId != $sUserData.additional.user.default_shipping_address_id}
                                                                    <div class="set-default">
                                                                        <input type="checkbox" name="setAsDefaultShippingAddress" id="set_as_default_shipping" value="1" /> <label for="set_as_default_shipping">{s name="ConfirmUseForFutureOrders"}{/s}</label>
                                                                    </div>
                                                                {/if}
                                                            {/block}
                                                        {/if}
                                                    {/block}
                                                </div>
                                            {/block}


                                            {block name="frontend_checkout_confirm_information_addresses_shipping_panel_actions"}
                                                <div class="panel--actions">
                                                    {block name="frontend_checkout_confirm_information_addresses_shipping_panel_actions_change"}
                                                        <div class="address--actions-change">
                                                            {block name="frontend_checkout_confirm_information_addresses_shipping_panel_actions_change_address"}
                                                            	{if $activeBillingAddressId != $activeShippingAddressId}
																	<a href="{url controller=address action=edit id=$activeShippingAddressId sTarget=checkout sTargetAction=confirm}"
																	   title="{s name="ConfirmAddressSelectButton"}Change address{/s}"
																	   data-title="{s name="ConfirmAddressSelectButton"}Change address{/s}"
																	   data-address-editor="true"
																	   data-id="{$activeShippingAddressId}"
																	   data-sessionKey="checkoutShippingAddressId"
																	   class="btn is--primary is--large">
																		{s name="ConfirmAddressSelectButton"}Change address{/s}
																	</a>
																{/if}

                                                            {/block}

                                                        {block name="frontend_checkout_confirm_information_addresses_shipping_panel_actions_select_address"}
                                                        <a class="btn  is--primary is--large open-shippingaddress" href="{url controller=address}?shipping=true"
                                                                                                   data-address-selection="true"
                                                                                                   data-sessionKey="checkoutShippingAddressId"
                                                                                                   data-id="{$activeShippingAddressId}"
                                                                                                   title="{s name="ConfirmAddressSelectLink"}{/s}">
                                                                                                    {s name="ConfirmAddressSelectLink"}{/s}
                                                        </a>
                                                        {/block}
            </div>
            {/block}
			                                    </div>
			                                {/block}
			                </div>
			            {/block}
		            </div>
		        {/block}


{/block}


{block name="frontend_index_header_javascript_jquery" append}
<script>
registerAddressTypeListener();
</script>
{/block}

                    