$(function () {
    $('.js--account').click(function () {
        $('.top-bar--login').slideToggle( "fast", function() {
        });
    });
   $( "body" ).click(function( event ) {
        if ( !$(event.target).hasClass("js--account") &&  event.target.parentNode.tagName != 'FORM' && 
                !$(event.target).hasClass("navigation--link")  &&  !$(event.target).hasClass("register--login-field") && 
                $('.top-bar--login').is(":visible")){ $('.top-bar--login').hide(); }
        });
});
