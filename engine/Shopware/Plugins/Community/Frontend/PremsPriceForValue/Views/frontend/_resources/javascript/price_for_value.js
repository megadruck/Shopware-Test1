;(function ($, document) {
    var PREMSQTYTEXTFIELD = {
        init: function(purchaseSteps){
            var minusButton = $('<div class="premsQuantityMinusButton">-</div>'),
                textfield = $('<div class="premsQuantityTextfield"><input type="text" name="textfieldQuantity" id="textfieldQuantity" /></div>')
                plusButton = $('<div class="premsQuantityPlusButton">+</div>'),
                selectfield = $('.buybox--inner #sQuantity');

            selectfield.after(plusButton).after(textfield).after(minusButton);

            if (selectfield.val()) {
                $(textfield).find('#textfieldQuantity').val(selectfield.val());
            } else {
                $(textfield).find('#textfieldQuantity').val(selectfield.first('option').val());
            }

            $(textfield).find('input').change(function() {
                PREMSQTYTEXTFIELD.changeQuantity(selectfield, textfield, parseInt(textfield.find('#textfieldQuantity').val()))
            });

            $(minusButton).click(function() {
                PREMSQTYTEXTFIELD.changeQuantity(selectfield, textfield, parseInt(textfield.find('#textfieldQuantity').val())-parseInt(purchaseSteps))
            });
            $(plusButton).click(function() {
                PREMSQTYTEXTFIELD.changeQuantity(selectfield, textfield, parseInt(textfield.find('#textfieldQuantity').val())+parseInt(purchaseSteps))
            });

            $('.buybox--quantity .js--fancy-select-text').hide();;
            $('.buybox--quantity .js--fancy-select-trigger').hide();
            $('.buybox--quantity div').removeClass('js--fancy-select');
            selectfield.hide();
        },
        changeQuantity: function(selectfield, textfield, qty) {

            var options = selectfield.find('option')
            var isValid = false;

            if (isNaN(qty)) {
                $(textfield).find('#textfieldQuantity').val(selectfield.first('option').val());
                $(selectfield).val($(textfield).find('#textfieldQuantity').val()).change();
                return;
            }

            options.each(function(){
                var option = $(this);

                if (option.val() == qty) {
                    isValid = true;
                }
            });

            if (isValid) {
                $(textfield).find('#textfieldQuantity').val(qty);
            } else {
                var min = selectfield.first('option').val();
                var max = selectfield.find('option:last-child').val();

                if (qty < min) {
                    $(textfield).find('#textfieldQuantity').val(min);
                }
                if (qty > max) {
                    $(textfield).find('#textfieldQuantity').val(max);
                }
            }

            $(selectfield).val($(textfield).find('#textfieldQuantity').val()).change();
        }
    }

    $.fn.startPlugin = function() {
        new PriceForValue(this);
    };

    $(function() {
        $(".premsPiecesPrice").startPlugin();
    });

    $.subscribe('plugin/swAjaxVariant/onRequestData', function() {
        $(".premsPiecesPrice").startPlugin();
    });

    function PriceForValue(element) {
        var me = this;

        me.element = element;

        me.init();
    }


    PriceForValue.prototype.init = function() {
        var me = this;
        me.config = {};

        $('.prems-price-for-value-config input').each(function(index, item) {
            me.config[item.name] = item.value;
        });

        var lsurl = '';

        if ($('.liveshopping--details').length) {
            lsurl = $('.liveshopping--details').data('dataurl');
        }

        if (me.config.premsLiveShoppingEnabled) {
            $.ajax({
                url: me.config.requestUrl,
                data: $(".buybox--inner .buybox--form, .configurator--form").serialize()+'&lsurl='+lsurl,
                method: 'post',
                //dataType:'json',
                success:function (response) {
                    var resultArea = $('.premsDynPriceArea');
                    resultArea.html(response);
                }
            });
        }

        if (me.config.premsReloadPrices) {
            $('.buybox--inner #sQuantity').bind('change', function(e) {
                $.ajax({
                    url: me.config.requestUrl,
                    data: $(".buybox--inner .buybox--form, .buybox--inner .configurator--form").serialize()+'&lsurl='+lsurl,
                    method: 'post',
                    //dataType:'json',
                    success:function (response) {
                        var resultArea = $('.premsDynPriceArea');
                        resultArea.html(response);
                    }
                });
            });
        }

        if (me.config.premsUseTextfieldForQuantity) {
            PREMSQTYTEXTFIELD.init(me.config.purchaseSteps);
        }
    };
})(jQuery, document);