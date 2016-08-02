$(function () {
    $('.js--account').click(function () {
        $('.top-bar--login').slideToggle( "fast", function() {
        });
    });
    $( "body" ).click(function( event ) {
        if ( !$(event.target).hasClass("js--account") && !$(event.target).hasClass("navigation--link")  && $('.top-bar--login').is(":visible")){
            $('.top-bar--login').hide();
        }
        });
});
