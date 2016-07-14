$(document).ajaxStop(function (e) {
    //If cookies are not allowed
    //show cookie bar
    var allowCookie = getCookie('allowCookie');
    if (allowCookie != 1) {
        $(document).off('ajaxStop');
        $.ajax({
            url: cookiePermissionUrl,
            dataType: 'json',
            'success': function(result) {
                if (result.isAffectedUser) {
                    $('.cookie-bar').show();
                    //If cookie mode is "remove cookies"
                    //page should be overlayed
                    if (isRemoveCookies && isForwarded() === false) {
                        $('body').append('<div class="cookie-overlay"></div>');
                    }
                }
            }
        });
    }

    /**
     * Returns cookie value
     * by name
     *
     * @param {string} name
     * @returns {string}
     */
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    /**
     * Checks if the user
     * is forwarded
     *
     * @returns {Boolean}
     */
    function isForwarded()
    {
        if (cookieForwardTo == window.location.href ||
            cookieForwardTo == window.location.pathname) {
            return true;
        }

        return false;
    }
});

$(document).ready(function() {
    //enable cookies button
    $('.cp-enable').on('click', function() {
        setCookie('allowCookie', 1);
        $( ".cookie-bar" ).slideToggle( "slow" );
        $( ".cookie-overlay" ).remove();
    });

    //disable cookies button
    $('.cp-disable').on('click', function() {
        var loc = window.location;
        if (cookieForwardTo.search('http://') > -1 || cookieForwardTo.search('https://') > -1) {
            window.location = cookieForwardTo;
        } else {
            window.location = loc.protocol+"//"+loc.hostname+"/" + cookieForwardTo;
        }

    });

    /**
     * Creates cookie
     *
     * @param {string} key
     * @param {type} value
     */
    function setCookie(key, value) {
        var expires = new Date();
        expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
    }
});

