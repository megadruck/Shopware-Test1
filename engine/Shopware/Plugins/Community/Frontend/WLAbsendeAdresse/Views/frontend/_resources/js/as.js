
function registerAjaxListener(){
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



window.addEventListener('load', function(){
    // bind events on load

});