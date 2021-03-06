<?php
/**
 * NameSch.php
 *
 * @category  member
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
require ("AdminIncludes.php");
require (CLASSES . 'CreateMarkupFromDB.php');
require (CLASSES . 'History.php');
define('FULLC_JS', 'js/fullcalendar.min.js');
define('FULLC_CSS', 'css/fullcalendar.css');

$wInit = new webInit();

$dbh = $wInit->dbh;


$pageTitle = $wInit->pageTitle;
$testVersion = $wInit->testVersion;

$menuMarkup = $wInit->generatePageMenu();

$uS = Session::getInstance();


$recHistory = History::getMemberHistoryMarkup($dbh);
$stuHistory = History::getStudentHistoryMarkup($dbh);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $pageTitle; ?></title>
        <link href="<?php echo JQ_UI_CSS; ?>" rel="stylesheet" type="text/css" />
        <link href="css/default.css" rel="stylesheet" type="text/css" />
<?php echo TOP_NAV_CSS; ?>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo JQ_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo JQ_UI_JS; ?>"></script>
        <script type="text/javascript">
    function isNumber(n) {
        "use strict";
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    $(document).ready(function() {
        var lastXhr;
        var d=new Date();
        $('#historyTabs').tabs();
        $('#txtsearch').autocomplete({
            source: function (request, response) {
                // Don't send for numbers
                if (isNumber(parseInt(request.term, 10))) {
                    response();
                }
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
                    }
                    });
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
                    window.location = "NameEdit.php?cmd=neworg";
                }

                var cid = parseInt(ui.item.id, 10);
                if (isNumber(cid)) {
                    window.location = "NameEdit.php?id=" + cid;
                }
            }
        });
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
    });
        </script>
    </head>
    <body <?php if ($testVersion) echo "class='testbody'"; ?> >
<?php echo $menuMarkup; ?>
        <div id="contentDiv">
            <h2><?php echo $wInit->pageHeading; ?></h2>
            <div class="ui-widget ui-widget-content ui-corner-all hhk-member-detail"  style="background:#EFDBC2; margin-bottom:10px;">
                <div style="float: left; border-width: 1px; border-color: gray; border-style: ridge; padding: 2px;">
                    <span>Search: </span>
                    <span style="margin: 0 10px;">
                        <label for="rbmemName">Name</label><input type="radio" name="msearch" checked="checked" id="rbmemName" />
                        <label for="rbmemEmail">Email</label><input type="radio" name="msearch" id="rbmemEmail" />
                    </span>
                    <input type="text" id="txtsearch" size="20" title="Enter at least 3 characters to invoke search" />
                </div>
            </div>
            <div style="clear:both; margin-top:50px"></div>
            <div id="historyTabs" class="hhk-member-detail" style="margin-bottom: 10px;">
                <ul>
                    <li><a href="#memHistory">Member History</a></li>
                    <li><a href="#student">Student History</a></li>
                </ul>
                <div id="memHistory">
                    <h3>Donor History</h3>
                    <?php echo $recHistory; ?>
                </div>
                <div id="student">
                    <h3>Student History</h3>
                    <?php echo $stuHistory; ?>
                </div>
            </div>
        </div>  <!-- div id="page"-->
    </body>
</html>
