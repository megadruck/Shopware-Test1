/**
 * Shopware 4.0
 * Copyright © 2013 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * TODO@STP - Set invalidate number based on the required and maxlength
 * HTML attributes
 */
;(function($, window, document, undefined) {

    var LoadingHelper = (function() {

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

    var isInvalid = function (requiredWrapper) {
        var invalid = false;
        requiredWrapper.each(function () {
            $(this).find('div[class^=option_values]').each(function () {
                var $element = $(this),
                    type = $element.attr('class').replace(/.*option_values[_ ]{0,1}/, '');
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
                        var checkBoxes = $element.find('.check-wrapper'),
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
                        var radio = $element.find('.radio-wrapper'),
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
                        if ($element.find('.thumbnail-wrapper').length == 0) {
                            invalid = true;
                        }
                        break;
                }
            });
        });

        return invalid;
    };

    var onBundleButtonClick = function (event) {
        event.preventDefault();

        var $this = $(this),
            requiredWrapper = $('.customizing-field-wrapper.required');

        if (isInvalid(requiredWrapper)) {
            $('#basketButton').trigger('submit');
            return;
        }

        $this.parents('form').submit();
    };

    var rewriteBundleButtons = function (bundleButtons) {

        // Check if the bundle plugin is available, so we can check it using the submit buttons of the plugin
        if (!bundleButtons || !bundleButtons.length) {
            return;
        }

        // Bind event listener to all available bundle buttons
        bundleButtons.on('click', onBundleButtonClick);
    };

    // change additional surcharge info
    var changePriceInfo = function (surchargeData, total) {
        var holder = $('.customizingSurcharge').show(),
            tbody = $(holder).find('tbody');
        addRows(surchargeData, $(tbody).children('tr'), $(tbody));
        deleteRows(surchargeData, $(tbody).children('tr'));
        changeTotal(total);
    };

    var addRows = function(data, rows, tbody) {
        for (var dataId in data) {
            var obj = data[dataId];
            if (!obj.hasOwnProperty('name')) {
                continue;
            }

            var matched = false;
            var id = 'surchargeOptionId-' + dataId;
            for (var key = 0; key < rows.length; key++) {
                var elementId = $(rows[key]).attr('id');
                if (elementId !== id) {
                    continue;
                }

                $(rows[key]).find('.surcharge strong').text(obj.surcharge);
                matched = true;
                break;
            }

            if (!matched) {
                tbody.append("<tr id='surchargeOptionId-" + dataId + "'><td class='name'>" + obj.name + "</td><td class='surcharge'><strong>" + obj.surcharge + "</strong></td></tr>");
            }
        }
    };

    var deleteRows = function(data, rows) {
        for (var key = 0; key < rows.length; key++) {
            var matched = true;
            var elementId = $(rows[key]).attr('id');
            for (var dataId in data) {
                var obj = data[dataId];
                if (!obj.hasOwnProperty('name')) {
                    continue;
                }

                var id = 'surchargeOptionId-' + dataId;
                if (elementId === id) {
                    matched = true;
                    break;
                }

                matched = false;
            }

            if (!matched) {
                $(rows[key]).remove();
            }
        }
    };

    var changeTotal = function(total) {
        $('#customizingTotal strong').text(total);
    };

    var getPriceWithQuantity = function(quantityDropdown) {
        quantityDropdown.on('change', changeQuantityAndPrice);
    };

    var changeQuantityAndPrice = function() {
        var quantity = $(this).val();
        changeQuantity(quantity);
        changePrice(quantity);
    };

    var changeQuantity = function(quantity) {
        $('input[name=currentQuantity]').val(quantity);
    };

    var changePrice = function(quantity) {
        if (customizingBlockPrices.length <= 0) {
            return;
        }
        quantity = parseInt(quantity);

        var newPrice;
        for (var key in customizingBlockPrices) {
            var validTo = customizingBlockPrices[key].valTo,
                valFrom = parseInt(customizingBlockPrices[key].valFrom),
                price = customizingBlockPrices[key].price;

            validTo = (validTo == 'beliebig') ? validTo : parseInt(validTo);

            if (valFrom == 1) {
                newPrice = price;
            }

            if (valFrom <= quantity && validTo >= quantity) {
                newPrice = price;
            } else if (validTo == 'beliebig' && valFrom <= quantity) {
                newPrice = price;
            }
        }

        $('input[name=price]').val(newPrice);

        onChange($('form[name="customizingOptions"]'));
    };

    // init form submit
    var initSubmit = function (form) {
        if ($.support.cors) {
            $.ajax({
                type: "POST",
                dataType: "json",
                data: form.serialize(),
                url: customizingSavePath,
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: true
            });
        } else {
            $.ajax({
                type: "POST",
                dataType: "iframe json",
                formData: form.serializeArray(),
                url: customizingSavePath
            });
        }
    };

    var onChange = function(form) {
        LoadingHelper.changePrevent(true);

        if ($.support.cors) {
            var jqXhr = $.ajax({
                type: "POST",
                dataType: "json",
                data: form.serialize(),
                url: customizingSavePath,
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: true
            }).done(function(result) {
                if (result && result.price) {
                    changePriceInfo(result.price.surcharge, result.price.total);
                } else if (result && !result.price) {
                    $('.customizingSurcharge').hide();
                }
            });
        } else {
            var jqXhr = $.ajax({
                type: "POST",
                dataType: "iframe json",
                formData: form.serializeArray(),
                url: customizingSavePath
            }).done(function(result) {
                if (result && result.price) {
                    changePriceInfo(result.price.surcharge, result.price.total);
                } else if (result && !result.price) {
                    $('.customizingSurcharge').hide();
                }
            });
        }

        jqXhr.done(function() {
            LoadingHelper.changePrevent();
        })
    };

    var resetCustomOptions = function() {
        $.ajax({
            type: "GET",
            dataType: $.support.cors ? "json" : "jsonp",
            xhrFields: { withCredentials: true },
            crossDomain: true,
            url: customizingResetPath
        }).done(function (result) {
            if (result && result.data && result.reset) {
                $.each(result.data, function (type, value) {
                    var field = $('.option_values_' + type),
                        emptyText;

                    //color picker
                    if (field.hasClass('option_values_color_select')) {

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
                    if (field.hasClass('option_values_color_field')) {
                        field.find('[name^="customizingValues"]').val('');
                        field.find('.minicolors-swatch span').removeAttr('style');
                        return;
                    }

                    //date
                    if (field.hasClass('option_values_date')) {
                        field.find('[name^="customizingValues"]').val('');
                        return;
                    }

                    //time
                    if (field.hasClass('option_values_time')) {
                        field.find('option:selected').removeAttr('selected');
                        emptyText = field.find('option:first').text();
                        field.find('.select-text').html(emptyText);
                        return;
                    }

                    //checkbox
                    if (field.hasClass('option_values_checkbox')) {

                        //deselect
                        field.find('[name^="customizingValues"]').attr('checked', false);

                        //select default value
                        if (value) {
                            field.find('#value' + value).prop('checked', true);
                        }
                        return;
                    }

                    //image picker
                    if (field.hasClass('option_values_image_select')) {

                        //deselect
                        field.find('.is-active').removeClass('is-active');
                        field.find('[name^="customizingValues"]').val('');
                        field.find('.image-selection-wrapper input').attr('checked', false);

                        //select default value
                        if (value) {
                            field.find('[name^="customizingValues"]').val(value);
                            field.find('#value' + value).prop('checked', true);
                            field.find('#value' + value).parent().addClass('is-active');
                        }
                        return;
                    }

                    //multiple select box
                    if (field.hasClass('option_values_multiple')) {

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
                    if (field.hasClass('option_values_radio')) {

                        //deselect
                        field.find('[name^="customizingValues"]').attr('checked', false);

                        //select default value
                        if (value) {
                            field.find('#value' + value).prop('checked', true);
                        }
                        return;
                    }

                    //select box
                    if (field.hasClass('option_values_select')) {

                        //deselect
                        emptyText = field.find('option:first').attr('selected', true).text();
                        field.find('.select-text').html(emptyText);

                        //select default value
                        if (value) {
                            emptyText = field.find('option[value=' + value + ']').attr('selected', true).text();
                            field.find('.select-text').html(emptyText);
                        }
                        return;
                    }

                    //html
                    if (field.hasClass('option_values_text_html')) {
                        var iframe = field.find('iframe');
                        iframe.contents().find("body").html('');
                        field.find('[name^="customizingValues"]').val('');
                        return;
                    }

                    //text area
                    if (field.hasClass('option_values_text_area')) {
                        field.find('[name^="customizingValues"]').val('');
                        return;
                    }

                    //text field
                    if (field.hasClass('option_values_text_field')) {
                        field.find('input').val('');
                        return;
                    }

                    //uploads
                    if (field.hasClass('option_values_upload_image') || field.hasClass('option_values_upload_file')) {
                        field.find('.thumbnail-wrapper').remove();
                        return;
                    }
                });
            }
        });
    };

    // Wait 'till the document is ready
    $(function () {

        rewriteBundleButtons($('.add-bundle'));

        var $fields = $('.customizing-field-wrapper input, .customizing-field-wrapper select, .customizing-field-wrapper textarea'),
            $form = $('form[name="customizingOptions"]'),
            changeTimer;

        // init form submit
        initSubmit($form);

        // overload submit button - so change requests can finish before button submit
        $('#basketButton').click(function(event) {

            if ($form.length) {
                resetCustomOptions();
            }

            if(!LoadingHelper.isPrevented()) {
                return;
            }

            event.preventDefault();

            var loaderClass = 'swag-customizing';
            var el = $(this);
            var loader = el.nextAll('.ajax_loader');

            LoadingHelper.addResolveHandler(function() {
                loader.removeClass(loaderClass);
                el.click();
            });

            loader.addClass(loaderClass);
        });

        $('.ajax_add_article .modal_close').live('click', function() {
            if ($form.length) {
                resetCustomOptions();
            }
        });

        getPriceWithQuantity($('#sQuantity'));

        $.ajax({
            type: "GET",
            dataType: $.support.cors ? "json" : "jsonp",
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            url: customizingSavePath
        }).done(function (result) {
            if (result && result.data) {
                $.each(result.data, function (optionId, value) {
                    if (!optionId || !value) {
                        return;
                    }
                    // For select / input values
                    var $option = $('#option' + optionId),
                        $field = $option.parent('.option_values'),
                        deleteUrl = customizingUploadUrl + '?_method=DELETE&file=',
                        thumbnailUrl, $value;

                    if ($field.hasClass('option_values_upload_image')) {
                        thumbnailUrl = customizingUploadUrl + '?download=1&version=thumbnail&file='
                    }
                    if ($option.hasClass('fileupload-input')) {
                        $.each(value, function (i, val) {
                            if (!val) {
                                return;
                            }
                            addUpload($option.parent(), {
                                name: val,
                                delete_url: deleteUrl + encodeURIComponent(val),
                                thumbnail_url: thumbnailUrl ? thumbnailUrl + encodeURIComponent(val) : null
                            });
                        });
                        return;
                    }
                    if ($option.hasClass('image-select')) {
                        $value = $('#value' + value);
                        if ($value.length) {
                            $option.val('');
                            $value.parent().click();
                        }
                        return;
                    }
                    if ($option.hasClass('color-select')) {
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
                    if ($option.hasClass('date-picker')) {
                        $.each(value, function (x, val) {
                            var $input = $('input[name="customizingValues[' + optionId + '][' + x + ']"]');
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
                    if ($option.hasClass('select-input')) {
                        $option.val(value).change();
                        return;
                    }
                    if ($option.length) {
                        $option.val(value);
                        return;
                    }

                    //Time field
                    if(value.hours) {
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
                        var $value = $('#value' + val);
                        if ($value.length) {
                            $value.attr('checked', true);
                            return;
                        }
                    });
                });
            }
            $fields.live('change', function (e) {
                clearTimeout(changeTimer);
                changeTimer = setTimeout(function() {
                    onChange($form);
                }, 100);
            });
            $fields.live('input', function (e) {
                clearTimeout(changeTimer);
                changeTimer = setTimeout(function() {
                    onChange($form);
                }, 100);
            });
        });

        var $wysiwyg = $('.option_values_text_html textarea');
        $wysiwyg.each(function () {
            var $this = $(this);
            $wysiwyg.wysiwyg({
                css: customizingEditorStylePath || undefined,
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

        var numberFields = $(".option_values_number input");
        numberFields.keydown(function (event) {
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

        var $resetButton = $('.option-reset');
        $resetButton.on('click', function(event) {
           var eventEmitter = event.currentTarget,
               optionId = eventEmitter.id,
               field = $('#option_value_' + optionId),
               selectBox = $('#option' + optionId);

            var emptyText = selectBox.find('option[id=option-default]').attr('selected', 'selected').text();
            field.find('.select-text').html(emptyText);
            onChange($('form[name="customizingOptions"]'));
        });

        var $dateFields = $('input[data-date-picker="true"]');
        if ($dateFields && $dateFields.length) {
            $dateFields.each(function () {
                var $input = $(this),
                    inputID = $input.attr('id'),
                    altFieldID = inputID + '-submit',
                    $altField = $('#' + altFieldID),
                    dateFormat = $input.attr('data-date-format') || 'dd.mm.yy';

                $input.datepicker({
                    dateFormat: dateFormat,
                    altField: $altField,
                    altFormat: 'dd.mm.yy',
                    showOn: "button",
                    onSelect: function () {
                        $input.change();
                    }
                });
            });
        }

        var $colorSelects = $('.option_values_color_field input');
        if ($colorSelects && $colorSelects.length) {
            $colorSelects.minicolors({
                change: function () {
                    $(this).change();
                }
            });
        }

        var $colorSwatches = $('.option_values_color_select .color-select-swatch');
        if ($colorSwatches && $colorSwatches.length) {
            var tipTip = $('<div>', { 'class': 'swag-customizing-tip-tip' }).appendTo($('body')),
                timeout = undefined;

            $colorSwatches.bind('click.color-swatch', function () {
                var $this = $(this),
                    $parent = $this.parent('.option_values_color_select'),
                    $field = $parent.find('input:hidden'),
                    $childs = $parent.find('.color-select-swatch'),
                    $value = $this.attr('data-value');

                $childs.removeClass('is-active');
                if ($field.val() != $value) {
                    $this.addClass('is-active');
                    $field.val($this.attr('data-value'));
                } else {
                    $field.val('');
                }
                $field.change();
            });

            $colorSwatches.bind('mouseenter.color-swatch', function () {
                var $this = $(this),
                    offset = $this.offset(),
                    val = $this.attr('data-tiptip');

                if (timeout !== undefined) {
                    window.clearTimeout(timeout);
                }

                if (!val) {
                    return false;
                }

                offset.left += 40;
                tipTip.html(val).css(offset);

                timeout = window.setTimeout(function () {
                    tipTip.fadeIn();
                }, 250);
            });
            $colorSwatches.bind('mouseleave.color-swatch', function () {
                if (timeout !== undefined) {
                    window.clearTimeout(timeout);
                }
                tipTip.hide();
            });
        }

        var $imageSelect = $('.option_values_image_select');
        if ($imageSelect && $imageSelect.length) {
            $imageSelect.find('.image-selection-wrapper').bind('click', function (e) {
                var $this = $(this),
                    $field = $this.find('input[type=checkbox]'),
                    parent = $this.parents('.option_values_image_select'),
                    hidden = parent.find('input:hidden'),
                    value = $field.val();

                parent.find('.image-selection-wrapper').removeClass('is-active');
                parent.find('input[type=checkbox]').attr('checked', false);

                if (hidden.val() != value) {
                    $field.attr('checked', true);
                    $field.parents('.image-selection-wrapper').addClass('is-active');
                    hidden.val(value).change();

                    if (typeof value == 'undefined') {
                        $this.addClass('is-active');
                    }
                } else {
                    hidden.val('').change();
                }
            });

            var selectedImage = $imageSelect.find('input:hidden');
            if (selectedImage.val() == '') {
                selectedImage.parent().find('.default').addClass('is-active');
            }
        }

        function addUpload($upload, file) {
            var $field = $upload.find('.fileupload-input'),
                $container = $('<p>', { 'class': 'thumbnail-wrapper' }).appendTo($upload),
                elementId = $($field).attr('id').replace('option', '');
            file.delete_url = file.delete_url + '&optionId=' + elementId;
            if (file.error) {
                var $error = $('<div class="error"/>')
                    .css('margin', '1em 0 0')
                    .text(file.error).appendTo($container);

                window.setTimeout(function () {
                    $error.slideUp('slow', function () {
                        $container.remove();
                    })
                }, 2000);
            } else {
                if (file.thumbnail_url) {
                    var $thumb = $('<img/>')
                        .attr('src', file.thumbnail_url)
                        .prependTo($container);
                } else {
                    var $thumb = $('<div>', {
                        'class': 'item-box'
                    }).prependTo($container);
                }

                var $info = $('<div>', { 'class': 'thumbnail-info' }).html(file.name).appendTo($container);
                var $input = $('<input type="hidden">')
                    .attr('name', $field.attr('name') + '[]')
                    .attr('value', file.name).appendTo($container).change();

                var $remove = $('<a>', { 'class': 'remove-item', 'html': 'X' })
                    .attr('href', file.delete_url || '#')
                    .click(function (event) {
                        $fields.first().change();
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
                            }).done(function () {
                                onChange($('form[name="customizingOptions"]'));
                            });
                        }
                        event.preventDefault();
                    })
                    .appendTo($container);
                $('<div>', { 'class': 'clear' }).appendTo($container);
            }
        }

        var $fileUploads = $('.option_values_upload_file, .option_values_upload_image');
        $fileUploads.each(function () {
            var $this = $(this),
                $dropZone = $this.find('.fileupload-dropzone'),
                $field = $this.find('.fileupload-input'),
                $progress = $('<div class="progress progress-striped active">')
                    .append('<div class="bar">')
                    .append('<div class="message">')
                    .hide()
                    .appendTo($this);

            var input = $($this).find('.fileupload-input');
            var elementId = $(input).attr('id').replace('option', '');
            var uploadUrl = customizingUploadUrl + '?optionId=' + elementId;

            var input = $($this).find('.fileupload-input');
            var elementId = $(input).attr('id').replace('option', '');
            var uploadUrl = customizingUploadUrl + '?optionId=' + elementId;

            $dropZone.bind('click', function() {
                $field.click();
            });

            if (!$.support.xhrFileUpload) {
                $dropZone.hide();
            }
            $field.fileupload({
                dataType: 'json',
                dropZone: $dropZone,
                url: uploadUrl,
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
                        addUpload($this, file);
                    });
                }
            });
        });

        var $charges = $('.customizing-charges-popup');
        $charges.each(function () {
            var $list = $(this),
                $icon = $list.prev();
            $list.appendTo(document.body);
            $icon.bind('click', function () {
                if (!$icon.hasClass('active')) {
                    var offset = $icon.offset();
                    offset.left -= $list.width() + 40;
                    offset.top -= 20;
                    $list.css(offset);
                    $list.fadeIn('fast');
                    $icon.addClass('active');
                    var timeout = window.setTimeout(function () {
                        window.clearTimeout(timeout);
                        timeout = undefined;
                        $('body').bind('click.customizing-charges', function () {
                            $icon.click();
                        });
                    }, 200);
                } else {
                    $list.fadeOut('fast');
                    $icon.removeClass('active');
                    $('body').unbind('click.customizing-charges');
                }
            });
        });
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
                initalWidth = $this.is(':hidden') ? $this.width() + 3 : $this.width() - 12,
                selected = $this.find(':selected'),
                initalText = selected.html(),
                template = createTemplate(initalWidth, initalText);

            template.insertBefore($this);
            $this.appendTo(template).width(initalWidth);

            if ($this.hasClass('instyle_error')) {
                template.addClass('instyle_error');
            }

            template.bind('mouseenter', function () {
                $(this).addClass('hovered');
            });
            template.bind('mouseleave', function () {
                $(this).removeClass('hovered');
            });

            $this.bind('change', function () {
                var $select = $(this),
                    selected = $select.find(':selected');

                template.find('.select-text').html(selected.html());
            })
        });
    }
})(jQuery, window, document);