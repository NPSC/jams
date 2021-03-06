<?php
/**
 * Duplicates.php
 *
 * @category  Reports
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

require ("AdminIncludes.php");
require_once (CLASSES . 'PDOdata.php');
require (DB_TABLES . 'nameRS.php');
require (DB_TABLES . 'visitRS.php');
require (DB_TABLES . 'HouseRS.php');
require (DB_TABLES . 'registrationRS.php');
require (DB_TABLES . 'ActivityRS.php');
require (DB_TABLES . 'ReservationRS.php');
require (DB_TABLES . 'PaymentsRS.php');

require (MEMBER . 'Member.php');
require (MEMBER . 'IndivMember.php');
require (MEMBER . 'OrgMember.php');
require (MEMBER . 'Addresses.php');


require(CLASSES . "chkBoxCtrlClass.php");
require(CLASSES . "selCtrl.php");
require(CLASSES . "OpenXML.php");
//require_once(CLASSES . "XML_Doc.php");

require (HOUSE . 'psg.php');
require (HOUSE . 'Role.php');
require (HOUSE . 'Guest.php');
require (HOUSE . 'Patient.php');
require (HOUSE . 'RoleMember.php');
require (HOUSE . 'Registration.php');
require (HOUSE . 'Reservation_1.php');
require (HOUSE . 'ReservationSvcs.php');
require (HOUSE . 'visitViewer.php');
require (HOUSE . 'Visit.php');
require (HOUSE . 'Fees.php');
require (HOUSE . 'Vehicle.php');
require (HOUSE . 'HouseServices.php');
require (HOUSE . 'Resource.php');
require (HOUSE . 'Room.php');
require (HOUSE . 'Hospital.php');

require (CLASSES . 'FinAssistance.php');
require (CLASSES . 'Notes.php');
require (CLASSES . 'emailClass.php');
require (CLASSES . 'Duplicate.php');
require (CLASSES . 'CreateMarkupFromDB.php');


$wInit = new webInit();
$dbh = $wInit->dbh;
$uS = Session::getInstance();

// AJAX
if (isset($_REQUEST['cmd'])) {

    $cmd = filter_var($_REQUEST['cmd'], FILTER_SANITIZE_STRING);
    $events = array();
    unset($uS->dupids);

    switch ($cmd) {

        case 'exp':

            $markup = '';
            if (isset($_REQUEST['nf']) && $_REQUEST['nf'] != '') {

                $fullName = filter_var($_REQUEST['nf'], FILTER_SANITIZE_STRING);

                // Expand this selection
                $expansion = Duplicate::expandName($dbh, $fullName);
                $data = array();
                $ids = array();

                foreach ($expansion as $d) {

                    $ids[$d['Id']] = $d['Id'];
                    $d['Id'] = HTMLInput::generateMarkup($d['Id'], array('type'=>'button', 'data-id'=>$d['Id'], 'class'=>'hhk-pick', 'title'=>'Click to choose'));
                    $data[] = $d;
                }

                $uS->dupids = $ids;


                $markup = CreateMarkupFromDB::generateHTML_Table($data, 'pickId');

            }

            $events = array('mk'=>$markup);

            break;

        case 'pik':

            $id = 0;
            if (isset($_REQUEST['id'])) {
                $id = intval(filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT), 10);
            }

            if (isset($uS->dupids) !== FALSE) {
                $events = Duplicate::processDup($dbh, $id, $uS->dupids);
            }

            break;

    }

    echo json_encode($events);
    exit();
}


$pageTitle = $wInit->pageTitle;
$testVersion = $wInit->testVersion;

$menuMarkup = $wInit->generatePageMenu();
$markup = '';

if (isset($_POST['btnGo'])) {

    $mType = '';
    if (isset($_POST['mtype'])){
        $mType = filter_var($_POST['mtype'], FILTER_SANITIZE_STRING);
    }

    $dups = new Duplicate();
    $msg = $dups->getNameDuplicates($dbh, $mType);

    if ($msg == '') {

        $data = array();

        foreach ($dups->dupNames as $d) {

            $data[] = array('Name'=>HTMLInput::generateMarkup($d['Name_Full'], array('type'=>'button', 'data-fn'=>$d['Name_Full'], 'class'=>'hhk-expand', 'title'=>'Click to expand')), 'Count'=>$d['dups']);
        }
        $markup = CreateMarkupFromDB::generateHTML_Table($data, 'dupNames');
    } else {
        $markup = $msg;
    }

}

// Instantiate the alert message control
$alertMsg = new alertMessage("divAlert1");
$resultMessage = "";


?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $pageTitle; ?></title>
        <link href="<?php echo JQ_UI_CSS; ?>" rel="stylesheet" type="text/css" />
        <link href="css/default.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo JQ_DT_CSS; ?>" rel="stylesheet" type="text/css" />
<?php echo TOP_NAV_CSS; ?>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo JQ_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo JQ_UI_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo PRINT_AREA_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo JQ_DT_JS; ?>"></script>
        <script type="text/javascript">
            // Init j-query
            $(document).ready(function() {
                $('.hhk-expand').click(function () {
                    $.post('Duplicates.php?cmd=exp&nf=' + $(this).data('fn'),
                    function (data) {
                       "use strict";
                       if (!data) {
                           alert('Bad Reply from Server');
                           return;
                       }
                       try {
                           data = $.parseJSON(data);
                       } catch (err) {
                           alert("Parser error - " + err.message);
                           return;
                       }
                       if (data.error) {
                           flagAlertMessage(data.error, true);
                           return;
                       }
                       $('#divExpansion').children().remove().end().append($(data.mk));
                       $('.hhk-pick').click(function () {
                            $.post('Duplicates.php?cmd=pik&id=' + $(this).data('id'),
                            function (data) {
                               "use strict";
                               if (!data) {
                                   alert('Bad Reply from Server');
                                   return;
                               }
                               try {
                                   data = $.parseJSON(data);
                               } catch (err) {
                                   alert("Parser error - " + err.message);
                                   return;
                               }
                               if (data.error) {
                                   flagAlertMessage(data.error, true);
                                   return;
                               }

                               });
                        });
                });
            });
        });
        </script>
    </head>
    <body <?php if ($testVersion) echo "class='testbody'"; ?>>
            <?php echo $menuMarkup; ?>
        <div id="contentDiv">
            <h1><?php echo $wInit->pageHeading; ?></h1>
            <?php echo $resultMessage ?>
            <form action="#" method="POST">
                <select name="mtype"><option value="g">Guest</option><option value="p">Patient</option><option value="ra">Referral Agent</option></select>
                <input type="submit" value="Go" name="btnGo"/>
            </form>
            <div class="ui-widget ui-widget-content ui-corner-all hhk-member-detail">
                <?php echo $markup; ?>
            </div>
            <div id="divExpansion" style="clear:left;" class="ui-widget ui-widget-content ui-corner-all hhk-member-detail"></div>
        </div>
    </body>
</html>
