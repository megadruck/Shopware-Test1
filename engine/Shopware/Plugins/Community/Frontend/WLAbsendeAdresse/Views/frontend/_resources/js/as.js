var createCookie = function(name, value) {
    var expires;
    expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}

function clearType(){
    createCookie("type","");
}

function registerAjaxListener(){

    if(getCookie("type") == "lieferadresse"){
        $(".absender-address-btn").remove();
        $(".noSenderBox").remove();
    }
    if(getCookie("type") == "absenderadresse"){
        $(".liefer-address-btn").remove();
    }

    $(".address--btn-wl").off();
    $(".address--btn-wl").on("click",function(){
        var submitType = $(this).attr('data-type');
        var dataId = $(this).attr('data-id');
        $(".address-manager--selection-form-"+dataId).append('<input type="hidden" name="extraData[addresstype]" value="'+submitType+'" />');
        $.ajax({
            type: "POST",
            url: $(".address-manager--selection-form-"+dataId).attr('action'),
            data: $(".address-manager--selection-form-"+dataId).serialize(),
            success: function(){
                location.reload();
                window.location.reload();
            },
            error: function(){
                location.reload();
                window.location.reload();
            },
            dataType:"json"
        });

    });

    // client side filtering
    $("#searchField").on("change",function(){
        var text = $(this).val().toLowerCase();

        console.log(text);
        $(".address--box").each(function(){

            $(this).show();
            var tags = $(this).attr('data-tags').toLowerCase();
            if(tags.indexOf(text) !== -1){
                //
            } else{
                $(this).hide();
            }

        });
    });

    $(".address--box").on("click",function(){
        var id = $(this).attr('id');
        $(".address-selection").hide();
        $(".address-selection-"+id).show();
    });

    $("#noSender").on("click",function(){
        $.ajax({
            type: "POST",
            url: clearAddrUrl,
            data: {},
            success: function(){
                createCookie(ty)
                location.reload();
                window.location.reload();
            },
            error: function(){
                location.reload();
                window.location.reload();
            },
            dataType:"json"
        });
        return false;
    });

}



function registerAddressTypeListener(){
    $(".open-shippingaddress").click(function(){
        console.log("shipping clicked");
        createCookie("type","lieferadresse");
    });
    $(".open-senderaddress").click(function(){
        console.log("sender clicked");
        createCookie("type","absenderadresse");
    });
}




window.addEventListener('load', function(){
    // bind events on load


});