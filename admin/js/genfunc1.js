/**
 * genfunc.js
 *
 *
 * @category  member
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

//function $() {}
function updateTips(tips, t) {
    "use strict";
    tips.text(t).addClass("ui-state-highlight");
//    setTimeout(function() {
//        tips.removeClass( "ui-state-highlight", 360000 );
//    }, 500 );
}

function errorOnZero(o, n, tips) {
    "use strict";
    if (o.val() == "" || o.val() == "0" || o.val() == "00") {
        o.addClass("ui-state-error");
        updateTips(tips, n + " cannot be zero");
        return false;
    } else {
        return true;
    }
}

function checkLength(o, n, min, max, tips) {
    "use strict";
    if (o.val().length > max || o.val().length < min) {
        o.addClass("ui-state-error");
        if (o.val().length == 0) {
            updateTips(tips, "Fill in the " + n);
        } else if (min == max) {
            updateTips(tips, "The " + n + " must be " + max + " characters.");
        } else if (o.val().length > max) {
            updateTips(tips, "The " + n + " length is to long");
        } else {
            updateTips(tips, "The " + n + " length must be between " + min + " and " + max + ".");
        }
        return false;
    } else {
        return true;
    }
}

function isNumber(n) {
    "use strict";
    return !isNaN(parseFloat(n)) && isFinite(n);
}
function alertCallback(containerId) {
    "use strict";
    setTimeout(function () {
        $("#" + containerId + ":visible").removeAttr("style").fadeOut(500);
    }, 4500
    );
}

function changeMemberStatus(sc, memData, savePressed) {
    "use strict";

    // Check for duplicate
//    if (memData.id > 0 && sc.val() == "u" && memData.memStatus != 'u') {
//        var r = confirm("Really mark this member as a duplicate?  If so, another page will appear to process the duplicate member");
//        if (r == true) {
//            savePressed = true; // turn off form change checker
//            window.location.assign("procDuplicate.php?id=" + memData.id);
//        } else {
//            sc.children().each ( function () {
//                if (this.value == memData.memStatus) {
//                    this.selected = true;
//                }
//            });
//        }
//    }

    // Set background color for user emphasis
    if (sc.val() == "p" || sc.val() == "TBD" || sc.val() == "u") {
        sc.css('background', "RED");
    } else if (sc.val() == 'in' || sc.val() == 'd') {
        sc.css('background', "YELLOW");
    } else {
        sc.css('background', "WHITE");
    }

}

function handleResponse(dataTxt, statusTxt, xhrObject) {
    "use strict";
    $('div.ui-dialog-buttonset').css("display", "block");
    if (statusTxt != "success") {
        alert('Server had a problem.  ' + xhrObject.status + ", " + xhrObject.responseText);
    } else {
        var data,  wasError = false, r = "", i, spn;

        data = $.parseJSON(dataTxt);
        for (i = 0; i < data.lenth; i++) {
            if (data[i].error) {
                wasError = true;
                r += data[i].error;
            }
        }

        spn = document.getElementById('webMessage');

        if (!wasError) {
            // define the error message markup
            $('#webResponse').removeClass("ui-state-error").addClass("ui-state-highlight");
            //$('#webContainer').attr("style", "display:block;");
            $('#webIcon').removeClass("ui-icon-alert").addClass("ui-icon-info");
            spn.innerHTML = "Okay";
            $("#webContainer").show("slide", {}, 700, alertCallback('webContainer'));
        } else {
            // define the success message markup
            $('webResponse').removeClass("ui-state-highlight").addClass("ui-state-error");
            //$('#webContainer').attr("style", "display:block;");
            $('#webIcon').removeClass("ui-icon-info").addClass("ui-icon-alert");
            spn.innerHTML = "<strong>Error: </strong>" + r;
            $("#webContainer").show("slide", {}, 700, alertCallback('webContainer'));
        }
    }
}
function handleError(xhrObject, stat, thrwnError) {
    "use strict";
    $('div.ui-dialog-buttonset').css("display", "block");
    alert("Server error: " + stat + ", " + thrwnError);
}
function flagAlertMessage(mess, wasError) {
    "use strict";
    var spn = document.getElementById('alrMessage');

    if (!wasError) {
        // define the error message markup
        $('#alrResponse').removeClass("ui-state-error").addClass("ui-state-highlight");
        $('#alrIcon').removeClass("ui-icon-alert").addClass("ui-icon-info");
        spn.innerHTML = "<strong>Success: </strong>" + mess;
        $("#divAlert1").show("slide", {}, 500, alertCallback('divAlert1'));
    } else {
        // define the success message markup
        $('alrResponse').removeClass("ui-state-highlight").addClass("ui-state-error");
        $('#alrIcon').removeClass("ui-icon-info").addClass("ui-icon-alert");
        spn.innerHTML = "<strong>Alert: </strong>" + mess;
        $("#divAlert1").show("pulsate", {}, 200, alertCallback('divAlert1'));
    }
}
function handleChangePW(data, statusTxt, xhrObject) {
    "use strict";
    if (statusTxt != "success") {
        alert('Server had a problem, Password not changed  ');
    }

    var wasError = true, dataObj, msg, spn;

    if (data) {
        dataObj = $.parseJSON(data);
        msg = "";

        if (dataObj.error) {
            msg = dataObj.error;
        } else if (dataObj.success) {
            wasError = false;
            msg = dataObj.success;
        }

        spn = document.getElementById('webMessage');

        if (!wasError) {
            // define the error message markup
            $('#webResponse').removeClass("ui-state-error").addClass("ui-state-highlight");
            $('#webIcon').removeClass("ui-icon-alert").addClass("ui-icon-info");
            spn.innerHTML = "<strong>Success: </strong>" + msg;
            $("#webContainer").show("pulsate", {}, 400, alertCallback('webContainer'));
        } else {
            // define the success message markup
            $('webResponse').removeClass("ui-state-highlight").addClass("ui-state-error");
            $('#webIcon').removeClass("ui-icon-info").addClass("ui-icon-alert");
            spn.innerHTML = "<strong>Error: </strong>" + msg;
            $("#webContainer").show("slide", {}, 100, alertCallback('webContainer'));
        }
    }
}
function getDonationMarkup(id) {
    "use strict";
    $.post(
        "donate.php",
        {
            id: id,
            sq: $("#squirm").val(),
            cmd: 'markup'
        },
        function (data) {
            var msg;
            data = $.parseJSON(data);

            if (data.error) {
                msg = data.error;
            } else {
                msg = data.success;
            }
            $('#divListDonation').children().remove();
            $("#divListDonation").append($(msg));
        }
    );
}
function donateDeleteMarkup(dataTxt, id) {
    "use strict";
    var spn, data;

    data = $.parseJSON(dataTxt);
    spn = document.getElementById('donResultMessage');
    document.getElementById('damount').value = "";

    if (data.error) {
        // define the error message markup
        $('#donateResponse').removeClass("ui-state-highlight").addClass("ui-state-error");
        $('#donateResponseContainer').attr("style", "display:block;");
        $('#donateResponseIcon').removeClass("ui-icon-info").addClass("ui-icon-alert");
        spn.innerHTML = "<strong>Error: </strong>" + data.error;

    } else {
        // define the success message markup
        $('#donateResponse').removeClass("ui-state-error").addClass("ui-state-highlight");
        $('#donateResponseContainer').attr("style", "display:block;");
        $('#donateResponseIcon').removeClass("ui-icon-alert").addClass("ui-icon-info");
        spn.innerHTML = "Donation Deleted";

        // create the markup for the new donations list.
        getDonationMarkup(id);
    }
}

function donateResponse(dataTxt, id) {
    "use strict";
    var spn, cbox, data;

        data = $.parseJSON(dataTxt);
        spn = document.getElementById('donResultMessage');
        $('#damount').val('');
        $('#dnote').val('');

        if (data.error) {
            // define the error message markup
            $('#donateResponse').removeClass("ui-state-highlight").addClass("ui-state-error");
            $('#donateResponseContainer').attr("style", "display:block;");
            $('#donateResponseIcon').removeClass("ui-icon-info").addClass("ui-icon-alert");
            spn.innerHTML = "<strong>Error: </strong>" + data.error;

        } else {
            // define the success message markup
            $('#donateResponse').removeClass("ui-state-error").addClass("ui-state-highlight");
            $('#donateResponseContainer').attr("style", "display:block;");
            $('#donateResponseIcon').removeClass("ui-icon-alert").addClass("ui-icon-info");
            spn.innerHTML = "Okay";

            // create the markup for the new donations list.
            getDonationMarkup(id);

            // update the Member_Type.Donor record on the page if needed
            cbox = document.getElementById("Vol_Type_cb_d");
            if (cbox && !cbox.checked) {
                cbox.checked = 'checked';
            }
        }
}

function getCampaign(code) {
    "use strict";

    if (code != "") {
        $.post(
            "liveGetCamp.php",
            { qc: code },
            function (data) {
                data = $.parseJSON(data);
                var amtLimit = $('#cLimits');

                if (data.error) {
                    amtLimit.val('');
                } else if (data.camp) {

                    if (data.camp.mindonation == 0 && data.camp.maxdonation == 0) {
                        amtLimit.val("Any amount.");
                    } else if (data.camp.mindonation > 0 && data.camp.maxdonation == 0) {
                        amtLimit.val("At least $" + data.camp.mindonation);
                    } else if (data.camp.mindonation == 0 && data.camp.maxdonation > 0) {
                        amtLimit.val("At most $" + data.camp.maxdonation);
                    } else {
                        amtLimit.val("$" + data.camp.mindonation + " to $" + data.camp.maxdonation);
                    }
                    // Deal with scholarships
                    if (data.camp.type == 'sch' && $('#dselStudent option').length > 0) {
                        $('#dbdyStudent, #dhdrStudent').show();
                    } else {
                        $('#dbdyStudent, #dhdrStudent').hide();
                    }
                }
            }
        );
    }
}

function relationReturn(data) {

    data = $.parseJSON(data);
    if (data.error) {
        flagAlertMessage(data.error, true);
    } else if (data.success) {
        if (data.rc && data.markup) {
            var div = $('#acm' + data.rc);
            div.children().remove();
            var newDiv = $(data.markup);
            div.append(newDiv.children());
        }
        flagAlertMessage(data.success, false);
    }
}

function manageRelation(id, rId, relCode, cmd) {
    $.post('ws_gen.php', {'id':id, 'rId':rId, 'rc':relCode, 'cmd':cmd}, relationReturn);
}
var dtCols = [
    {
        "aTargets": [ 0 ],
        "sTitle": "Date",
        "sType": "date",
        "mDataProp": function (source, type, val) {
            "use strict";
            if (type === 'set') {
                source.LogDate = val;
                return null;
            } else if (type === 'display') {
                if (source.Date_display === undefined) {
                    var dt = new Date(Date.parse(source.LogDate));
                    source.Date_display = (dt.getMonth() + 1) + '/' + dt.getDate() + '/' + dt.getFullYear() + ' ' + dt.getHours() + ':' + dt.getMinutes();
                        //$.fullCalendar.formatDate(dt, "M/d/yyyy h:mmtt");
                }
                return source.Date_display;
            }
            return source.LogDate;
        }
    },
    {
        "aTargets": [ 1 ],
        "sTitle": "Type",
        "bSearchable": false,
        "bSortable": false,
        "mDataProp": "LogType"
    },
    {
        "aTargets": [ 2 ],
        "sTitle": "Sub-Type",
        "bSearchable": false,
        "bSortable": false,
        "mDataProp": "Subtype"
    },
     {
        "aTargets": [ 3 ],
        "sTitle": "User",
        "bSearchable": false,
        "bSortable": false,
        "mDataProp": "User"
    },
    {
        "aTargets": [ 4 ],
        "bVisible": false,
        "mDataProp": "idName"
    },
    {
        "aTargets": [ 5 ],
        "sTitle": "Log Text",
        "bSortable": false,
        "mDataProp": "LogText"
    }

];
