var express_entryurl = "https://expressentry.melissadata.net/";
function compare(a, b) {
    if (a.label < b.label)
        return -1;
    if (a.label > b.label)
        return 1;
    return 0;
}
function custom_sort(arr) {
    arr.sort(compare);
    // delete all duplicates from the array
    for (var i = 0; i < arr.length - 1; i++) {
        if (arr[i].label == arr[i + 1].label) {
            delete arr[i];
        }
    }
    return arr.filter(function (el) {
        return (typeof el !== "undefined");
    });
}
jQuery(document).ready(function () {
    streetfocused = false;
    $('#zipcode').autocomplete({
        showHeader: true,
        minLength: 3,
        delay: 0,
        source: function (request, response) {
            $.getJSON(express_entryurl + "jsonp/GlobalExpressPostalCode?callback=?", {
                format: "jsonp",
                id: melissa_id,
                postalcode: request.term,
                country: $('#country option:selected').attr('isocode'),
                administrativearea: $('#country_' + $('#country option:selected').val() + '_states option:selected').attr('isocode'),
                maxrecords: "10"
            }, function (data) {


                //alert(JSON.stringify(data.Results));
                //console.debug(data);
                var arr = $.map(data.Results, function (item) {

                    if (item.Address.Locality != '') {
                        return {
                            label: item.Address.PostalCodePrimary + ' ' + item.Address.Locality,
                            value: item.Address.PostalCodePrimary
                        };
                    }
                });
                response(custom_sort(arr));
            });
        },
        select: function (evt, ui) {
            //fill in the city and state fields
            $('#city').val(ui.item.label.replace(ui.item.value + ' ', ''));
            //this.form.administrativearea.value = citystatezip[1];

            //move focus to the next entry field
            $("#street").focus();
        }
    });

    $('#street').autocomplete({
        showHeader: true,
        minLength: 4,
        delay: 0,
        source: function (request, response) {
            $.getJSON(express_entryurl + "jsonp/GlobalExpressThoroughfare?callback=?", {
                format: "jsonp",
                id: melissa_id,
                thoroughfare: request.term,
                locality: $('#city').val(),
                postalcode: $('#zipcode').val(),
                country: $('#country option:selected').attr('isocode'),
                maxrecords: "5  "
            }, function (data) {
                //alert(JSON.stringify(data.Results));
                var arr = $.map(data.Results, function (item) {
                    var street = item.Address.Thoroughfare;
                    var streetnumber = item.Address.Premise;
                    return {
                        label: street,
                        value: street
                    };
                });
                response(custom_sort(arr));
            });
        },
    });

    $("#street").focusin(function () {
        if ($('#street').val() == '' && streetfocused == 0) {
            streetfocused = 1;
            $.getJSON(express_entryurl + "jsonp/GlobalExpressThoroughfare?callback=?", {
                format: "jsonp",
                id: melissa_id,
                locality: $('#city').val(),
                postalcode: $('#zipcode').val(),
                country: $('#country option:selected').attr('isocode'),
                maxrecords: "200"
            }, function (data) {
                //alert(JSON.stringify(data.Results));
                var arr = $.map(data.Results, function (item) {
                    return{
                        label: item.Address.Thoroughfare,
                        value: item.Address.Thoroughfare
                    };
                });
                var arr2 = $.map(data.Results, function (item) {
                    return{
                        label: item.Address.Premise,
                        value: item.Address.Premise
                    };
                });

                arr = custom_sort(arr);
                if (arr.length == 1) {
                    $('#street').val(arr[0].label);
                }
                arr2 = custom_sort(arr2);
                if (arr2.length == 1) {
                    $('#streetnumber').val(arr2[0].label);
                }
            });
        }
    });


    $('#city').autocomplete({
        showHeader: true,
        minLength: 4,
        delay: 0,
        source: function (request, response) {
            $.getJSON(express_entryurl + "jsonp/GlobalExpressAddress?callback=?", {
                format: "jsonp",
                id: melissa_id,
                address1: request.term,
                postalcode: $('#zipcode').val(),
                country: $('#country option:selected').attr('isocode'),
                maxrecords: "10"
            }, function (data) {
                //alert(JSON.stringify(data.Results));
                var arr = $.map(data.Results, function (item) {
                    return{
                        label: item.Address.Locality,
                        value: item.Address.Locality};
                });
                response(custom_sort(arr));
            });
        },
        select: function (evt, ui) {
            //put selection in result box
            $('#city').val(ui.item.label);
        }
    });

    $('#zipcode2').autocomplete({
        showHeader: true,
        minLength: 3,
        delay: 0,
        source: function (request, response) {
            $.getJSON(express_entryurl + "jsonp/GlobalExpressPostalCode?callback=?", {
                format: "jsonp",
                id: melissa_id,
                postalcode: request.term,
                country: $('#country option:selected').attr('isocode'), maxrecords: "10"
            }, function (data) {
                //alert(JSON.stringify(data.Results));
                var arr = $.map(data.Results, function (item) {
                    if (item.Address.Locality != '') {
                        return {
                            label: item.Address.PostalCode + ' ' + item.Address.Locality,
                            value: item.Address.PostalCode
                        };
                    }
                });
                response(custom_sort(arr));
            });
        },
        select: function (evt, ui) {
            //fill in the city and state fields
            $('#city2').val(ui.item.label.replace(ui.item.value + ' ', ''));
            //this.form.administrativearea.value = citystatezip[1];

            //move focus to the next entry field
            $("#street2").focus();
        }
    });

    $('#street2').autocomplete({
        showHeader: true,
        minLength: 4,
        delay: 0,
        source: function (request, response) {
            $.getJSON(express_entryurl + "jsonp/GlobalExpressThoroughfare?callback=?", {
                format: "jsonp",
                id: melissa_id,
                thoroughfare: request.term,
                locality: $('#city2').val(),
                postalcode: $('#zipcode2').val(),
                country: $('#country2 option:selected').attr('isocode'),
                maxrecords: "5"
            }, function (data) {
                //alert(JSON.stringify(data.Results));
                var arr = $.map(data.Results, function (item) {
                    var street = item.Address.DeliveryAddress1;
                    var streetnumber = street.split(/[ ,]+/);
                    streetnumber = streetnumber[streetnumber.length - 1];
                    street = street.replace(' ' + streetnumber, '');
                    return{
                        label: street,
                        value: street
                    };
                });
                response(custom_sort(arr));
            });
        },
    });

    $('#city2').autocomplete({
        showHeader: true,
        minLength: 4,
        delay: 0,
        source: function (request, response) {
            $.getJSON(express_entryurl + "jsonp/GlobalExpressAddress?callback=?",
                {
                    format: "jsonp",
                    id: melissa_id,
                    address1: request.term,
                    postalcode: $('#zipcode2').val(),
                    country: $('#country2 option:selected').attr('isocode'),
                    maxrecords: "10"
                }, function (data) {
                    //alert(JSON.stringify(data.Results));
                    var arr = $.map(data.Results, function (item) {
                        return{
                            label: item.Address.Locality,
                            value: item.Address.Locality};
                    });
                    response(custom_sort(arr));
                });
        },
        select: function (evt, ui) {
            //put selection in result box
            $('#city2').val(ui.item.label);
        }
    });
});