{extends file='parent:frontend/address/ajax_selection.tpl'}


{block name="frontend_address_selection_modal_container"}

    <div class="modal--container" data-panel-auto-resizer="true">
        <div class="filterbar">
            <input type="text" id="searchField" placeholder="Adressbuchsuche">
        </div>
        {if $sender_address_id != 0}
            <div class="modal--container-item address--box noSenderBox" id="0" data-tags="Marcelo Dit  Muster">
                <div class="panel address--item-content has--border is--rounded block">

                    <div class="address--item-body panel--body-row is--wide" style="height: auto;">
                        <span class="address--firstname is--bold"><i class="icon--star" style="color:red"></i> Megadruck als Absender</span>
                    </div>

                   <div class="panel--actions address-selection address-selection-0" style="display: none; height: auto;" "="">

                        <button class="is--block is--primary is--icon-right" id="noSender" data-type="absendeadresse" data-checkformisvalid="false" data-preloader-button="true">Als Absendeadresse nutzen
                         <span class="icon--arrow-right"></span>
                        </button>

                    </div>

                </div>
            </div>
        {/if}
        {foreach $addresses as $address}

            {block name='frontend_address_selection_modal_container_item'}
                <div class="modal--container-item address--box" id="{$address.id}" data-tags="{$address.firstname} {$address.lastname} {$address.company} {$address.city}">
                    <div class="panel address--item-content has--border is--rounded block">
                        {block name='frontend_address_selection_modal_container_item_body'}
                            <div class="address--item-body panel--body-row is--wide">
                                <span class="address--firstname is--bold"><i class="icon--users"></i> {$address.firstname}</span> <span class="address--lastname is--bold">{$address.lastname},</span>
                                {if $address.company} <span class="address--company">{$address.company},</span>{/if}
                                <span class="address--street"> {$address.street},</span>
                                {if $address.additionalAddressLine1} <span class="address--additional-one">{$address.additionalAddressLine1},</span>{/if}
                                {if $address.additionalAddressLine2} <span class="address--additional-two">{$address.additionalAddressLine2},</span>{/if}
                                {if {config name=showZipBeforeCity}}
                                <span class="address--zipcode">{$address.zipcode}</span> <span class="address--city">{$address.city},</span>
                                {else}
                                <span class="address--city">{$address.city}</span> <span class="address--zipcode">{$address.zipcode},</span>
                                {/if}
                                <span class="address--countryname">{$address.country.name}</span>
                            </div>
                        {/block}

                        {block name='frontend_address_selection_modal_container_item_actions'}
                            <div class="panel--actions address-selection address-selection-{$address.id}" style="display:none;"">
                                <form class="address-manager--selection-form-{$address.id}" action="{url controller=senderaddress action=handleExtra}" method="post">
                                    <input type="hidden" name="id" value="{$address.id}" />

                                    {block name="frontend_address_selection_modal_container_item_extra_data"}
                                        {foreach $extraData as $key => $val}
                                            <input type="hidden" name="extraData[{$key}]" value="{$val}" />
                                        {/foreach}
                                    {/block}

                                </form>

                                {block name="frontend_address_selection_modal_container_item_select_button"}


                                    <button class="btn-xs is--block is--primary is--icon-right address--btn-wl absender-address-btn"
                                            data-type="absendeadresse"
                                            data-checkFormIsValid="false"
                                            data-preloader-button="true"
                                            data-id="{$address.id}"
                                            >

                                        {s name="SelectSenderAddressButton"}Als Absendeadresse nutzen{/s}
                                        <span class="icon--arrow-right"></span>
                                    </button>

                                    <button class="btn-xs is--block is--primary is--icon-right address--btn-wl liefer-address-btn"
                                            data-type="lieferadresse"
                                            data-checkFormIsValid="false"
                                            data-preloader-button="true"
                                            data-id="{$address.id}"
                                            >
                                        {s name="SelectShippingAddressButton"}Als Lieferadresse nutzen{/s}
                                        <span class="icon--arrow-right"></span>
                                    </button>




                                {/block}

                            </div>
                        {/block}
                    </div>
                </div>
            {/block}
        {/foreach}
        <script>
            var clearAddrUrl = '{url controller=senderaddress action=clearSender}';
            registerAjaxListener();
        </script>
    </div>
{/block}








