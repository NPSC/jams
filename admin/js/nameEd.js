// Init j-query.
$(document).ready(function () {
    "use strict";

    var memData = $.parseJSON('<?php echo $memDataJSON; ?>');
    var userData = $.parseJSON('<?php echo $usrDataJSON; ?>');
    var listJSON = 'ws_gen.php?cmd=chglog&uid=' + memData.id;
    var postCode = /^(?:[A-Z]{1,2}[0-9][A-Z0-9]? [0-9][ABD-HJLNP-UW-Z]{2}|[ABCEGHJKLMNPRSTVXY][0-9][A-Z] [0-9][A-Z][0-9]|[0-9]{5}(?:\-[0-9]{4})?)$/i;
    var donName;
    var savePressed = false;

    // Unsaved changes on form are caught here.
    $(window).bind('beforeunload', function () {
        // Did user press the save button?
        if (savePressed !== true) {

            var isDirty = false;

            $('#form1').find("input[type='text'],textarea").not(".ignrSave").each(function () {

                if ($(this).val() != $(this).prop("defaultValue")) {
                    isDirty = true;
                }
            });
            $('#form1').find("input[type='radio'],input[type='checkbox']").not(".ignrSave").each(function () {
                if ($(this).prop("checked") != $(this).prop("defaultChecked")) {
                    isDirty = true;
                }
            });
            $('#form1').find("select").not(".ignrSave").each(function () {
                var thsSel = $(this).prop('id');
                if ($(this).data('bfhstates')) {
                    if ($(this).data('state') != $(this).val()) isDirty = true;
                } else if ($(this).data('bfhcountries')) {
                    if ($(this).data('country') != $(this).val()) isDirty = true;
                } else {
                    // gotta look at each option
                    $(this).children('option').each(function () {
                        // find the default option
                        if (this.defaultSelected != this.selected && thsSel != '') {
                            isDirty = true;
                        }
                    });
                }
            });

            if (isDirty === true) {
                return 'You have unsaved changes.';
            }
        }
    });
    $.ajaxSetup({
        beforeSend: function () {
            $('body').css('cursor', "wait");
        },
        complete: function () {
            $('body').css('cursor', "auto");
        },
        cache: false
    });
    // phone - email tabs block
    $('#phEmlTabs').tabs();
    $('#demographicTabs').tabs();
    $('#addrsTabs').tabs();
    var tabs, tbs;
    var listEvtTable;
    tabs = $("#divFuncTabs").tabs({
        collapsible: true,
        beforeActivate: function (event, ui) {
            if (ui.newPanel.length > 0) {
                if (ui.newPanel.selector === '#vchangelog' && !listEvtTable) {
                    listEvtTable = $('#dataTbl').dataTable({
                    "aoColumnDefs": dtCols,
                    "bServerSide": true,
                    "bProcessing": true,
                    "bDeferRender": true,
                    "oLanguage": {"sSearch": "Search Log Text:"},
                    "aaSorting": [[0,'desc']],
                    "iDisplayLength": 25,
                    "aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
                    "Dom": '<"top"ilf>rt<"bottom"ip>',
                    "sAjaxSource": listJSON
                    });
                }
                // Donation dialog setup
                if (ui.newPanel.selector === '#vdonblank' && userData.donFlag) {
                    donName = memData.memName;
                    if (memData.memDesig == 'i' && memData.coId > 0) {
                        donName = donName + '  (' + memData.coName + ')';
                    }
                    //$(ui.newTab.parentnode).removeClass('ui-state-focus').removeClass('ui-state-hover');
                    // create the markup for the new donations list.
                    getDonationMarkup(memData.id);
                    // Write the selected campaign amount range info into the donor tab.
                    $('#dselCamp').change();
                    $('#vdon').dialog("option", "title", 'Record Donations for ' + donName);
                    $('#vdon').dialog('open');
                    event.preventDefault();
                }
                if (ui.newPanel.selector === '#vwuser') {
                    $('#vwebUser').dialog("option", "title", 'Web Volunter Info for ' + memData.memName);
                    $('#vwebUser').dialog('open');
                    //$(ui.newTab.parentnode).removeClass('ui-state-focus').removeClass('ui-state-hover');
                    event.preventDefault();
                }
            }
        }
    });
    tbs = tabs.find( ".ui-tabs-nav" ).children('li').length;
    // enable tabs for a "new" member
    if (memData.id == '0') {
        if (userData.donFlag) {
            tabs.tabs("option", "disabled", [ tbs - 4, tbs - 3, tbs - 2, tbs - 1]);
        } else {
            tabs.tabs("option", "disabled", [ tbs - 3, tbs - 2, tbs - 1]);
        }
        $('#phEmlTabs').tabs("option", "active", 1);
        $('#phEmlTabs').tabs("option", "disabled", [0]);
    } else {
        // Existing member
        $('#addrsTabs').tabs("option", "active", (memData.addrPref - 1));
        // web user? if not, disable the tab.
        if (memData.webUserName == '') {
            $("#divFuncTabs").tabs("option", "disabled", [tbs - 2]);
        }
    }
    // Relationship tab control
    $('#linkTabs').tabs({ collapsible: true});
    // relationship dialog
    $("#submit").dialog({
        autoOpen: false,
        resizable: false,
        width: 300,
        modal: true,
        buttons: {
            "Exit": function () {
                $(this).dialog("close");
            }
        }
    });
    // Relationship events
    $('div.hhk-relations').each(function () {
        var schLinkCode = $(this).attr('name');
        $(this).on('click', 'td.hhk-deletelink', function () {
            if (memData.id > 0) {
                if (confirm($(this).attr('title') + '?')) {
                    manageRelation(memData.id, $(this).attr('name'), schLinkCode, 'delRel');
                }
            }
        });
        $(this).on('click', 'td.hhk-careoflink', function () {
            if (memData.id > 0) {
                if (confirm($(this).attr('title') + '?')) {
                    var flag = $(this).find('span').attr('name');
                    manageRelation(memData.id, $(this).attr('name'), schLinkCode, flag);
                }
            }
        });
        $(this).on('click', 'td.hhk-newlink', function () {
            if (memData.id > 0) {
                var title = $(this).attr('title');
                $('#hdnRelCode').val(schLinkCode);
                $('input#txtRelSch').val('');
                $('#submit').dialog("option", "title", title);
                $('#submit').dialog('open');
            }
        });
    });
    $('#divListDonation').on('click', 'input.hhk-undonate', function () {
        var parts = $(this).attr('id').split('_');
        if (parts.length > 1) {
            var did = parseInt(parts[1]);
            if (!isNaN(did)) {
                if (confirm('Delete this Donation?')) {
                    $.post("donate.php",
                        {
                            did: did,
                            sq: $("#squirm").val(),
                            cmd: 'delete'
                        },
                        function (data) {
                            donateDeleteMarkup(data, memData.id);
                        }
                    );
                } else {
                    $(this).prop('checked', false);
                }
            }
        }
    });
    $('#btnSubmit, #btnReset, #btnCopy, #chgPW').button();
    $("#zipSearch").dialog({
        autoOpen: false,
        resizable: true,
        width: 450,
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
            lastXhr = $.getJSON("ws_gen.php", {zip: request.term, cmd: 'schzip'})
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
    $('div#addrsTabs').on('click', '.hhk-zipsearch', function() {
        $('#zipSearch').data('hhkprefix', $(this).data('hhkprefix'));
        $('#zipSearch').data('hhkindex', $(this).data('hhkindex'));
        $('#zipSearch').dialog('open');
        $('input#txtZipSch').val($('#' + $(this).data('hhkprefix') + 'adrzip' + $(this).data('hhkindex')).val());
    });
    $('input.hhk-emailInput').change(function () {
        // Inspect email text box input for correctness.
        var rexEmail = /^[A-Z0-9._%+\-]+@(?:[A-Z0-9]+\.)+[A-Z]{2,4}$/i;
        $('#emailWarning').text("");
        // each email input control
        $('input.hhk-emailInput').each(function () {
            if ($.trim($(this).val()) != '' && rexEmail.test($(this).val()) === false) {
                $(this).next('span').text("*");
                $('#emailWarning').text("Incorrect Email Address");

            } else {
                $(this).next('span').text("");
            }
        });
    });
    $('input.hhk-phoneInput').change(function () {
        // inspect each phone number text box for correctness
        var testreg = /^([\(]{1}[0-9]{3}[\)]{1}[\.| |\-]{0,1}|^[0-9]{3}[\.|\-| ]?)?[0-9]{3}(\.|\-| )?[0-9]{4}$/;
        var regexp = /^(?:(?:[\+]?([\d]{1,3}(?:[ ]+|[\-.])))?[(]?([2-9][\d]{2})[\-\/)]?(?:[ ]+)?)?([2-9][0-9]{2})[\-.\/)]?(?:[ ]+)?([\d]{4})(?:(?:[ ]+|[xX]|(i:ext[\.]?)){1,2}([\d]{1,5}))?$/;
        var numarry;
        $('#phoneWarning').text("");
        $('input.hhk-phoneInput').each(function () {
            if ($.trim($(this).val()) != '' && testreg.test($(this).val()) === false) {
                $(this).nextAll('span').show();
                $('#phoneWarning').text("Incorrect Phone Number");

            } else {
                $(this).nextAll('span').hide();
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
    });
    $('input.hhk-phoneInput').change();
    $('input.hhk-emailInput').change();
    $('#txtNewPw1').change(function () {
        if ($('#txtNewPw2').val() != "" && $('#txtNewPw2').val() != this.value) {
            updateTips($('#pwChangeErrMsg'), "New passwords do not match");
        } else {
            $(this).removeClass("ui-state-highlight");
        }
    });
    $('#txtNewPw2').change(function () {
        if ($('#txtNewPw1').val() != "" && $('#txtNewPw1').val() != this.value) {
            updateTips($('#pwChangeErrMsg'), "New passwords do not match");
        } else {
            $(this).removeClass("ui-stat-highlight");
        }
    });
    $('#chgPW').click(function () {
        $('#dchgPw').dialog("option", "title", "Change Password for " + memData.memName);
        $('#txtOldPw').val('');
        $('#txtNewPw1').val('');
        $('#txtNewPw2').val('');

        $('#dchgPw').dialog('open');
        $('#pwChangeErrMsg').val('');
        $('#txtOldPw').focus();
    });
    $('#dselCamp').change(function () {
        getCampaign($(this).val());
    });
    $('#dchgPw').dialog({
        autoOpen: false,
        width: 550,
        resizable: true,
        modal: true,
        buttons: {
            "Save": function () {
                var tips = $('#pwChangeErrMsg');
                $('#dchgPw').children('input').removeClass("ui-state-error");

                tips.text('');

                var oldpw, pw1, oldpwMD5, newpwMD5;
                oldpw = $('#txtOldPw').val();
                if (!oldpw || oldpw == "") {
                    $('#txtOldPw').addClass("ui-state-error").focus();
                    updateTips(tips, 'Enter the old password');
                    return;
                }

                pw1 = $('#txtNewPw1').val();
                if (!checkLength($('#txtNewPw1'), 'New password', 7, 25, tips)) {
                    return;
                }

                if ($('#txtNewPw1').val() != $('#txtNewPw2').val()) {
                    updateTips(tips, "New passwords do not match");
                    return;
                }

                // immediatly clear the passwords.
                $('#txtOldPw').val('');
                $('#txtNewPw1').val('');
                $('#txtNewPw2').val('');

                $(this).dialog("close");
                // make MD5 hash of password and concatenate challenge value
                // next calculate MD5 hash of combined values
                oldpwMD5 = hex_md5(hex_md5(oldpw) + '<?php echo $challengeVar; ?>');
                newpwMD5 = hex_md5(pw1);

                $.ajax({
                    type: "POST",
                    url: "ws_gen.php",
                    data: ({
                        cmd: 'adchgpw',
                        uid: memData.id,
                        adpw: oldpwMD5,
                        newer: newpwMD5
                    }),
                    success: handleChangePW,
                    error: handleChangePW,
                    datatype: "json"
                });

            },
            "Cancel": function () {
                $(this).dialog("close");
            }
        },
        close: function () {
            $('body').css('cursor', "auto");
        }
    });
    // Donation panel dialog box.
    $("#vdon").dialog({
        autoOpen: false,
        height: 480,
        width: 900,
        resizable: true,
        modal: true,
        buttons: {
            "Record": function () {
                var amt = parseFloat($('#damount').val());
                if (isNaN(amt) || amt <= 0) {
                    return;
                }
                // collect the donation data
                var parms = {};
                $('.hhk-ajx-dondata').each(function() {
                    parms[$(this).attr("id")] = $(this).val();
                });
                $.post(
                    "donate.php",
                    {
                        cmd: "insert",
                        id: memData.id,
                        sq: $('#squirm').val(),
                        qd: parms
                    },
                    function (data) {
                        donateResponse(data, memData.id);
                    });
            },
            "Print": function () {
                $("#divListDonation").printArea();
            },
            "Exit": function () {
                $(this).dialog("close");
            }
        },
        close: function () {
            $('#damount').val('');
            $('#donateResponseContainer').attr("style", "display:none;");
        }
    });
    $('#vwebUser').dialog({
        autoOpen: false,
        height: 420,
        width: 732,
        resizable: true,
        modal: true,
        buttons: {
            "Save": function (event) {

                var parms = {};

                $('.grpSec').each(function (index) {
                    if ($(this).prop("checked")) {
                        parms[$(this).attr("id")] = "checked";
                    } else {
                        parms[$(this).attr("id")] = "unchecked";
                    }
                });
                $('div.ui-dialog-buttonset').css("display", "none");
                $.ajax({
                    type: "GET",
                    url: "ws_gen.php",
                    data: ({
                        role: $("#selwRole").val(),
                        cmd: "save",
                        uid: memData.id,
                        status: $('#selwStatus').val(),
                        fbst: $('#selFbStatus').val(),
                        admin: userData.userName,
                        vaddr: $('#selwVerify').val(),
                        parms: parms
                    }),
                    success: handleResponse,
                    error: handleError,
                    datatype: "json"
                });
            },
            "Exit": function () {
                $(this).dialog("close");
            }
        },
        close: function () {
            $('body').css('cursor', "auto");
        }
    });
    $('.ckdate').datepicker({
        yearRange: '-03:+05',
        changeMonth: true,
        changeYear: true,
        autoSize: true,
        dateFormat: 'M d, yy'
    });
    $('.ckzip').blur(function () {
        var txt, zipError;
        txt = $(this).val();
        zipError = $('#w' + $(this).attr("id"));
        if (txt != "" && !postCode.test(txt)) {
            zipError.text("Bad Postal Code");
        } else {
            zipError.text("");
        }
    });
    $('#goWebSite').click(function () {
        var site = $('#txtWebSite').val();
        if (site != "") {
            var parts = site.split(':');

            if (parts.length < 2) {
                site = 'http://' + site;
            }
            window.open(site);
            return false;
        }
    });
    // Main form submit button.  Disable page during POST
    $('#btnSubmit').click(function () {
        if ($(this).val() == 'Saving>>>>') {
            return false;
        }
        savePressed = true;
        $(this).val('Saving>>>>');

    });
    // Notes search button click handler
    $('#btnNoteSch').click(function () {
        var stxt = $("#txtSearchNotes").val();
        $('#schNotes textarea').each(function () {

            if ($(this).val().toUpperCase().indexOf(stxt.toUpperCase()) > -1) {
                $(this).css("background-color", "yellow");
            } else {
                $(this).css("background-color", "white");
            }
        });
    });
    // Don't let user choose a blank address as preferred.'
    $('.addrPrefs').click(function () {
        var indx = this.value, adr1, cty, foundOne;

        adr1 = document.getElementById("adraddress1" + indx);
        cty = document.getElementById("adrcity" + indx);
        if ((adr1 != null && adr1.value == "") || (cty != null && cty.value == "")) {
            alert("This address is blank.  It cannot be the 'preferred' address.");
            this.checked = false;
            foundOne = false;

            // see if the old preferred phone has a number - then we check it and done.
            if (memData.addrPref != "" && $("#adraddress1" + memData.addrPref).val() != "") {
                $('#rbPrefMail' + memData.addrPref).prop('checked', true);
                foundOne = true;
            }

            if (!foundOne) {
                $('.addrPrefs').each(function () {
                    if ($("#adraddress1" + this.value).val() != "") {
                        $(this).prop('checked', true);
                        memData.addrPref = this.value;

                    }
                });
            }
        }
    });
    // enforce the Preferred phone number actually has a number
    $('input.prefPhone').change(function () {
        var foundOne, ctl = $("#txtPhone" + this.value);
        if (ctl !== null && ctl.val() == "") {
            alert("This Phone Number is blank.  It cannot be the 'preferred' phone number.");
            this.checked = false;
            foundOne = false;

            // see if the old preferred phone has a number - then we check it and done.
            if (memData.phonePref != "" && $("#txtPhone" + memData.phonePref).val() != "") {
                $('#ph' + memData.phonePref).prop('checked', true);
                foundOne = true;
            }

            // If we did not check a rb, then find one that has a textbox with info in it
            if (!foundOne) {
                $('.prefPhone').each(function () {
                    if ($("#txtPhone" + this.value).val() != "") {
                        $(this).prop('checked', true);
                        memData.phonePref = this.value;
                        return;
                    }
                });
            }
        }
    });
    // Enforce preferred Email has a value defined.
    $('input.prefEmail').change(function () {
        var ctl = $("#txtEmail" + this.value), foundOne;
        if (ctl != null && ctl.val() == "") {
            alert("This Email Address is blank.  It cannot be the 'preferred' Email address.");
            foundOne = false;
            this.checked = false;

            if (memData.emailPref != "" && $("#txtEmail" + memData.emailPref).val() != "") {
                $('#em' + memData.emailPref).prop('checked', true);
                foundOne = true;
            }

            // If we did not check a rb, then find one that has a textbox with info in it
            if (!foundOne) {
                $('.prefEmail').each(function () {
                    if ($("#txtEmail" + this.value).val() != "") {
                        $(this).prop('checked', true);
                        memData.emailPref = this.value;
                        return;
                    }
                });
            }
        }
    });
    var origSchAmt;
    $('#cbStuAddYear').change(function () {
        if ($(this).prop('checked')) {
            var spnAmt = parseFloat($(this).data('amt'));
            var addnl = parseFloat($(this).data('addnl'));
            if (isNaN(spnAmt) || isNaN(addnl)) {
                return;
            }
            var tot = spnAmt + addnl;
            origSchAmt = spnAmt;
            $('#' + $(this).data('udid')).text('$'+ tot);
        } else {
            $('#' + $(this).data('udid')).text('$'+ origSchAmt);
        }
    });
    var lastXhr;
    $('#txtsearch').autocomplete({
        source: function (request, response) {
            if (isNumber(parseInt(request.term, 10))) {
                response();
            } else {
                var schType = 'm';
                if ($('#rbmemEmail').prop("checked")) {
                    schType = 'e';
                }
                // get more data
                var inpt = {
                    cmd: "srrel",
                    letters: request.term,
                    basis: schType,
                    id: 0
                };
                lastXhr = $.getJSON("liveNameSearch.php", inpt,
                    function(data, status, xhr) {
                        if (xhr === lastXhr) {
                            if (data.error) {
                                data.value = data.error;
                            }
                            response(data);
                        } else {
                            response();
                        }
                    });
            }
        },
        minLength: 3,
        select: function( event, ui ) {
            if (!ui.item) {
                return;
            }

            if (ui.item.id == 'i') {
                // New Individual
                window.location = "NameEdit.php?cmd=newind";
            } else if (ui.item.id == 'stu') {
                window.location = "NameEdit.php?cmd=newstu";
            } else if (ui.item.id == 'o') {
                // new organization
                window.location = "NameEdit.php?cmd=neworg";
            }

            var cid = parseInt(ui.item.id, 10);
            if (isNumber(cid)) {
                window.location = "NameEdit.php?id=" + cid;
            }
        }
    });
    $('#txtRelSch').autocomplete({
        source: function (request, response) {
            if (isNumber(parseInt(request.term, 10))) {
                response();
            } else {
                // get more data
                var inpt = {
                    cmd: "srrel",
                    letters: request.term,
                    basis: $('#hdnRelCode').val(),
                    id: memData.id,
                    nonly: '1'
                };
                lastXhr = $.getJSON("liveNameSearch.php", inpt,
                    function(data, status, xhr) {
                        if (xhr === lastXhr) {
                            if (data.error) {
                                data.value = data.error;
                            }
                            response(data);
                        } else {
                            response();
                        }
                    });
            }
        },
        minLength: 3,
        select: function( event, ui ) {
            if (!ui.item) {
                return;
            }
            $('input#txtRelSch').val('');
            $('#submit').dialog('close');

            var cid = parseInt(ui.item.id, 10);
            if (isNumber(cid)) {
                $.post('ws_gen.php', {'rId':cid, 'id':memData.id, 'rc':$('#hdnRelCode').val(), 'cmd':'newRel'}, relationReturn);
            }
        }
    });
    // Member search letter input box
    $('#txtsearch').keypress(function (event) {
        var mm = $(this).val();
        if (event.keyCode == '13') {
            if (mm == '' || !isNumber(parseInt(mm, 10))) {
                alert("Don't press the return key unless you enter an Id.");
                event.preventDefault();
            } else {
                window.location = "NameEdit.php?id=" + mm;
            }
        }
    });
    $('input.hhk-check-button').click(function () {
        if ($(this).prop('id') == 'exAll') {
            $('input.hhk-ex').prop('checked', true);
        } else {
            $('input.hhk-ex').prop('checked', false);
        }
    });
    changeMemberStatus($("#selStatus"), memData, savePressed);
    // Flag member status if not active
    $("#selStatus").change(function () {
        changeMemberStatus($(this), memData, savePressed);
    });
    $('#divFuncTabs').css('display', 'block');
    $('.hhk-showonload').css('display', 'block');
    //show details/hide details
    $(".toggle-docs-detail").toggle(function(){

        var theUl = $(this).text("Show details").parent().next("ul");
        theUl.find("li > div:first-child").removeClass("header-open")
        .nextAll().hide();

        //e.preventDefault();

        // adjust the size of the vol tab control
        var sumpx = 0;

        theUl.children().each( function () {
            sumpx = sumpx + $(this).height();
        });

        theUl.parent().height(sumpx + 40);

    },function(e){
        var theUl = $(this).text("Hide details").parent().next("ul");
        var details = theUl.find("li > div:first-child").addClass("header-open");
        details.next().show();
        //e.preventDefault();

        // adjust the size of the vol tab control
        var sumpx = 0;

        theUl.children().each( function () {
            sumpx = sumpx + $(this).height();
        });

        theUl.parent().height(sumpx + 40);

    });

    //Initially hide all options/methods/events
    //$('div.option-description').hide();

    //Make list items collapsible
    $('div.option-header h3').on('click', function(e) {

        var details = $(this).parent().toggleClass('header-open');
        details.next().toggle();
        e.preventDefault();

        // adjust the size of the vol tab control
        // Open:  140, closed: 35
        var sumpx = 0;
        var theUl = details.closest('ul');
        theUl.children().each( function () {
            sumpx = sumpx + $(this).height();
        });

        theUl.parent().height(sumpx + 40);
    });

});


