;(function ($) {
    'use strict';

    $.plugin('swagCustomProductsDatePicker', {

        /** @object Default plugin configuration */
        defaults: {
            parentWrapperSelector: '.custom-products--option',
            hiddenName: true,
            formatSubmit: 'yyyy-mm-dd',
            disable: null,
            max: null,
            min: null
        },

        /**
         * Initializes the plugin
         *
         * @returns void
         */
        init: function () {
            var me = this;

            me.applyDataAttributes();
            me.formatMinMaxDate();

            me.$el.pickadate(me.opts);

            me.$parent = me.$el.parents(me.opts.parentWrapperSelector);
            me.$hiddenField = me.$parent.find('input[type="hidden"]');

            $.publish('plugin/swagCustomProductsDatePicker/init', [me, me.$parent, me.$hiddenField]);
        },

        /**
         * @param {*} val
         */
        setValue: function (val) {
            var me = this,
                plugin = me.$el.data('pickadate');

            val = val || '';

            if (!val.length) {
                plugin.set('clear');
                $.publish('plugin/swagCustomProductsDatePicker/setValue', [me, val]);

                return;
            }

            plugin.set('select', val, { format: me.opts.formatSubmit });

            $.publish('plugin/swagCustomProductsDatePicker/setValue', [me, val]);
        },

        /**
         * formats the dateString to a date array for the "picker" plugin
         */
        formatMinMaxDate: function () {
            var me = this;

            if (me.opts.min) {
                me.opts.min = me.createDateArray(me.opts.min);
            } else {
                // else we set the var "me.opts.min" to undefined because the picker plugin use undefined
                me.opts.min = undefined;
            }

            if (me.opts.max != null) {
                me.opts.max = me.createDateArray(me.opts.max);
            } else {
                // else we set the var "me.opts.max" to undefined because the picker plugin use undefined
                me.opts.max = undefined;
            }
        },

        /**
         * Create from a string like "2015-03-20 00:00:00" a array like:
         * [
         *      2015,
         *      03,
         *      20
         * ]
         *
         * @param {string} value
         * @returns {Array}
         */
        createDateArray: function (value) {
            var explodedDate = value.split('-');

            explodedDate[2] = explodedDate[2].substr(0, 2);

            return explodedDate;
        }
    });

    $(function () {
        StateManager.addPlugin('*[data-custom-products-datepicker="true"]', 'swagCustomProductsDatePicker');
    });
})(jQuery);
