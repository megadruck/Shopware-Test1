{*extends file=parent inside here, because the file is loaded over the file inheritance*}
{extends file="parent:frontend/checkout/items/product.tpl"}

{block name='frontend_checkout_cart_item_delete_article' append}
    <div style="clear:both;"></div>
    {if $sBasket['content'][$sBasketItem@key]['uploadFields']}

        {if $sUserLoggedIn}

            {$selectStatus = "selection_status_"|cat:$sBasketItem.id}
            <div id="eMailTransferNotice" class="panel"
                 style="padding:5px;margin-left:10px;text-align:left;{if $sWaUp[$selectStatus]==0 || $sWaUp[$selectStatus]==1 || $sWaUp[$selectStatus]==3}display:none;{/if}">
                <div class="panel--body is--rounded">
                    <div class="panel--header secondary">{s name='BoxHeader1' namespace='frontend/basketUploader'}Daten hochladen{/s}</div>
                    <div class="panel--table">
                        <div class="panel--tr">
                            <div class="panel--td">
                                Sie haben die Übertragungsart per e-Mail ausgewählt.
                                <div class="clearfix"></div>
                                <a href="#" id="change_datatransfer_{$sBasketItem.id}" class="btn is--small">
                                    Datenübertragung ändern</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="postTransferNotice" class="panel"
                 style="padding:5px;margin-left:10px;text-align:left;{if $sWaUp[$selectStatus]==0 || $sWaUp[$selectStatus]==1 || $sWaUp[$selectStatus]==2}display:none;{/if}">
                <div class="panel--body is--rounded">
                    <div class="panel--header secondary">{s name='BoxHeader1' namespace='frontend/basketUploader'}Daten hochladen{/s}</div>
                    <div class="panel--table">
                        <div class="panel--tr">
                            <div class="panel--td">
                                Sie haben die Übertragungsart per Post ausgewählt.
                                <div class="clearfix"></div>
                                <a href="#" id="change_datatransfer_{$sBasketItem.id}" class="btn is--small">
                                    Datenübertragung ändern</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="selectorBox" class="panel"
                 style="padding:5px;text-align:left;{if $sWaUp[$selectStatus]==1 || $sWaUp[$selectStatus]==2 || $sWaUp[$selectStatus]==3 || $sWaUp['vorauswahl_enable']==false}display:none;{/if}">
                <div class="panel--body is--rounded">
                    <div class="panel--header secondary">{s name='BoxHeader1' namespace='frontend/basketUploader'}Daten hochladen{/s}</div>
                    <div class="panel--table">
                        <div class="panel--tr">
                            <div class="panel--td">
                                Treffen sie Ihre Auswahl wie Sie uns Ihre Daten übertragen möchten.

                                <ul id="selector">
                                    <li style="float:left;"><a class="btn is--small" href="/perUpload"
                                                               id="select_{$sBasketItem.id}">{$sWaUp.auswahl1}</a></li>
                                    {if $sWaUp.auswahl2}
                                        <li style="float:left;"><a class="btn is--small" href="/perEMail"
                                                                   id="select_{$sBasketItem.id}">{$sWaUp.auswahl2}</a>
                                        </li>{/if}
                                    {if $sWaUp.auswahl3}
                                        <li style="float:left;"><a class="btn is--small" href="/perPost"
                                                                   id="select_{$sBasketItem.id}">{$sWaUp.auswahl3}</a>
                                        </li>{/if}
                                    <li style="clear:both;"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel" style="margin-bottom:10px;">
                <div class="panel--body is--rounded"
                     style="{if $sWaUp['vorauswahl_enable']==true && $sWaUp[$selectStatus]==0 || $sWaUp[$selectStatus]==2 || $sWaUp[$selectStatus]==3}display:none;{/if}">
                    <div id="upload_box_{$sBasketItem.id}"
                         style="{if $sWaUp['vorauswahl_enable']==true && $sWaUp[$selectStatus]==0 || $sWaUp[$selectStatus]==2 || $sWaUp[$selectStatus]==3}display:none;{/if}">
                        <div class='uploadBox'>
                            <div class="panel--header secondary">{s name='BoxHeader1' namespace='frontend/basketUploader'}Daten hochladen{/s}</div>
                            <div class="panel--table">
                                <div class="panel--tr">
                                    {foreach key=ufKey from=$sBasket['content'][$sBasketItem@key]['uploadFields'][0] item=uploadFields}
                                    <input type="hidden" id="file_upload_page_{$sBasketItem.id}_{$ufKey}"
                                           value="{$uploadFields[0]}"/>

                                    <div class="panel--td">
                                        <label style="font-weight:normal;">
                                            {$uploadFields[0]}<br/>
                                            (Min: {$uploadFields[1]}/Max: {$uploadFields[2]})<br/>
                                        </label>
                                    </div>
                                    {if $uploadFields[3] < $uploadFields[2] || $uploadFields[2]==0}
                                    <div class="panel--td">
                                        <div id="file_upload_button_{$sBasketItem.id}_{$ufKey}"></div>
                                        {if $sWaUp.dropbox}
                                        <div id="dropBoxContainer_{$sBasketItem.id}_{$ufKey}"></div>{/if}
                                    </div>
                                    {if $sWaUp.drag_and_drop_enable}
                                        <div id="upload-drop-area_{$sBasketItem.id}_{$ufKey}"
                                             class="panel--td qq-upload-extra-drop-area"
                                             qq-drop-area-text="Drag'n'Drop"></div>
                                    {/if}
                                    <div class="panel--td column--upload">
                                        <input type="hidden" name="upload-item-limit_{$sBasketItem.id}_{$ufKey}"
                                               id="upload-item-limit_{$sBasketItem.id}_{$ufKey}"
                                               value="{$uploadFields[2]}"/>
                                        <ul id="upload-queue_{$sBasketItem.id}_{$ufKey}"
                                            class="qq-upload-list">
                                        </ul>
                                        {else}
                                        <div style="float:left;width:110px;line-height:40px;margin-left:30px;font-weight:bold;">{s name='maxUploaded' namespace='frontend/basketUploader'}Max. erreicht!{/s}</div>
                                        <div class="panel--td column--upload">
                                        {/if}

                                        {assign var=fileCounter value=0}
                                        <ul class="qq-upload-list-selector qq-upload-list">
                                        {foreach key=pageKey from=$sBasketItem['uploadedFiles'][$uploadFields[0]]['name'] item=uploadedFiles}
                                            {$fileCounter=$fileCounter+1}
                                                {foreach key=fileKey from=$uploadedFiles item=files}
                                                    <li qq-file-id='{$pageKey}'>
                                                        <span class="qq-upload-file-selector qq-upload-file">
                                                            {if $sBasketItem['uploadedFiles'][$uploadFields[0]]['thumbnail'][$pageKey]}
                                                                <img class="qq-thumbnail-selector" qq-max-size="100"
                                                                     qq-server-scale=""
                                                                     src="{$sBasketItem['uploadedFiles'][$uploadFields[0]]['thumbnail'][$pageKey]}"
                                                                     alt="Thumbnail"
                                                                     title="{$sBasketItem['uploadedFiles'][$uploadFields[0]]['name'][$pageKey]}"/>
                                                            {/if}
                                                            <a href="{if $sBasketItem['uploadedFiles'][$uploadFields[0]]['linkType'][$pageKey] == 'internal'}http://{$smarty.server.HTTP_HOST}{/if}{$sBasketItem['uploadedFiles'][$uploadFields[0]]['link'][$pageKey]}"
                                                               target="_blank"
                                                               title="{$sBasketItem['uploadedFiles'][$uploadFields[0]]['name'][$pageKey]}">{$sBasketItem['uploadedFiles'][$uploadFields[0]]['name'][$pageKey]}</a>
                                                        </span>

                                                        <span class="qq-upload-size-selector qq-upload-size">{$sBasketItem['uploadedFiles'][$uploadFields[0]]['size'][$pageKey]}</span>
                                                        <a title="{$sBasketItem['uploadedFiles'][$uploadFields[0]]['name'][$pageKey]} wirklich l&ouml;schen?"
                                                           class="btn is--small column--actions-link right"
                                                           href="{$sBasketItem['uploadedFiles'][$uploadFields[0]]['link'][$pageKey]}"
                                                           id="delUploadedFile_{$sBasketItem.id}_{$ufKey}_{$fileCounter}_{$sBasketItem['uploadedFiles'][$uploadFields[0]]['linkType'][$pageKey]}">
                                                            <i class="icon--cross"></i>
                                                        </a>

                                                    </li>
                                                {/foreach}
                                        {/foreach}
                                        </ul>
                                    </div>

                                </div>
                                <div class="panel--tr">

                                    {/foreach}
                                </div>
                                {if $sWaUp['vorauswahl_enable']==true}
                                    <div class="panel--tr">
                                        <div class="panel--td">
                                            <a href="#" id="change_datatransfer_{$sBasketItem.id}"
                                               class="btn is--small">Datenübertragung ändern</a>
                                        </div>
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {else}
            <div class='panel--td'>
                <strong>{s name='BoxHeader1' namespace='frontend/basketUploader'}Daten hochladen{/s}</strong><br/>
                {s name='UnloggedBoxContent' namespace='frontend/basketUploader'}Bitte melden Sie sich zuerst an. Danach k&ouml;nnen Sie Ihre Daten hochladen.{/s}
                <br/>
                <a href="{url controller='checkout' action='confirm'}"
                   title="{s name='LoginLinkLogon' namespace='frontend/account/login'}{/s}"
                   class="btn btn--checkout-continue is--secondary is--left">
                    {s name='LoginLinkLogon' namespace='frontend/account/login'}{/s}
                </a>
            </div>
        {/if}
    {/if}
{/block}