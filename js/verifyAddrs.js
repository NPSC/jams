
function verifyAddrs(container) {
    "use strict";
    var lastXhr;
    $("#zipSearch").dialog({
        autoOpen: false,
        resizable: true,
        width: 350,
        modal: true,
        title: 'Zip Code Search',
        buttons: {
            "Exit": function() {
                $(this).dialog("close");
            }
        }
    });
    $('#txtZipSch').autocomplete({
        source: function(request, response) {
            lastXhr = $.getJSON("ws_admin.php", {zip: request.term, cmd: 'schzip'})
                    .done(function(data, status, xhr) {

                        if (xhr === lastXhr) {
                            if (data.error) {
                                data.value = data.error;
                            }
                            response(data);
                        } else {
                            response();
                        }
                    })
                    .fail(function(jqxhr, textStatus, error) {
                        var err = textStatus + ', ' + error;
                        alert("Postal code request failed: " + err);
                    });
        },
        minLength: 3,
        select: function(event, ui) {
            if (!ui.item) {
                return;
            }
            $("#zipSearch").dialog('close');
            var idx = $("#zipSearch").data('hhkindex');
            var prf = $("#zipSearch").data('hhkprefix');

            $('#' + prf + 'adrcity' + idx).val(ui.item.City);
            $('#' + prf + 'adrcountry' + idx).val('US');
            $('#' + prf + 'adrcountry' + idx).change();
            $('#' + prf + 'adrstate' + idx).val(ui.item.State);
            $('#' + prf + 'adrzip' + idx).val(ui.item.id);

        }
    });
    $(container).on('click', '.hhk-zipsearch', function() {
        $('#zipSearch').data('hhkprefix', $(this).data('hhkprefix'));
        $('#zipSearch').data('hhkindex', $(this).data('hhkindex'));
        $('input#txtZipSch').val('');
        $('#zipSearch').dialog('open');
    });
    $(container).on('change', 'input.hhk-emailInput', function() {
        var rexEmail = /^[A-Z0-9._%+\-]+@(?:[A-Z0-9]+\.)+[A-Z]{2,4}$/i;
        if ($.trim($(this).val()) !== '' && rexEmail.test($(this).val()) === false) {
            $(this).addClass('ui-state-error');
        } else {
            $(this).removeClass('ui-state-error');
        }
    });
    $(container).on('change', 'input.hhk-phoneInput', function() {
        // inspect each phone number text box for correctness
        var testreg = /^([\(]{1}[0-9]{3}[\)]{1}[\.| |\-]{0,1}|^[0-9]{3}[\.|\-| ]?)?[0-9]{3}(\.|\-| )?[0-9]{4}$/;
        var regexp = /^(?:(?:[\+]?([\d]{1,3}(?:[ ]+|[\-.])))?[(]?([2-9][\d]{2})[\-\/)]?(?:[ ]+)?)?([2-9][0-9]{2})[\-.\/)]?(?:[ ]+)?([\d]{4})(?:(?:[ ]+|[xX]|(i:ext[\.]?)){1,2}([\d]{1,5}))?$/;
        var numarry;
        if ($.trim($(this).val()) != '' && testreg.test($(this).val()) === false) {
            // error
            $(this).addClass('ui-state-error');

        } else {
            $(this).removeClass('ui-state-error');
            regexp.lastIndex = 0;
            // 0 = matached, 1 = 1st capturing group, 2 = 2nd, etc.
            numarry = regexp.exec($(this).val());
            if (numarry != null && numarry.length > 3) {
                var ph = "";
                // Country code?
                if (numarry[1] != null && numarry[1] != "") {
                    ph = '+' + numarry[1];
                }
                // The main part
                $(this).val(ph + '(' + numarry[2] + ') ' + numarry[3] + '-' + numarry[4]);
                // Extension?
                if (numarry[6] != null && numarry[6] != "") {
                    $(this).next('input').val(numarry[6]);
                }
            }
        }
    });
    $(container).on('change', 'input.ckzip', function() {
        var postCode = /^(?:[A-Z]{1,2}[0-9][A-Z0-9]? [0-9][ABD-HJLNP-UW-Z]{2}|[ABCEGHJKLMNPRSTVXY][0-9][A-Z] [0-9][A-Z][0-9]|[0-9]{5}(?:\-[0-9]{4})?)$/i;
        if ($(this).val() !== "" && !postCode.test($(this).val())) {
            $(this).addClass('ui-state-error');
        } else {
            $(this).removeClass('ui-state-error');
        }
    });
}