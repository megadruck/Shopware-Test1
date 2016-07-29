{extends file='parent:frontend/address/ajax_selection.tpl'}


{block name="frontend_address_selection_modal_container"}
    <div class="modal--container" data-panel-auto-resizer="true">
        <div class="filterbar">
            <p class="labeltag">Suche:</p><input type="text" id="searchField">
        </div>
        {foreach $addresses as $address}
            {block name='frontend_address_selection_modal_container_item'}
                <div class="modal--container-item address--box" data-tags="{$address.firstname} {$address.lastname} {$address.company} {$address.city}">
                    <div class="panel address--item-content has--border is--rounded block">
                        {block name='frontend_address_selection_modal_container_item_body'}
                            <div class="address--item-body panel--body is--wide">
                                <span class="address--firstname is--bold">{$address.firstname}</span> <span class="address--lastname is--bold">{$address.lastname}</span><br />
                                {if $address.company}<span class="address--company">{$address.company}</span><br/>{/if}
                                <span class="address--street">{$address.street}</span><br />
                                {if $address.additionalAddressLine1}<span class="address--additional-one">{$address.additionalAddressLine1}</span><br />{/if}
                                {if $address.additionalAddressLine2}<span class="address--additional-two">{$address.additionalAddressLine2}</span><br />{/if}
                                {if {config name=showZipBeforeCity}}
                                <span class="address--zipcode">{$address.zipcode}</span> <span class="address--city">{$address.city}</span>
                                {else}
                                <span class="address--city">{$address.city}</span> <span class="address--zipcode">{$address.zipcode}</span>
                                {/if}<br />
                                <span class="address--countryname">{$address.country.name}</span>
                            </div>
                        {/block}

                        {block name='frontend_address_selection_modal_container_item_actions'}
                            <div class="panel--actions">
                                <form class="address-manager--selection-form" action="{url controller=senderaddress action=handleExtra}" method="post">
                                    <input type="hidden" name="id" value="{$address.id}" />

                                    {block name="frontend_address_selection_modal_container_item_extra_data"}
                                        {foreach $extraData as $key => $val}
                                            <input type="hidden" name="extraData[{$key}]" value="{$val}" />
                                        {/foreach}
                                    {/block}

                                </form>

                                {block name="frontend_address_selection_modal_container_item_select_button"}


                                    <button class="btn is--block is--primary is--icon-right address--btn-wl"
                                            data-type="absendeadresse"
                                            data-checkFormIsValid="false"
                                            data-preloader-button="true">
                                        {s name="SelectSenderAddressButton"}Als Absendeadresse nutzen{/s}
                                        <span class="icon--arrow-right"></span>
                                    </button>

                                    <button class="btn is--block is--primary is--icon-right address--btn-wl"
                                            data-type="lieferadresse"
                                            data-checkFormIsValid="false"
                                            data-preloader-button="true">
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
            registerAjaxListener();
        </script>
    </div>
{/block}








