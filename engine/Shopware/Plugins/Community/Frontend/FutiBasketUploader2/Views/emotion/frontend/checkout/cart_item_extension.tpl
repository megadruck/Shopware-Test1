{block name="frontend_index_header_javascript" append}
    <script type="text/javascript"
            src="{link file='frontend/_resources/javascript/jquery.fine-uploader-5.1.2.min.js'}"></script>
    {if $sWaUp.dropbox}
        <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs"
                data-app-key="wdjxa7pvixhw0zw"></script>
    {/if}
{/block}

{block name='frontend_index_header_css_screen' append}
    <link rel="stylesheet" href="{link file='frontend/_resources/styles/fine-uploader-5.1.2.min.css'}"/>
    <link rel="stylesheet" href="{link file='frontend/_resources/styles/basketuploader.css'}"/>
{/block}

{block name='frontend_checkout_cart_item_delete_article' append}
    <div style="clear:both;"></div>
    {if isset($sBasket['content'][$key]['uploadFields'])}

        {if $sUserLoggedIn}
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#selector #select_{$sBasketItem.id}').click(function (e) {
                        e.preventDefault();
                        $.loadingIndicator.open();

                        switch ($(this).attr('href')) {
                            case '/perUpload':
                                $.post("{url controller=Basketuploader action=changeUploadTransfer forceSecure}", {
                                    utStatus: "1",
                                    basketID: "{$sBasketItem.id}"
                                }, function (data) {
                                    if (data == 1) {
                                        $.loadingIndicator.close();
                                        window.location.href = "{url}";
                                    }
                                });
                                break;
                            case '/perEMail':
                                $.post("{url controller=Basketuploader action=insertUpload forceSecure}", {
                                    sessionID: "{$sUserData.additional.user.sessionID}",
                                    basketID: "{$sBasketItem.id}",
                                    filename: "per e-Mail",
                                    filesize: '0',
                                    filepath: "{$sWaUp.uploadPath}",
                                    page: "{$sBasket['content'][$key]['uploadFields'][0][0][0]}",
                                    linkType: "perMail",
                                    uuid: ''
                                }, function (data) {
                                    if (data == 1) {
                                        $.post("{url controller=Basketuploader action=changeUploadTransfer forceSecure}", {
                                            utStatus: "2",
                                            basketID: "{$sBasketItem.id}",
                                            del: '0'
                                        }, function (data) {
                                            if (data == 1) {
                                                $.loadingIndicator.close();
                                                window.location.href = "{url}";
                                            }
                                        });
                                    }
                                });
                                break;
                            case '/perPost':
                                $.post("{url controller=Basketuploader action=insertUpload forceSecure}", {
                                    sessionID: "{$sUserData.additional.user.sessionID}",
                                    basketID: "{$sBasketItem.id}",
                                    filename: "per Post",
                                    filesize: '0',
                                    filepath: "{$sWaUp.uploadPath}",
                                    page: "{$sBasket['content'][$key]['uploadFields'][0][0][0]}",
                                    linkType: "perPost",
                                    uuid: ''
                                }, function (data) {
                                    if (data == 1) {
                                        $.post("{url controller=Basketuploader action=changeUploadTransfer forceSecure}", {
                                            utStatus: "3",
                                            basketID: "{$sBasketItem.id}",
                                            del: '0'
                                        }, function (data) {
                                            if (data == 1) {
                                                $.loadingIndicator.close();
                                                window.location.href = "{url}";
                                            }
                                        });
                                    }
                                });
                                break;
                        }
                    });
                    $('a#change_datatransfer_{$sBasketItem.id}').click(function (e) {
                        e.preventDefault();
                        $.loadingIndicator.open();

                        $.post("{url controller=Basketuploader action=changeUploadTransfer forceSecure}", {
                            utStatus: "0",
                            basketID: "{$sBasketItem.id}",
                            del: '1'
                        }, function (data) {
                            if (data == 1) {
                                $.loadingIndicator.close();
                                window.location.href = "{url}";
                            }
                        });
                    });
                });
            </script>
            {$selectStatus = "selection_status_"|cat:$sBasketItem.id}
            <div id="eMailTransferNotice" class="notice"
                 style="width:310px;margin-left:10px;text-align:left;{if $sWaUp[$selectStatus]==0 || $sWaUp[$selectStatus]==1 || $sWaUp[$selectStatus]==3}display:none;{/if}">
                Sie haben die Übertragungsart per e-Mail ausgewählt.
                <div class="space"></div>
                <a href="#" id="change_datatransfer_{$sBasketItem.id}" class="button-middle small">Datenübertragung
                    ändern</a>
            </div>
            <div id="postTransferNotice" class="notice"
                 style="width:310px;margin-left:10px;text-align:left;{if $sWaUp[$selectStatus]==0 || $sWaUp[$selectStatus]==1 || $sWaUp[$selectStatus]==2}display:none;{/if}">
                Sie haben die Übertragungsart per Post ausgewählt.
                <div class="space"></div>
                <a href="#" id="change_datatransfer_{$sBasketItem.id}" class="button-middle small">Datenübertragung
                    ändern</a>
            </div>
            <div id="selectorBox" class="notice"
                 style="text-align:left;margin-left:10px;{if $sWaUp[$selectStatus]==1 || $sWaUp[$selectStatus]==2 || $sWaUp[$selectStatus]==3 || $sWaUp['vorauswahl_enable']==false}display:none;{/if}">
                Treffen sie Ihre Auswahl wie Sie uns Ihre Daten übertragen möchten.<br/>

                <div class="space"></div>
                <ul id="selector">
                    <li style="float:left;"><a class="button-right" href="/perUpload"
                                               id="select_{$sBasketItem.id}">{$sWaUp.auswahl1}</a></li>
                    {if $sWaUp.auswahl2}
                        <li style="float:left;"><a class="button-right" href="/perEMail"
                                                   id="select_{$sBasketItem.id}">{$sWaUp.auswahl2}</a></li>{/if}
                    {if $sWaUp.auswahl3}
                        <li style="float:left;"><a class="button-right" href="/perPost"
                                                   id="select_{$sBasketItem.id}">{$sWaUp.auswahl3}</a></li>{/if}
                </ul>
                <div class="clear"></div>
            </div>
            <!-- Template -->
            <script type="text/template" id="qq-template">
                <div class="qq-uploader-selector qq-uploader">
                    {** old drag and drop zone
                    <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                        <span>{s name='dropText' namespace='frontend/basketUploader'}Ziehen Sie Ihre Daten hier her{/s}</span>
                    </div>
                    **}
                    <div class="qq-upload-button-selector button-right">
                        <div>{s name='buttonText' namespace='frontend/basketUploader'}W&auml;hlen{/s}</div>
                    </div>

                    <span class="qq-drop-processing-selector qq-drop-processing">
                      <span>Processing dropped files...</span>
                      <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
                    </span>
                    <ul class="qq-upload-list-selector qq-upload-list">
                        <li>
                            <div class="qq-progress-bar-container-selector">
                                <div class="qq-progress-bar-selector qq-progress-bar"></div>
                            </div>
                            <span class="qq-edit-filename-icon-selector qq-edit-filename-icon"></span>
                            <span class="qq-upload-file-selector qq-upload-file"></span>
                            <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                            <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                            <span class="qq-upload-size-selector qq-upload-size"></span>
                            <a class="qq-upload-cancel-selector qq-upload-cancel" href="#">Abbrechen</a>
                            <a class="qq-upload-retry-selector qq-upload-retry" href="#">Wiederholen</a>
                            <a class="qq-upload-pause-selector btn-small btn-info" href="#">Pause</a>
                            <a class="qq-upload-continue-selector btn-small btn-info" href="#">Weiter</a>
                            <span class="qq-upload-status-text-selector qq-upload-status-text"></span>
                        </li>
                    </ul>
                </div>
            </script>
            <script type="text/javascript">
                $(document).ready(function () {
                    {foreach key=ufKey from=$sBasket['content'][$key]['uploadFields'][0] item=uploadFields}
                        {if $uploadFields[3] < $uploadFields[2] || $uploadFields[2]==0}
                            $('#file_upload_'+{$sBasketItem.id}+'_' +{$ufKey}).fineUploader({
                                debug: {if $sWaUp.debug_enable==1}true{else}false{/if},
                                request: {
                                    params: { "sUploadDir": "{$sWaUp.uploadPath}", "sizeLimit": "{$sWaUp.file_size}" },
                                    endpoint: '{url controller=Basketuploader action=transferUpload forceSecure}'
                                },
                                chunking: {
                                    enabled: {if $sWaUp.chunking_enable==1}true{else}false{/if},
                                    partSize: {$sWaUp.chunking_partsize} // bytes
                                },
                                resume: {
                                    enabled: {if $sWaUp.resume_enable==1}true{else}false{/if}
                                },
                                validation: {
                                    allowedExtensions: [{$sWaUp.file_type}],
                                    itemLimit: {$uploadFields[2]},
                                    sizeLimit: {$sWaUp.file_size} // bytes
                                },
                                dragAndDrop: {
                                    extraDropzones: [$("#file_upload_draganddrop_"+{$sBasketItem.id}+"_" +{$ufKey})]
                                },
                                dropZoneElements:[$("#file_upload_draganddrop_"+{$sBasketItem.id}+"_" +{$ufKey})],
                                listElement: $("#upload-queue_"+{$sBasketItem.id}+"_" +{$ufKey}),
                                classes: {
                                    buttonHover: "",
                                    buttonFocus:""
                                },
                                text: {
                                    failUpload: "Upload fehlgeschlagen",
                                    waitingForResponse: "Bearbeitung...",
                                    paused: " | Upload wurde angehalten"
                                },
                                retry: {
                                    autoRetryNote: 'Wiederholen'
                                },
                                deleteFile: {
                                    deletingStatusText: "Löschen...",
                                    deletingFailedText: "Löschen gescheitert"
                                },
                                messages: {
                                    tooManyFilesError: 'Sie können nur eine Datei löschen',
                                    unsupportedBrowser: 'Fehler � dieser Browser ermöglicht nicht das Hochladen von Dateien aller Art. Bitte nutzen Sie alternative den Mozilla Firefox oder Google Chrome.'
                                }
                            }).on("complete", function (event, id, name, responseJSON, xhr) {
                                $.post("{url controller=Basketuploader action=insertUpload forceSecure}", {
                                    sessionID: "{$sUserData.additional.user.sessionID}",
                                    basketID: "{$sBasketItem.id}",
                                    filename: "" + name + "",
                                    filesize: responseJSON.uploadSize,
                                    filepath: "{$sWaUp.uploadPath}",
                                    page: "{$uploadFields[0]}",
                                    linkType: "internal",
                                    uuid: responseJSON.uuid
                                });
                            }).on("allComplete", function (event, status) {
                                $.loadingIndicator.open();
                                setTimeout(function () {
                                    $.loadingIndicator.close();
                                    window.location.href = "{url}";
                                }, 1000);
                            });

                            /** drobox */
                            {if $sWaUp.dropbox}
                            var button = Dropbox.createChooseButton({
                                success: function (files) {
                                    $.loadingIndicator.open();
                                    $.post("{url controller=Basketuploader action=insertUpload forceSecure}", {
                                        sessionID: "{$sUserData.additional.user.sessionID}",
                                        basketID: "{$sBasketItem.id}",
                                        filename: files[0].name,
                                        filelink: files[0].link,
                                        filethumbnaillink: files[0].thumbnailLink,
                                        filepath: "{$sWaUp.uploadPath}",
                                        page: "{$uploadFields[0]}",
                                        linkType: "dropbox"
                                    }, function (data) {
                                        if (data == 1) {
                                            $.loadingIndicator.close();
                                            window.location.href = "{url}";
                                        }
                                    });
                                },
                                linkType: "preview",
                                multiselect: false,
                                extensions: [{$sWaUp.dropbox_file_types}],
                            });
                            $('#dropBoxContainer_'+{$sBasketItem.id}+'_' +{$ufKey}).html(button);
                            {/if}
                        {/if}
                    {/foreach}
                });
            </script>
            <div class="notice"
                 style="margin-left:10px;text-align:left;color:#000000;{if $sWaUp['vorauswahl_enable']==true && $sWaUp[$selectStatus]==0 || $sWaUp[$selectStatus]==2 || $sWaUp[$selectStatus]==3}display:none;{/if}">
                <div id="upload_box_{$sBasketItem.id}"
                     style="{if $sWaUp['vorauswahl_enable']==true && $sWaUp[$selectStatus]==0 || $sWaUp[$selectStatus]==2 || $sWaUp[$selectStatus]==3}display:none;{/if}">
                    <div class='uploadBox'>
                        <div>
                            <strong>{s name='BoxHeader1' namespace='frontend/basketUploader'}Daten hochladen{/s}</strong>
                            <hr style="border-top:1px solid #DFDFDF;width:100%;margin-bottom:5px;"/>
                        </div>
                        <div style="clear:both;"></div>

                        <div>
                            {foreach key=ufKey from=$sBasket['content'][$key]['uploadFields'][0] item=uploadFields}
                                <div style="float:left;"><label style="font-weight:normal;">
                                        {$uploadFields[0]}<br/>
                                        (Min: {$uploadFields[1]}/Max: {$uploadFields[2]})<br/>
                                    </label></div>
                                {if $uploadFields[3] < $uploadFields[2] || $uploadFields[2]==0}
                                    <div style="float:left;width:170px;margin-left:30px;">
                                        <div id="file_upload_{$sBasketItem.id}_{$ufKey}"></div>
                                        {if $sWaUp.drag_and_drop_enable}
                                            <div id="file_upload_draganddrop_{$sBasketItem.id}_{$ufKey}"
                                                 class="qq-upload-extra-drop-area">{s name='dropText' namespace='frontend/basketUploader'}Ziehen Sie Dateien hierher{/s}</div>{/if}
                                        {if $sWaUp.dropbox}
                                            <div id="dropBoxContainer_{$sBasketItem.id}_{$ufKey}"></div>{/if}
                                    </div>
                                    <div style="float:right;width:650px;">
                                        <ul id="upload-queue_{$sBasketItem.id}_{$ufKey}" class="qq-upload-list"></ul>
                                    </div>
                                {else}
                                    <div style="float:left;width:110px;line-height:40px;margin-left:30px;font-weight:bold;">{s name='maxUploaded' namespace='frontend/basketUploader'}Max. erreicht!{/s}</div>
                                {/if}
                                <div style="float:right;width:650px;">
                                    {assign var=fileCounter value=0}
                                    {foreach key=pageKey from=$sBasketItem['uploadedFiles'][$uploadFields[0]]['name'] item=uploadedFiles}
                                        {$fileCounter=$fileCounter+1}
                                        <div class="qq-uploader-selector qq-uploader">
                                            <ul class="qq-upload-list-selector qq-upload-list">
                                                {foreach key=fileKey from=$uploadedFiles item=files}
                                                    <li qq-file-id='{$pageKey}'>

                                                    <span class="qq-upload-file-selector qq-upload-file">
                                                        <a href="{if $sBasketItem['uploadedFiles'][$uploadFields[0]]['linkType'][$pageKey] == 'internal'}http://{$smarty.server.HTTP_HOST}{/if}{$sBasketItem['uploadedFiles'][$uploadFields[0]]['link'][$pageKey]}"
                                                           target="_blank"
                                                           title="{$sBasketItem['uploadedFiles'][$uploadFields[0]]['name'][$pageKey]}">{$sBasketItem['uploadedFiles'][$uploadFields[0]]['name'][$pageKey]}</a>
                                                    </span>

                                                        <span class="qq-upload-size-selector qq-upload-size">{$sBasketItem['uploadedFiles'][$uploadFields[0]]['size'][$pageKey]}</span>
                                                        <a class="qq-upload-delete-selector qq-upload-delete del"
                                                           href="#"
                                                           id="delUploadedData_{$sBasketItem.id}_{$ufKey}_{$fileCounter}"
                                                           title="{$sBasketItem['uploadedFiles'][$uploadFields[0]]['name'][$pageKey]} wirklich l&ouml;schen?"></a>

                                                        <script type="text/javascript">
                                                            $(document).ready(function () {
                                                                $('#delUploadedData_{$sBasketItem.id}_{$ufKey}_{$fileCounter}').click(function () {
                                                                    $.loadingIndicator.open();
                                                                    $.post("{url controller=Basketuploader action=deleteUpload forceSecure}", {
                                                                        basketID: "{$sBasketItem.id}",
                                                                        page: "{$uploadFields[0]}",
                                                                        path: "{$sBasketItem['uploadedFiles'][$uploadFields[0]]['link'][$pageKey]}",
                                                                        sUploadDir: "{$sWaUp.uploadPath}",
                                                                        uuid: "{$sBasketItem['uploadedFiles'][$uploadFields[0]]['uuid'][$pageKey]}"
                                                                    }, function (data) {
                                                                        var obj = jQuery.parseJSON(data);
                                                                        if (obj.success == true) {
                                                                            $.loadingIndicator.close();
                                                                            location.href = "{url}";
                                                                        }
                                                                    });
                                                                    return false;
                                                                });
                                                            });
                                                        </script>
                                                    </li>
                                                {/foreach}
                                            </ul>
                                        </div>
                                    {/foreach}
                                </div>
                                <div style="clear:both;">&nbsp;</div>
                            {/foreach}
                        </div>

                        <div style="clear:both;"></div>

                        {if $sWaUp['vorauswahl_enable']==true}<a href="#" id="change_datatransfer_{$sBasketItem.id}"
                                                                 class="button-middle small">Datenübertragung
                            ändern</a>{/if}

                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
        {else}
            <div style="clear:both;"></div>
            <div class='loginBox'>
                <strong>{s name='BoxHeader1' namespace='frontend/basketUploader'}Daten hochladen{/s}</strong>
                <hr style="border-top:1px solid #DFDFDF;width:100%;"/>
                <a href="{url controller='checkout'}/cart"
                   title="{s name='LoginLinkLogon' namespace='frontend/account/login'}{/s}" class="button-right login">
                    {s name='LoginLinkLogon' namespace='frontend/account/login'}{/s}
                </a><br/>
                {s name='UnloggedBoxContent' namespace='frontend/basketUploader'}Bitte melden Sie sich zuerst an. Danach k&ouml;nnen Sie Ihre Daten hochladen.{/s}
            </div>
        {/if}
    {/if}
{/block}

