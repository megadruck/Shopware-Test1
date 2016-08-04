
function registerAjaxListener(){
    $(".address--btn-wl").off();
    $(".address--btn-wl").on("click",function(){
        var submitType = $(this).attr('data-type');
        $(".address-manager--selection-form").append('<input type="hidden" name="extraData[addresstype]" value="'+submitType+'" />');
        $(".address-manager--selection-form").submit();
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