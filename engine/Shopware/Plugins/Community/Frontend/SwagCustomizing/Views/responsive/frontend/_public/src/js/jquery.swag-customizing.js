;(function ($, window, document, undefined) {
    $.plugin('swCustomize', {
        alias: 'swagCustomize',

        /**
         * Plugin constructor
         */
        init: function () {
            var me = this;
            if (typeof jsCustomizingObject !== 'undefined') {

                me.$fields = $('.customizing--field-wrapper input:not(*[class=customizing--fileupload-input]), .customizing--field-wrapper select, .customizing--field-wrapper textarea');
                me.$form = $('form[name="customizingOptions"]');
                me.savePath = jsCustomizingObject.customizingSavePath;
                me.uploadPath = jsCustomizingObject.customizingUploadUrl;
                me.stylePath = jsCustomizingObject.customizingEditorStylePath;
                me.resetPath = jsCustomizingObject.customizingResetPath;

                $('.reset--configuration').off('click');

                me.createLoadingHelper();
                me.createButtonLoadingHelper();
                me.registerEvents();
                me.initSubmit();
                me.initAjax();
                me.initFields();
            }
        },

        createLoadingHelper: function() {

            /**
             * Simple subobject ta capsulate loading
             */
            this.LoadingHelper = (function() {

                /**
                 * o => nothing to indicate
                 * < 0 => there are requests
                 *
                 * @type {number}
                 */
                var indicator = 0;

                /**
                 * Handlers to call after loading
                 * @type {Array}
                 */
                var resolveHandlers = [];

                function callResolveListeners() {
                    for(var i = 0; i < resolveHandlers.length; i++) {
                        resolveHandlers[i]();
                    }

                    resolveHandlers = [];
                }

                return {

                    /**
                     * add a callback
                     *
                     * @param callable
                     * @returns {this.LoadingHelper}
                     */
                    addResolveHandler: function(callable) {
                        resolveHandlers.push(callable);
                        return this;
                    },

                    /**
                     * is loading prevented
                     *
                     * @returns {boolean}
                     */
                    isPrevented: function() {
                        return indicator > 0;
                    },

                    /**
                     * add remove a loader
                     *
                     * @param add - true => add, false|undefined => remove
                     * @returns {this.LoadingHelper}
                     */
                    changePrevent: function(add) {
                        if(add) {
                            indicator++;
                            return;
                        }

                        indicator--;

                        if(!this.isPrevented()) {
                            callResolveListeners();
                        }

                        return this;
                    }
                };
            } ());

        },

        createButtonLoadingHelper: function() {
            this.ButtonLoadingHelper = (function() {
                var _DATA_KEY_ = 'swag-customizing-id';
                var _LOADING_CLASS_ = 'swag-customizing-loader';

                var storeCurrentIconClassAttribute = function(jqElemnt) {
                    jqElemnt.data(_DATA_KEY_, jqElemnt.attr('class'));
                };

                var retrieveClassAttribute = function(jqElemnt) {
                    return jqElemnt.data(_DATA_KEY_);
                };

                var getIconItem = function(jqElemnt) {
                    return jqElemnt.find('i');
                };

                return {
                    setLoading: function(button) {
                        var icon = getIconItem(button);

                        storeCurrentIconClassAttribute(icon);

                        button.prop('disabled', true);

                        icon.removeAttr('class')
                            .addClass(_LOADING_CLASS_);

                        return this;
                    },
                    resetLoading: function(button) {
                        var icon = getIconItem(button);
                        var classInformation = retrieveClassAttribute(icon);

                        button.prop('disabled', false);

                        icon.removeAttr('class')
                            .attr('class', classInformation);

                        return this;
                    }
                };
            } ());
        },

        /**
         * Method to register all needed events
         */
        registerEvents: function () {
            var me = this;

            me._on('.buybox--button-container button', 'click', $.proxy(me.onAddToCartSubmit, me));
            me._on('.bundle-header--price-container button.btn.is--primary', 'click', $.proxy(me.onBundleButtonClick, me));
            me._on('.customizing--upload-btn', 'click', $.proxy(me.onClickUpload, me));
            me._on('.customizing--charges-popup-icon', 'click', $.proxy(me.onOpenChargeInfo, me));
            me._on('.option--reset', 'click', $.proxy(me.onOptionReset, me));
        },

        /**
         * This method will be called if the customer clicks on the 'reset option' button next to a selectbox.
         * It will reset the selectbox to the default values.
         * @param event
         */
        onOptionReset: function (event) {
            var me = this,
                eventEmitter = event.currentTarget,
                optionId = eventEmitter.id,
                selectBox = $('#option' + optionId);

            //Set the selectbox index to 0 which is the default value
            $("#option" + optionId + " option[id='option--default']").attr("selected", "selected");
            var fancySelectBoxPlugin = selectBox.data('plugin_swSelectboxReplacement');

            if (fancySelectBoxPlugin !== undefined) {
                fancySelectBoxPlugin.val();
            }

            clearTimeout(me.changeTimer);
            me.changeTimer = setTimeout(function() {
                me.onChange()
            }, 100);
         },

        /**
         * Helper method to trigger the change-event once the upload-fields have finished uploading
         */
        triggerUploadChange: function () {
            var me = this;

            clearTimeout(me.changeTimer);
            me.changeTimer = setTimeout(function() {
                me.onChange()
            }, 100);
        },

        /**
         * This is triggered when a user clicks the buy button
         *
         * @param event
         */
        onAddToCartSubmit: function(event) {
            var me = this;

            if (me.$form.length) {
                me.resetCustomOptions();
            }

            if(!this.LoadingHelper.isPrevented()) {
                return;
            }

            var buyBoxButton = me.$customitingLoader || $('.buybox--button-container button');

            event.preventDefault();
            me.ButtonLoadingHelper.setLoading(buyBoxButton);

            this.LoadingHelper.addResolveHandler(function() {
                me.ButtonLoadingHelper.resetLoading(buyBoxButton);
                buyBoxButton.click();
            });
        },

        /**
         * Helper method to style the file-/image-uploader by overlaying the original button and clicking it with this function
         * @param event
         */
        onClickUpload: function(event) {
            var $el = $(event.currentTarget);

            event.preventDefault();

            $el.parent().find('.customizing--fileupload-input').click();
        },

        /**
         * Event is triggered when someone clicks on the bundle-button.
         * This method enables compatibility between these plugins
         * @param event
         */
        onBundleButtonClick: function (event) {
            event.preventDefault();

            var $this = $(event.currentTarget),
                me = this,
                requiredWrapper = $('.customizing--field-wrapper.required');

            if (me.isInvalid(requiredWrapper)) {
                $('.buybox--button-container button').click();
                return;
            }

            if(me.LoadingHelper.isPrevented()) {
                me.ButtonLoadingHelper.setLoading($this);
                me.LoadingHelper.addResolveHandler(function() {
                    me.ButtonLoadingHelper.resetLoading($this);
                    $this.click();
                });
                return;
            }

            $this.parents('form').submit();
            $.publish('plugin/swCustomizing/onBundleButtonClick');

            /** @deprecated updated to 'sw' prefixed version */
            $.publish('plugin/swagCustomizing/onBundleButtonClick');
        },

        /**
         * This event is called when the charges are supposed to be called.
         * It gets triggered by clicking on the "i"-icon
         * @param event
         */
        onOpenChargeInfo: function (event) {
            var $popup = $(event.target).parents('.customizing--field-wrapper').find('.customizing--charges-popup'),
                method = ($popup.hasClass('is--hidden')) ? 'removeClass' : 'addClass';

            event.preventDefault();

            $popup[method]('is--hidden');
            $.publish('plugin/swCustomizing/onOpenChargeInfo');

            /** @deprecated updated to 'sw' prefixed version */
            $.publish('plugin/swagCustomizing/onOpenChargeInfo');
        },

        /**
         * This helper-method checks for invalid fields
         * @param requiredWrapper
         * @returns {boolean}
         */
        isInvalid: function (requiredWrapper) {
            var invalid = false;
            requiredWrapper.each(function () {
                $(this).find('div[class^=customizing--option-values]').each(function () {
                    var $element = $(this),
                        type = $element.attr('class').replace(/.*customizing--option-values[- ]{0,1}/, '');
                    switch (type) {
                        case 'image_select':
                        case 'color_select':
                        case 'color_field':
                        case 'date':
                        case 'date_time':
                        case 'text_field':
                            if (!$element.find('input[id^=option]').attr('value')) {
                                invalid = true;
                            }
                            break;
                        case 'checkbox':
                            var checkBoxes = $element.find('.customizing--check-wrapper'),
                                checkBoxesChecked = false;
                            checkBoxes.each(function () {
                                if ($(this).find('input').is(':checked')) {
                                    checkBoxesChecked = true;
                                }
                            });
                            if (!checkBoxesChecked) {
                                invalid = true;
                            }
                            break;
                        case 'multiple':
                        case 'select':
                            var options = $element.find('select option'),
                                optionSelected = false;
                            options.each(function () {
                                if ($(this).is(':selected') && $(this).attr('value')) {
                                    optionSelected = true;
                                }
                            });
                            if (!optionSelected) {
                                invalid = true;
                            }
                            break;
                        case 'radio':
                            var radio = $element.find('.customizing--radio-wrapper'),
                                radioChecked = false;
                            radio.each(function () {
                                if ($(this).find('input').is(':checked')) {
                                    radioChecked = true;
                                }
                            });
                            if (!radioChecked) {
                                invalid = true;
                            }
                            break;
                        case 'text_html':
                        case 'text_area':
                            if (!$element.find('textarea[id^=option]').attr('value')) {
                                invalid = true;
                            }
                            break;
                        case 'upload_image':
                        case 'upload_file':
                            if ($element.find('.customizing--thumbnail-wrapper').length === 0) {
                                invalid = true;
                            }
                            break;
                    }
                });
            });

            return invalid;
        },

        /**
         * This method inits the first call of the save-method to initiate the form.
         */
        initSubmit: function () {
            var me = this;

            if ($.support.cors) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: me.$form.serialize(),
                    url: me.savePath,
                    xhrFields: {
                        withCredentials: true
                    },
                    crossDomain: true
                });
            } else {
                $.ajax({
                    type: "POST",
                    dataType: "iframe json",
                    formData: me.$form.serializeArray(),
                    url: me.savePath
                });
            }
        },

        /**
         * This event is called everytime any custom field is changed in any way to save the new values
         */
        onChange: function () {
            var me = this,
                jqXhr,
                form = me.$form || $('form[name="customizingOptions"]'),
                savePath = me.savePath || jsCustomizingObject.customizingSavePath;

            me.LoadingHelper.changePrevent(true);

            if ($.support.cors) {
                jqXhr = $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: form.serialize(),
                    url: savePath,
                    xhrFields: {
                        withCredentials: true
                    },
                    crossDomain: true
                });
            } else {
                jqXhr = $.ajax({
                    type: "POST",
                    dataType: "iframe json",
                    formData: form.serializeArray(),
                    url: savePath
                });
            }

            jqXhr.done(function(result) {
                me.LoadingHelper.changePrevent();
                if (result && result.price) {
                    me.changePriceInfo(result.price.surcharge, result.price.total);
                } else {
                    $('.customizing--charges-panel').addClass('is--hidden');
                }
            });
        },

        handleResponse: function(result) {
            var me = this;
            $.each(result.data, function (optionId, value) {
                if (!optionId || !value) {
                    return;
                }

                // For select / input values
                var $option = $('#option' + optionId),
                    $field = $option.parent('.customizing--option-values'),
                    deleteUrl = me.uploadPath + '?_method=DELETE&file=',
                    thumbnailUrl, $value;

                if ($field.hasClass('customizing--option-values-upload-image')) {
                    thumbnailUrl = me.uploadPath + '?download=1&version=thumbnail&file=';
                }
                if ($option.hasClass('customizing--fileupload-input')) {
                    $.each(value, function (i, val) {
                        if (!val) {
                            return;
                        }
                        me.addUpload($option.parent(), {
                            name: val,
                            delete_url: deleteUrl + encodeURIComponent(val),
                            thumbnail_url: thumbnailUrl ? thumbnailUrl + encodeURIComponent(val) : null
                        });
                    });
                    return;
                }
                if ($option.hasClass('customizing--image-select')) {
                    $value = $('#value' + parseInt(value));

                    if ($value.length) {
                        $option.val('');
                        $value.parent().click();
                    }
                    return;
                }
                if ($option.hasClass('customizing--color-select')) {
                    $value = $('#value' + value);
                    if ($option.val() != value) {
                        $value.click();
                    }
                    return;
                }
                if ($option.hasClass('wysiwyg-input')) {
                    $option.wysiwyg('setContent', value);
                    return;
                }
                if ($option.attr('data-date-picker')) {
                    $.each(value, function (x, val) {
                        var $input = $('input[name="customizingValues[' + optionId + '][' + x + ']-show"]');
                        if ($input.length) {
                            $input.val(val);
                            return;
                        }
                        var $select = $('select[name="customizingValues[' + optionId + '][' + x + ']"]');
                        if ($select.length) {
                            $select.val(val).change();
                            return;
                        }
                    });
                    return;
                }
                if ($option.hasClass('customizing--select-input')) {
                    $option.val(value).change();
                    return;
                }

                if ($option.length) {
                    $option.val(value);
                    return;
                }

                //Time field
                if(value.date) {
                    $.each(value, function (x, val) {
                        var $select = $('select[name="customizingValues[' + optionId + '][' + x + ']"]');

                        if ($select.length) {
                            $select.val(val).change();
                            return;
                        }
                    });
                    return;
                }

                // For radio values
                value = $.isArray(value) ? value : [value];
                // For radio / checkbox values
                $.each(value, function (x, val) {
                    try {
                        var $value = $('#value' + val);
                        if ($value.length) {
                            $value.attr('checked', true);
                            return;
                        }
                    } catch (error) {}
                });
            });
        },

        /**
         * This method is doing the first ajax call.
         */
        initAjax: function () {
            var me = this;
            $.ajax({
                type: "GET",
                dataType: $.support.cors ? "json" : "jsonp",
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: true,
                url: me.savePath
            }).done(function (result) {
                if (result && result.data) {
                    me.handleResponse(result);
                }
                me.$fields.on('change', function (e) {
                    clearTimeout(me.changeTimer);
                    me.changeTimer = setTimeout(function() {
                        me.onChange();
                    }, 100);
                });
                me.$fields.on('input', function (e) {
                    clearTimeout(me.changeTimer);
                    me.changeTimer = setTimeout(function() {
                        me.onChange();
                    }, 100);
                });
            });
        },

        /**
         * Helper method to upload something
         * @param $upload
         * @param file
         */
        addUpload: function ($upload, file) {
            var me = this,
                $field = $upload.find('.customizing--fileupload-input'),
                $container = $('<p>', { 'class': 'customizing--thumbnail-wrapper' }).appendTo($upload),
                name = file.name;

            if (file.error) {
                var $error = $('<div>', {
                    'class': 'error',
                    'css': {
                        'margin': '1em 0 0'
                    },
                    text: file.error
                }).appendTo($container).delay(2000).slideUp('slow', function() {
                    $container.remove();
                });
            } else {
                var $imgContainer = $('<span>', {
                    'class': 'customizing--img-container'
                }).prependTo($container);

                if (file.thumbnail_url) {
                    var $thumb = $('<img/>', {
                        'src': file.thumbnail_url,
                        'title': file.name
                    }).prependTo($imgContainer);

                    var $imgInfo = $('<div>', {
                        'html': name,
                        'class': 'customizing--image-name'
                    }).appendTo($container);
                } else {
                    var $thumb = $('<div>', {
                        'class': 'customizing--item-box'
                    }).prependTo($imgContainer);

                    var $fileInfo = $('<div>', {
                        'html': name,
                        'class': 'customizing--file-name'
                    }).appendTo($container);
                }
                var $remove = $('<a>', {
                    'class': 'customizing--remove-item btn is--small',
                    'href': file.delete_url || '#',
                    'html': $('<i>', {
                        'class': 'icon--cross'
                    })
                }).on('click', function (event) {
                    me.$fields.first().change();
                    $container.remove();
                    if (file.delete_url) {
                        $.ajax({
                            type: "POST",
                            dataType: $.support.cors ? "json" : "jsonp",
                            url: file.delete_url,
                            xhrFields: {
                                withCredentials: true
                            },
                            crossDomain: true
                        });
                    }
                    event.preventDefault();
                }).appendTo($imgContainer);

                var $input = $('<input>', {
                    type: 'hidden',
                    name: $field.attr('name') + '[]',
                    value: name
                }).appendTo($container);

                me.triggerUploadChange();
            }
        },

        /**
         * Method to first initiate the custom fields
         */
        initFields: function () {
            var me = this,
                $wysiwyg = $('.customizing--option-values-text-html textarea');

            $wysiwyg.each(function () {
                var $this = $(this);

                $this.wysiwyg({
                    css: me.stylePath || undefined,
                    rmUnusedControls: true,
                    initialContent: $this.attr('placeholder'),
                    controls: {
                        bold: { visible: true },
                        italic: { visible: true },
                        underline: { visible: true },
                        createLink: { visible: true },
                        justifyLeft: { visible: true },
                        justifyCenter: { visible: true },
                        justifyRight: { visible: true },
                        html: { visible: true }
                    }
                });
            });

            var numberFields = $(".customizing--option-values-number input");
            numberFields.on('keydown', function (event) {
                // Allow: backspace, delete, tab, escape, and enter
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
                            // Allow: Ctrl+A
                        (event.keyCode == 65 && event.ctrlKey === true) ||
                            // Allow: home, end, left, right
                        (event.keyCode >= 35 && event.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
                else {
                    // Ensure that it is a number and stop the keypress
                    if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                        event.preventDefault();
                    }
                }
            });

            var $dateFields = $('input[data-date-picker="true"]');
            if ($dateFields && $dateFields.length) {
                $dateFields.each(function () {
                    var $input = $(this),
                        inputID = $input.attr('id'),
                        altFieldID = inputID + '-submit';

                    $input.datepicker($.extend({}, { altField: $('#' + altFieldID) }, $.datePickerSettings));
                });
            }

            var $colorSelects = $('.customizing--option-values-color-field input');
            if ($colorSelects && $colorSelects.length) {
                $colorSelects.minicolors({
                    change: function () {
                        $(this).change();
                    }
                });
            }
            var $colorSwatches = $('.customizing--option-values-color-select .customizing--color-select-swatch');
            if ($colorSwatches && $colorSwatches.length) {
                var tipTip = $('<div>', { 'class': 'swag-customizing--tip-tip' }).appendTo($('body'));

                $colorSwatches.on('click', function () {
                    var $this = $(this),
                        $field = $this.parent('.customizing--option-values-color-select').find('input:hidden'),
                        $value = $this.attr('data-value');

                    $colorSwatches.removeClass('is-active');
                    if ($field.val() != $value) {
                        $this.addClass('is-active');
                        $field.val($this.attr('data-value'));
                    } else {
                        $field.val('');
                    }
                    $field.change();
                });

                $colorSwatches.on('mouseenter', function () {
                    var $this = $(this),
                        offset = $this.offset(),
                        val = $this.attr('data-tiptip');

                    if (!val) {
                        return false;
                    }

                    //Displays the tiptip-info a little more to the right. Otherwise the tiptip-overlay would hide behind the color-select itself
                    offset.left += 40;
                    tipTip.html(val).css(offset).delay(250).fadeIn();
                });
                $colorSwatches.on('mouseleave', function () {
                    tipTip.hide();
                });
            }

            var $imageSelect = $('.customizing--option-values-image-select');
            if ($imageSelect && $imageSelect.length) {
                $imageSelect.find('.customizing--image-selection-wrapper').on('click', function (e) {
                    var $this = $(this),
                        $field = $this.find('input[type=checkbox]'),
                        parent = $this.parents('.customizing--option-values-image-select'),
                        hidden = parent.find('input:hidden'),
                        value = $field.val();

                    parent.find('.customizing--image-selection-wrapper').removeClass('is-active');
                    parent.find('input[type=checkbox]').each(function(iterator, item) {
                        item.checked = false;
                    });

                    if (hidden.val() != value) {
                        $field.get(0).checked = true;
                        $field.parents('.customizing--image-selection-wrapper').addClass('is-active');
                        hidden.val(value).change();
                    } else {
                        hidden.val('').change();
                    }
                });
            }

            var $fileUploads = $('.customizing--option-values-upload-file, .customizing--option-values-upload-image');
            $fileUploads.each(function () {
                var $this = $(this),
                    $dropZone = $this.find('.customizing--fileupload-dropzone'),
                    $field = $this.find('.customizing--fileupload-input'),
                    $progress = $('<div class="progress progress-striped active">')
                            .append('<div class="bar">')
                            .append('<div class="message">')
                            .hide()
                            .appendTo($this);

                var elementId = $field.attr('data-optionId');

                $dropZone.bind('click', function() {
                    $field.click();
                });

                if (!$.support.xhrFileUpload) {
                    $dropZone.hide();
                }
                $field.fileupload({
                    dataType: 'json',
                    dropZone: $dropZone,
                    url: me.uploadPath + '?optionId=' + elementId,
                    paramName: 'files',
                    redirect: 1,
                    //forceIframeTransport: true,
                    formData: function (form) {
                        return [];
                    },
                    start: function (e, data) {
                        if ($.support.xhrFileUpload) {
                            $progress.children('.bar')
                                    .css('width', 0);
                        } else {
                            $progress.children('.bar')
                                    .css('width', '100%');
                        }
                        $progress.show();
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $progress.children('.bar')
                                .css('width', '' + progress + '%');
                    },
                    done: function (e, data) {
                        $progress.hide();
                        $.each(data.result.files, function (index, file) {
                            me.addUpload($this, file);
                        });
                    }
                });
            });
        },

        resetCustomOptions: function() {
            var me = this;

            $.ajax({
                type: "GET",
                dataType: $.support.cors ? "json" : "jsonp",
                xhrFields: { withCredentials: true },
                crossDomain: true,
                url: me.resetPath
            }).done(function (result) {
                if (result && result.data && result.reset) {
                    $.each(result.data, function (type, value) {
                        var field = $('.customizing--option-values-' + type),
                            emptyText;

                        //color picker
                        if (field.hasClass('customizing--option-values-color-select')) {

                            //deselect
                            var el = field.find('[name^="customizingValues"]').val('');
                            field.find('.is-active').removeClass('is-active');

                            //select default value
                            if (value) {
                                el.val(value);
                                field.find('#value' + value).addClass('is-active');
                            }
                            return;
                        }

                        //color selector
                        if (field.hasClass('customizing--option-values-color-field')) {
                            field.find('[name^="customizingValues"]').val('');
                            field.find('.minicolors-swatch span').removeAttr('style');
                            return;
                        }

                        //date
                        if (field.hasClass('customizing--option-values-date')) {
                            field.find('[name^="customizingValues"]').val('');
                            return;
                        }

                        //time
                        if (field.hasClass('customizing--option-values-time')) {
                            field.find('option:selected').removeAttr('selected');
                            emptyText = field.find('option:first').text();
                            field.find('.js--fancy-select-text').html(emptyText);
                            return;
                        }

                        //checkbox
                        if (field.hasClass('customizing--option-values-checkbox')) {

                            //deselect
                            field.find('[name^="customizingValues"]').attr('checked', false);

                            //select default value
                            if (value) {
                                field.find('#value' + value).prop('checked', true);
                            }
                            return;
                        }

                        //image picker
                        if (field.hasClass('customizing--option-values-image-select')) {

                            //deselect
                            field.find('.is-active').removeClass('is-active');
                            field.find('[name^="customizingValues"]').val('');
                            field.find('.customizing--image-select-checkbox-input').attr('checked', false);

                            //select default value
                            if (value) {
                                field.find('[name^="customizingValues"]').val(value);
                                field.find('#value' + value).prop('checked', true);
                                field.find('#value' + value).parent().addClass('is-active');
                            }
                            return;
                        }

                        //multiple select box
                        if (field.hasClass('customizing--option-values-multiple')) {

                            //deselect
                            field.find('option:selected').prop('selected', false);

                            //select default value
                            if (value) {
                                field.find('option[value=' + value + ']').prop('selected', true);
                            } else {
                                field.find('option:first').prop('selected', true);
                            }
                            return;
                        }

                        //radio button
                        if (field.hasClass('customizing--option-values-radio')) {

                            //deselect
                            field.find('[name^="customizingValues"]').attr('checked', false);

                            //select default value
                            if (value) {
                                field.find('#value' + value).prop('checked', true);
                            }
                            return;
                        }

                        //select box
                        if (field.hasClass('customizing--option-values-select')) {

                            //deselect
                            emptyText = field.find('option:first').attr('selected', true).text();
                            field.find('.js--fancy-select-text').html(emptyText);

                            //select default value
                            if (value) {
                                emptyText = field.find('option[value=' + value + ']').attr('selected', true).text();
                                field.find('.js--fancy-select-text').html(emptyText);
                            }
                            return;
                        }

                        //html
                        if (field.hasClass('customizing--option-values-text-html')) {
                            var iframe = field.find('iframe');
                            iframe.contents().find("body").html('');
                            field.find('[name^="customizingValues"]').val('');
                            return;
                        }

                        //text area
                        if (field.hasClass('customizing--option-values-text-area')) {
                            field.find('[name^="customizingValues"]').val('');
                            return;
                        }

                        //text field
                        if (field.hasClass('customizing--option-values-text-field')) {
                            field.find('input').val('');
                            return;
                        }

                        //uploads
                        if (field.hasClass('customizing--option-values-upload-image') || field.hasClass('customizing--option-values-upload-file')) {
                            field.find('.customizing--thumbnail-wrapper').remove();
                            return;
                        }
                    });
                }
            });
        },

        /** Change additional surcharge info */
        changePriceInfo: function(surchargeData, total) {
            var me = this,
                holder = $('.customizing--charges-panel');

            holder.removeClass('is--hidden');

            holder.find('.customizing--table-row:not(:last)').remove();
            me.addRows(surchargeData);

            holder.find('.total strong').text(total);
        },

        /** Adde new row in additional surcharge info box */
        addRows: function(surchargeData) {
            var totalRow = $('.customizing--charges-panel .customizing--table-row:last');

            for (var dataId in surchargeData) {
                var obj = surchargeData[dataId];

                var parent = $('<div>', { 'class': 'customizing--table-row panel--tr' }),
                    name = $('<div>', { 'class': 'customizing--row-name panel--td' }).text(obj.name).appendTo(parent),
                    value = $('<div>', { 'class': 'customizing--table-row-value panel--td' }).appendTo(parent),
                    text = $('<strong>').text(obj.surcharge).appendTo(value);

                parent.insertBefore(totalRow);
            }
        },

        /**
         * Destroys the plugin
         */
        destroy: function () {
            this._destroy();
        }
    });

    /** Overrides over default fancy select plugin */
    $.fn.fancySelect = function () {

        function createTemplate(width, text) {
            if (width < 50) {
                width = 50;
            }
            var outer = $('<div>', { 'class': 'outer-select' }).css('width', width),
                inner = $('<div>', { 'class': 'inner-select' }).appendTo(outer),
                text = $('<span>', { 'class': 'select-text', 'html': text }).appendTo(inner);

            return outer;
        }

        return this.each(function () {

            if ($(this).hasClass('no-fancy')) {
                return;
            }

            var $this = $(this),
                initalWidth = $this.is(':hidden') ? $this.width() + 3 : $this.width() + 15,
                selected = $this.find(':selected'),
                initalText = selected.html(),
                template = createTemplate(initalWidth, initalText);

            template.insertBefore($this);
            $this.appendTo(template).width(initalWidth);

            if ($this.hasClass('instyle-error')) {
                template.addClass('instyle-error');
            }

            template.on('mouseenter', function () {
                $(this).addClass('hovered');
            });
            template.on('mouseleave', function () {
                $(this).removeClass('hovered');
            });

            $this.on('change', function () {
                var $select = $(this),
                    selected = $select.find(':selected');

                template.find('.select-text').html(selected.html());
            });
        });
    };

    $(document).ready(function () {
        $('.customizing--plugin').swCustomize();
    });
})(jQuery, window, document);