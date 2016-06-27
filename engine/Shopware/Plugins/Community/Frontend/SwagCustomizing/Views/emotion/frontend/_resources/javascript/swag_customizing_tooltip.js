$(document).ready(function () {

    $('#confirm .customizing-basket_details').parent().parent().css('overflow', 'hidden');

    $('.tooltip').on('mouseover mouseout', function (e) {
        var message = $(this).attr('data-tooltip');
        var position = $(this).position();
        var width = $(this).outerWidth();
        var tooltip = $('<div>', {
            'class': 'tooltip-box',
            'html': message,
            'css': {
                textAlign: 'center',
                fontWeight: 'bold',
                background: '#fffAF0',
                position: 'absolute',
                zIndex: '20',
                width: '240px',
                padding: '14px 20px',
                border: '1px solid #DCA',
                color: '#111',
                lineHeight: '16px',
                borderRadius: '4px',
                "-moz-border-radius": '4px',
                "-webkit-border-radius": "4px",
                "-ms-border-radius": "4px",
                "-o-border-radius": "4px",
                "-moz-box-shadow": "5px 5px 8px #CCC",
                "-webkit-box-shadow": "5px 5px 8px #CCC",
                "-ms-box-shadow": "5px 5px 8px #CCC",
                "-o-box-shadow": "5px 5px 8px #CCC",
                "box-shadow": "5px 5px 8px #CCC"
            }
        });

        if (e.type == 'mouseover') {
            $(this).append(tooltip);
            var height = tooltip.outerHeight();
            tooltip.css({
                top: position.top - (height / 2),
                left: position.left + width + 10
            });

            $('.table .table_row.small_quantities').css('overflow', 'visible');
        } else {
            $('.table .table_row.small_quantities').removeAttr('style');
            $('#content #confirm .table .small_quantities').css('cssText', 'min-height: 70px !important');
            $('.tooltip-box').remove();
        }
    });
});