{block name="frontend_checkout_actions_confirm"}
    {if $checkSubmitButton>0}
        <a href="{url action=confirm}"
           title="{s name='CheckoutActionsLinkProceed' namespace='frontend/checkout/actions'}{/s}"
           class="button-right large right checkout">
            {se name="CheckoutActionsLinkProceed" namespace='frontend/checkout/actions'}{/se}
        </a>
    {else}
        <div class='CheckoutActionsLinkProceedMsg'>
            {se name="CheckoutActionsLinkProceedMsg" namespace='frontend/basketUploader'}Laden Sie zuerst alle n&ouml;tigen Dateien hoch{/se}
        </div>
        <div style="clear:both;"></div>
    {/if}
{/block}

{block name='frontend_checkout_confirm_submit'}
    {* Submit order button *}
    {if $checkSubmitButton>0}
        <div class="actions">
            {if $sPayment.embediframe || $sPayment.action}
                <input type="submit" class="button-right large" id="basketButton"
                       value="{s name='ConfirmDoPayment' namespace='frontend/checkout/confirm'}Zahlungspflichtig bestellen{/s}"/>
            {else}
                <input type="submit" class="button-right large" id="basketButton"
                       value="{s name='ConfirmActionSubmit' namespace='frontend/checkout/confirm'}{/s}"/>
            {/if}
        </div>
    {else}
        <a title="{s name='CheckoutActionsLinkProceedMsg' namespace='frontend/basketUploader'}Laden Sie zuerst alle n&ouml;tigen Dateien hoch{/s}"
           class="button-right large right checkout" style='right:100px;'>
            {se name="CheckoutActionsLinkProceedMsg" namespace='frontend/basketUploader'}Laden Sie zuerst alle n&ouml;tigen Dateien hoch{/se}
        </a>
    {/if}
{/block}