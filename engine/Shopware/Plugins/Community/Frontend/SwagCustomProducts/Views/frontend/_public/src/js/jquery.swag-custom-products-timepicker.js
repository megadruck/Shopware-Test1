;(function ($) {
    'use strict';

    $.plugin('swagCustomProductsTimePicker', {

        /** @object Default plugin configuration */
        defaults: {
            parentWrapperSelector: '.custom-products--option',
            hiddenName: true,
            format: 'H:i',
            min: null,
            max: null
        },

        /**
         * Initializes the plugin
         *
         * @returns void
         */
        init: function () {
            var me = this,
                opts;

            me.applyDataAttributes();
            me.formatMinMaxTime();

            opts = $.extend({}, { clear: jQuery.fn.pickadate.defaults.clear }, me.opts);

            me.$el.pickatime(opts);

            me.$parent = me.$el.parents(me.opts.parentWrapperSelector);
            me.$hiddenField = me.$parent.find('input[type="hidden"]');

            $.publish('plugin/swagCustomProductsTimePicker/init', [me]);
        },

        /**
         * format the timeString
         */
        formatMinMaxTime: function () {
            var me = this;

            if (me.opts.min) {
                me.opts.min = me.formatTimeArray(me.opts.min);
            } else {
                me.opts.min = undefined;
            }

            if (me.opts.max) {
                me.opts.max = me.formatTimeArray(me.opts.max);
            } else {
                me.opts.max = undefined;
            }
        },

        /**
         * format the timeString from "2008-01-01 10:00:00" to:
         * [
         *      10,
         *      00
         * ]
         *
         * @param {string} value
         * @returns {Array}
         */
        formatTimeArray: function (value) {
            var array = value.split(' '),
                timeArray;

            if (array[1]) {
                timeArray = array[1].split(':');
                timeArray.splice(2, 1);

                for (var i = 0; i < timeArray.length; i++) {
                    timeArray[i] = parseInt(timeArray[i]);
                }

                return timeArray;
            }
        },

        /**
         * Sets the value for the time picker and the hidden field.
         *
         * @param {*} val
         */
        setValue: function (val) {
            var me = this;

            val = val || '';

            me.$el.val(val);
            me.$hiddenField.val(val);

            $.publish('plugin/swagCustomProductsTimePicker/setValue', [ me, val ]);
        },

        /**
         * Returns the value of the hidden element.
         * @returns {*}
         */
        getValue: function () {
            var me = this;

            return me.$hiddenField.val(val);
        }
    });

    $(function () {
        StateManager.addPlugin('*[data-custom-products-timepicker="true"]', 'swagCustomProductsTimePicker');
    });
})(jQuery);
