{extends file="parent:frontend/checkout/confirm.tpl"}

{block name="frontend_index_header_javascript_jquery_lib" append}
    <!-- include dropbox js -->
    <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs"
            data-app-key="wdjxa7pvixhw0zw"></script>
    <!-- upload template -->
    <script type="text/template" id="qq-template">
        <div class="qq-uploader-selector qq-uploader">
            <div class="qq-upload-button-selector btn is--secondary">
                <div>{s name='buttonText' namespace='frontend/basketUploader'}W&auml;hlen{/s}</div>
            </div>
            <span class="qq-drop-processing-selector qq-drop-processing">
                <span>Processing dropped files...</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
            <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
                <li>
                    <div class="qq-progress-bar-container-selector">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                             class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                    <span class="qq-edit-filename-icon-selector qq-edit-filename-icon"
                          aria-label="Edit filename"></span>
                    <span class="qq-upload-file-selector qq-upload-file"></span>
                    <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                    <span class="qq-upload-size-selector qq-upload-size"></span>
                    <a class="qq-upload-retry-selector" href="#">Wiederholen</a>
                    <a class="qq-upload-pause-selector" href="#">Pause</a>
                    <a class="qq-upload-continue-selector" href="#">Weiter</a>
                    <a class="qq-upload-cancel-selector" href="#">Abbrechen</a>
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button class="qq-cancel-button-selector">Close</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button class="qq-cancel-button-selector">No</button>
                    <button class="qq-ok-button-selector">Yes</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">

                <div class="qq-dialog-buttons">
                    <button class="qq-cancel-button-selector">Cancel</button>
                    <button class="qq-ok-button-selector">Ok</button>
                </div>
            </dialog>
        </div>
    </script>
    <script type="text/javascript">
        $(document).ready(
                function () {
                    $.loadingIndicator.open();

                    // get upload config
                    $.getJSON("{url controller=Basketuploader action=getUploadConfig forceSecure}",
                            function (data) {
                                // delete upload
                                $("a[id^='delUploadedFile_']").click(function (e) {
                                    e.preventDefault();

                                    var thisUpload = $(this).attr('id'), thisUploadExplode = thisUpload.split('_'),
                                            basketItemID = thisUploadExplode[1] + '_' + thisUploadExplode[2] + '_' + thisUploadExplode[3];

                                    var file_upload_page = $('#file_upload_page_' + thisUploadExplode[1] + '_' + thisUploadExplode[2]).val();

                                    $.post("{url controller=Basketuploader action=deleteUpload forceSecure}", {
                                        basketID: basketItemID,
                                        page: file_upload_page,
                                        path: $(this).attr('href'),
                                        sUploadDir: data.uploadPath,
                                        type: thisUploadExplode[4],
                                        uuid: ""
                                    }, function (data) {
                                        var obj = jQuery.parseJSON(data);
                                        if (obj.success == true) {
                                            location.href = "{url}";
                                        } else if (obj.success == false) {
                                            window.location.reload();
                                        }
                                    });
                                });

                                // each upload buttons
                                $("div[id^='file_upload_button_']").each(
                                        function (index) {
                                            var thisUpload = $(this).attr('id'), thisUploadExplode = thisUpload.split('_'),
                                                    basketItemID = thisUploadExplode[3] + '_' + thisUploadExplode[4];

                                            var file_upload_page = $('#file_upload_page_' + basketItemID).val();

                                            var itemLimit = parseInt($("#upload-item-limit_" + basketItemID).val());

                                            // select upload type
                                            $('#selector #select_' + thisUploadExplode[3]).click(function (e) {
                                                e.preventDefault();

                                                switch ($(this).attr('href')) {
                                                    case '/perUpload':
                                                        $.post("{url controller=Basketuploader action=changeUploadTransfer forceSecure}", {
                                                            utStatus: "1",
                                                            basketID: thisUploadExplode[3]
                                                        }, function (data) {
                                                            if (data == 1) {
                                                                window.location.reload();
                                                            }
                                                        });
                                                        break;
                                                    case '/perEMail':
                                                        $.post("{url controller=Basketuploader action=insertUpload forceSecure}", {
                                                            sessionID: data.sessionID,
                                                            basketID: thisUploadExplode[3],
                                                            filename: "per e-Mail",
                                                            filesize: '0',
                                                            filepath: data.uploadPath,
                                                            page: file_upload_page,
                                                            linkType: "perMail",
                                                            uuid: ''
                                                        }, function (data) {
                                                            if (data == 1) {
                                                                $.post("{url controller=Basketuploader action=changeUploadTransfer forceSecure}", {
                                                                    utStatus: "2",
                                                                    basketID: thisUploadExplode[3],
                                                                    del: '0'
                                                                }, function (data) {
                                                                    if (data == 1) {
                                                                        window.location.reload();
                                                                    }
                                                                });
                                                            }
                                                        });
                                                        break;
                                                    case '/perPost':
                                                        $.post("{url controller=Basketuploader action=insertUpload forceSecure}", {
                                                            sessionID: data.sessionID,
                                                            basketID: thisUploadExplode[3],
                                                            filename: "per Post",
                                                            filesize: '0',
                                                            filepath: data.uploadPath,
                                                            page: file_upload_page,
                                                            linkType: "perPost",
                                                            uuid: ''
                                                        }, function (data) {
                                                            if (data == 1) {
                                                                $.post("{url controller=Basketuploader action=changeUploadTransfer forceSecure}", {
                                                                    utStatus: "3",
                                                                    basketID: thisUploadExplode[3],
                                                                    del: '0'
                                                                }, function (data) {
                                                                    if (data == 1) {
                                                                        window.location.reload();
                                                                    }
                                                                });
                                                            }
                                                        });
                                                        break;
                                                }
                                            });

                                            // change upload type
                                            $('a#change_datatransfer_' + thisUploadExplode[3]).click(function (e) {
                                                e.preventDefault();

                                                $.post("{url controller=Basketuploader action=changeUploadTransfer forceSecure}", {
                                                    utStatus: "0",
                                                    basketID: thisUploadExplode[3],
                                                    del: '1'
                                                }, function (data) {
                                                    if (data == 1) {
                                                        window.location.reload();
                                                    }
                                                });
                                            });

                                            // define fineUploader
                                            $('div#' + thisUpload).fineUploader({
                                                debug: data.debug_enable,
                                                request: {
                                                    params: {
                                                        "sUploadDir": data.uploadPath,
                                                        "sizeLimit": data.file_size,
                                                        "sendThumbnailUrl": !qq.supportedFeatures.imagePreviews
                                                    },
                                                    endpoint: '{url controller=Basketuploader action=transferUpload forceSecure}'
                                                },
                                                chunking: {
                                                    enabled: data.chunking_enable,
                                                    partSize: data.chunking_partsize
                                                },
                                                resume: {
                                                    enabled: data.resume_enable
                                                },
                                                validation: {
                                                    allowedExtensions: eval('[' + data.file_type + ']'),
                                                    itemLimit: itemLimit,
                                                    sizeLimit: data.file_size
                                                },
                                                dragAndDrop: {
                                                    extraDropzones: [$("#upload-drop-area_" + basketItemID)]
                                                },
                                                listElement: $("#upload-queue_" + basketItemID),
                                                classes: {
                                                    buttonHover: "",
                                                    buttonFocus: ""
                                                },
                                                text: {
                                                    failUpload: "Upload fehlgeschlagen",
                                                    waitingForResponse: "Bearbeitung...",
                                                    paused: "Upload wurde angehalten"
                                                },
                                                retry: {
                                                    autoRetryNote: 'Wiederholen'
                                                },
                                                deleteFile: {
                                                    deletingStatusText: "Löschen...",
                                                    deletingFailedText: "Löschen gescheitert"
                                                }

                                                // upload complete
                                            }).on("complete", function (event, id, name, responseJSON, xhr) {
                                                var basketID = $(this).attr('id').split("_");
                                                var thumbnail = $('ul#upload-queue_' + basketID[3] + '_' + basketID[4] + ' .qq-file-id-'+id);
                                                thumbnail = thumbnail.find('.qq-thumbnail-selector');


                                                $.post("{url controller=Basketuploader action=insertUpload forceSecure}", {
                                                    sessionID: data.sessionID,
                                                    basketID: basketID[3],
                                                    filename: "" + name + "",
                                                    filesize: responseJSON.uploadSize,
                                                    filepath: data.uploadPath,
                                                    page: file_upload_page,
                                                    linkType: "internal",
                                                    uuid: responseJSON.uuid,
                                                    thumbnail:thumbnail[0].src
                                                });

                                                // all upload complete
                                            }).on("allComplete", function (event, status) {
                                                setTimeout(function () {
                                                    window.location.reload();
                                                }, 1000);
                                            });
                                            // define drobbox
                                            if (data.dropbox) {
                                                var button = Dropbox.createChooseButton({
                                                    success: function (files) {
                                                        $.loadingIndicator.open();
                                                        $.post("{url controller=Basketuploader action=insertUpload forceSecure}",
                                                                {
                                                                    sessionID: data.sessionID,
                                                                    basketID: basketItemID,
                                                                    filename: files[0].name,
                                                                    filelink: files[0].link,
                                                                    filethumbnaillink: files[0].thumbnailLink,
                                                                    filepath: data.uploadPath,
                                                                    page: file_upload_page,
                                                                    linkType: "dropbox"
                                                                }, function (data) {
                                                                    if (data == 1) {
                                                                        $.loadingIndicator.close();
                                                                        window.location.reload();
                                                                    }
                                                                });
                                                    },
                                                    multiselect: false,
                                                    linkType: 'preview',
                                                    extensions: [data.dropbox_file_types]
                                                });
                                                $('#dropBoxContainer_' + basketItemID).html(button);
                                            }
                                        }
                                );
                                $.loadingIndicator.close();
                            }
                    );
                }
        );
    </script>
{/block}

{block name='frontend_checkout_confirm_submit'}
    {if $checkSubmitButton>0}
        {* Submit order button *}
        {if $sPayment.embediframe || $sPayment.action}
            <button type="submit" class="btn is--primary is--large right is--icon-right" form="confirm--form"
                    data-preloader-button="true">
                {s name='ConfirmDoPayment'}{/s}<i class="icon--arrow-right"></i>
            </button>
        {else}
            <button type="submit" class="btn is--primary is--large right is--icon-right" form="confirm--form"
                    data-preloader-button="true">
                {s name='ConfirmActionSubmit'}{/s}<i class="icon--arrow-right"></i>
            </button>
        {/if}
    {else}
        <div class="right">
            {include file="frontend/_includes/messages.tpl" type="warning" content="{se name="CheckoutActionsLinkProceedMsg" namespace='frontend/basketUploader'}Laden Sie zuerst alle n&ouml;tigen Dateien hoch{/se}"}
        </div>
    {/if}
{/block}