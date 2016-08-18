;(function($, window) {
    'use strict';

    /**
     * Small plugin which enables a textarea to grow till the max-height is reached
     */
    $.plugin('swagCustomProductsAutoSizeTextArea', {

        /** @object Default plugin configuration */
        defaults: {

            /** @array Event list */
            eventList: [ 'input', 'keyup' ]
        },

        /**
         * Initializes the plugin and applies the necessary event listeners.
         *
         * @returns void
         */
        init: function() {
            var me = this;

            me._el = me.$el.get(0);
            me._height = me.$el.outerHeight();
            me._diff = window.parseInt(me.$el.css('paddingTop')) + window.parseInt(me.$el.css('paddingBottom')) || 0;

            if(me.containsText(me.$el.val())) {
                me.$el.height(me._el.scrollHeight - me._diff);
            }

            $.publish('plugin/swagCustomProductsAutoSizeTextArea/init', [ me ]);

            me._on(me.$el, me.opts.eventList.join(' '), $.proxy(me.onSizeField, me));
        },

        /**
         * Checks if the textarea has a value
         *
         * @param {String} value
         * @returns {boolean}
         */
        containsText: function(value) {
            return (value.replace(/\s/g, '').length > 0);
        },

        /**
         * Event listener method which will be fired when the user inputs something.
         *
         * The listener checks the height and the current scroll position and sets the size of the textarea.
         *
         * @param e {jQuery.Event}
         */
        onSizeField: function(e) {
            var me = this,
                $target = $(e.target),
                $window = $(window),
                scrollPos = $window.scrollTop();

            $target.height(0).height($target.get(0).scrollHeight - me._diff);

            $.publish('plugin/swagCustomProductsAutoSizeTextArea/onSizeField', [ me ]);

            $window.scrollTop(scrollPos);
        }
    });

    $(function() {
        StateManager.addPlugin('*[data-custom-products-autosize-textarea="true"]', 'swagCustomProductsAutoSizeTextArea');
    });
})(jQuery, window);
