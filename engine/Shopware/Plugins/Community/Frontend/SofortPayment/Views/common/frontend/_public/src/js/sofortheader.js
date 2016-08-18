function sofortPaymentiDealBankChange()
{
    var url = arguments[0];
    var data = "sofort_ideal_bank_select=" + $( "#sofort_ideal_bank_select" ).val();
    $.ajax( {
        type:  "POST",
        async: false,
        url:   url,
        data:  data
    } );
}
