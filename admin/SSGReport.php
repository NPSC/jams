<?php
/**
 * PSG_Report.php
 *
 *
 * @category  house
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2013 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
require ("AdminIncludes.php");
require (CLASSES . 'PDOdata.php');
require (DB_TABLES . 'nameRS.php');
require (DB_TABLES . 'ActivityRS.php');
require (DB_TABLES . 'WebSecRS.php');
require (DB_TABLES . 'SsgRS.php');

require (CLASSES . 'StudentSupport/Ssg.php');
require (CLASSES . 'StudentSupport/Student.php');

require (CLASSES . 'Campaign.php');
require (MEMBER . 'Member.php');
require (MEMBER . 'IndivMember.php');
require (MEMBER . 'OrgMember.php');
require (MEMBER . 'Addresses.php');
require (MEMBER . 'WebUser.php');




try {
    $wInit = new webInit();
} catch (Exception $exw) {
    die("arrg!  " . $exw->getMessage());
}

$dbh = $wInit->dbh;

$pageTitle = $wInit->pageTitle;
$pageHeader = $wInit->pageHeading;

// get session instance
$uS = Session::getInstance();

$menuMarkup = $wInit->generatePageMenu();

// Load the session with member - based lookups
$wInit->sessionLoadGenLkUps();


$config = new Config_Lite(ciCFG_FILE);

// Instantiate the alert message control
$alertMsg = new alertMessage("divAlert1");
$alertMsg->set_DisplayAttr("none");
$alertMsg->set_Context(alertMessage::Success);
$alertMsg->set_iconId("alrIcon");
$alertMsg->set_styleId("alrResponse");
$alertMsg->set_txtSpanId("alrMessage");
$alertMsg->set_Text("help");

$resultMessage = $alertMsg->createMarkup();


$year = date('Y');

$studentSelected = TRUE;
$headerTable = '';
$mkTable = '0';
$totalsMarkup = '';
$who = 's';

// web service call
if (isset($_POST['cmd'])) {

    $events = array();
    try {
        $cmd = filter_var($_POST['cmd'], FILTER_SANITIZE_STRING);

        $who = 's';
        if (isset($_POST['who']) && $_POST['who'] == 'd') {
            $who = 'd';
        }

        $id = 0;
        if (isset($_POST['id'])) {
            $id = intval(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT), 10);
        }

        if ($id > 0) {

            if ($cmd == 'getdiag') {

                $events = array('markup' => StudentFunding::getDialog($dbh, $id, $who));

            } else if ($cmd == 'saveDiag') {

                if (isset($_POST['dons'])) {
                    $events = array('message' => StudentFunding::saveDialog($dbh, $id, $_POST['dons'], $uS->username));
                } else {
                    $events = array('error' => 'Donors not found.');
                }

            } else {

                $events = array('error' => 'Bad Command: ' . htmlspecialchars($cmd));

            }

        } else {

            $events = array('error' => 'Member Id not set.');
        }

    } catch (PDOException $ex) {
        $events = array("error" => "Database Error" . $ex->getMessage());
    } catch (Hk_Exception $ex) {
        $events = array("error" => "HouseKeeper Error" . $ex->getMessage());
    }

    if (is_array($events)) {
        echo (json_encode($events));
    } else {
        echo $events;
    }

    exit();
}


if (isset($_POST['btnHere']) || isset($_POST['btnExcel'])) {


    $local = TRUE;
    if (isset($_POST['btnExcel'])) {
        $local = FALSE;
    }


//    if (isset($_POST['selIntYear'])) {
//        $year = intval(filter_var($_POST['selIntYear'], FILTER_SANITIZE_NUMBER_INT), 10);
//    }

    if (isset($_POST['cbwho'])) {
        $who = filter_var($_POST['cbwho'], FILTER_SANITIZE_STRING);
    }


    if ($who == 's') {

        $query = "select
    s.idSsg,
    s.idStudent,
    s.Max_Amount,
    ns.Name_First,
    ns.Name_Last,
    s.Start_Date,
    s.Graduation_Date,
    sum(sc.Original_Amount - sc.Balance) as `Current_Amount`
from
    ssg s left join scholarship sc on s.Fund_Code = sc.Fund_Code and sc.Is_Deleted = 0
    left join name ns ON s.idStudent = ns.idName
where ns.Member_Status = 'a' and s.`Status` = 'a'
group by s.Fund_Code;";


        $stmt = $dbh->query($query);

        if ($local) {
            $tbl = new HTMLTable();
            $tbl->addHeaderTr(HTMLTable::makeTh('Student Id')
                    . HTMLTable::makeTh('First')
                    . HTMLTable::makeTh('Last')
                    . HTMLTable::makeTh('Starting')
                    . HTMLTable::makeTh('Graduation')
                    . HTMLTable::makeTh('Donations')
                    . HTMLTable::makeTh('Scholarship')
                    . HTMLTable::makeTh('% Funded')
                    . HTMLTable::makeTh('Get Funding')
            );
        } else {
            require CLASSES . 'OpenXML.php';

            $reportRows = 1;
            $file = 'SSGReport';
            $sml = OpenXML::createExcel($uS->username, 'Student Support Group Report');

            // build header
            $hdr = array();
            $n = 0;

            $hdr[$n++] = "Student Id";
            $hdr[$n++] = "First";
            $hdr[$n++] = "Last";
            $hdr[$n++] = "Start";
            $hdr[$n++] = "Graduation";
            $hdr[$n++] = "Doantions";
            $hdr[$n++] = "Scholarship";
            $hdr[$n++] = "Percent Funded";

            OpenXML::writeHeaderRow($sml, $hdr);
            $reportRows++;
        }


        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {


            $maxAmt = floatval($r['Max_Amount']);
            $curAmt = floatval($r['Current_Amount']);
            $funded = '';
            if ($maxAmt > 0) {
                $ratio = $curAmt / $maxAmt * 100;
                $funded = number_format($ratio) . '%';
            }

            if ($ratio >= 100) {
                continue;
            }

            if ($local) {

                $idsMarkup = HTMLContainer::generateMarkup('a', $r['idStudent'], array('href' => 'NameEdit.php?id=' . $r['idStudent']));

                $tbl->addBodyTr(
                        HTMLTable::makeTd($idsMarkup)
                        . HTMLTable::makeTd($r['Name_First'])
                        . HTMLTable::makeTd($r['Name_Last'])
                        . HTMLTable::makeTd($r['Start_Date'] == '' ? '' : date('Y', strtotime($r['Start_Date'])))
                        . HTMLTable::makeTd($r['Graduation_Date'] == '' ? '' : date('Y', strtotime($r['Graduation_Date'])))
                        . HTMLTable::makeTd('$'.number_format($curAmt, 2), array('style' => 'text-align:right;'))
                        . HTMLTable::makeTd('$'.number_format($maxAmt, 2), array('style' => 'text-align:right;'))
                        . HTMLTable::makeTd($funded, array('style' => 'text-align:center;'))
                        . HTMLTable::makeTd(($ratio >= 100 ? '' : HTMLInput::generateMarkup('Fund', array('type' => 'button', 'class' => 'hhk-fund', 'id' => 'btnStudent'.$r['idStudent'], 'data-who' => 's', 'data-id' => $r['idStudent']))), array('style' => 'text-align:center;'))
                );
            } else {

                $n = 0;
                $flds = array(
                    $n++ => array('type' => "n",
                        'value' => $r["idStudent"]
                    ),
                    $n++ => array('type' => "s",
                        'value' => $r['Name_First']
                    ),
                    $n++ => array('type' => "s",
                        'value' => $r["Name_Last"]
                    ),
                    $n++ => array('type' => "n",
                        'value' => PHPExcel_Shared_Date::PHPToExcel(new Datetime($r['Graduation_Date'])),
                        'style' => PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14
                    ),
                    $n++ => array('type' => "n",
                        'value' => PHPExcel_Shared_Date::PHPToExcel(new Datetime($r['Start_Date'])),
                        'style' => PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14
                    ),
                    $n++ => array('type' => "s",
                        'value' => $curAmt
                    ),
                    $n++ => array('type' => "s",
                        'value' => $maxAmt
                    ),
                    $n++ => array('type' => "s",
                        'value' => $funded
                    ),
                );

                $reportRows = OpenXML::writeNextRow($sml, $flds, $reportRows);
            }
        }

        if ($local) {


            $dataTable = $tbl->generateMarkup(array('id' => 'tblrpt'));
            $mkTable = 1;
        } else {

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $file . '.xlsx"');
            header('Cache-Control: max-age=0');

            OpenXML::finalizeExcel($sml);
            exit();
        }
    } else if ($who == 'd') {

        $query = "select
    sc.idName,
    n.Name_First,
    n.Name_Last,
    ifnull(s.Title, 'Unallocated') as `Title`,
    sc.Original_Amount,
    sc.`Balance`,
    sc.`Timestamp`
from
    scholarship sc
        left join
    `name` n ON sc.idName = n.idName
        left join
    ssg s ON sc.Fund_Code = s.Fund_Code and s.`Status` = 'a'
where n.Member_Status = 'a' and sc.Is_Deleted = 0
order by n.Name_Last , `Title`;";


        $stmt = $dbh->query($query);

        if ($local) {
            $tbl = new HTMLTable();
            $tbl->addHeaderTr(HTMLTable::makeTh('Donor Id')
                    . HTMLTable::makeTh('First')
                    . HTMLTable::makeTh('Last')
                    . HTMLTable::makeTh('Fund')
                    . HTMLTable::makeTh('Donation Date')
                    . HTMLTable::makeTh('Original Amount')
                    . HTMLTable::makeTh('Balance')
            );
        } else {
            require CLASSES . 'OpenXML.php';

            $reportRows = 1;
            $file = 'SSGdReport';
            $sml = OpenXML::createExcel($uS->username, 'Donor Report');

            // build header
            $hdr = array();
            $n = 0;

            $hdr[$n++] = "Donor Id";
            $hdr[$n++] = "First";
            $hdr[$n++] = "Last";
            $hdr[$n++] = "Fund";
            $hdr[$n++] = "Date";
            $hdr[$n++] = "Original Amount";
            $hdr[$n++] = "Balance";

            OpenXML::writeHeaderRow($sml, $hdr);
            $reportRows++;
        }


        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {


            $origAmt = floatval($r['Original_Amount']);
            $curAmt = floatval($r['Balance']);


            if ($local) {

                $idsMarkup = HTMLContainer::generateMarkup('a', $r['idName'], array('href' => 'NameEdit.php?id=' . $r['idName']));
                $balAttr = array('style' => 'text-align:right;');
                if ($curAmt > 0) {
                    $balAttr['style'] .= 'font-weight:bold;';
                }

                $tbl->addBodyTr(
                        HTMLTable::makeTd($idsMarkup)
                        . HTMLTable::makeTd($r['Name_First'])
                        . HTMLTable::makeTd($r['Name_Last'])
                        . HTMLTable::makeTd($r['Title'])
                        . HTMLTable::makeTd($r['Timestamp'] == '' ? '' : date('M j, Y', strtotime($r['Timestamp'])))
                        . HTMLTable::makeTd(number_format($origAmt, 2), array('style' => 'text-align:right;'))
                        . HTMLTable::makeTd(number_format($curAmt, 2), $balAttr)
                );
            } else {

                $n = 0;
                $flds = array(
                    $n++ => array('type' => "n",
                        'value' => $r["idName"]
                    ),
                    $n++ => array('type' => "s",
                        'value' => $r['Name_First']
                    ),
                    $n++ => array('type' => "s",
                        'value' => $r["Name_Last"]
                    ),
                    $n++ => array('type' => "s",
                        'value' => $r["Title"]
                    ),
                    $n++ => array('type' => "n",
                        'value' => PHPExcel_Shared_Date::PHPToExcel(new Datetime($r['Timestamp'])),
                        'style' => PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14
                    ),
                    $n++ => array('type' => "n",
                        'value' => $origAmt
                    ),
                    $n++ => array('type' => "n",
                        'value' => $curAmt
                    ),
                );

                $reportRows = OpenXML::writeNextRow($sml, $flds, $reportRows);
            }
        }

        if ($local) {


            $dataTable = $tbl->generateMarkup(array('id' => 'tblrpt'));
            $mkTable = 1;
        } else {

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $file . '.xlsx"');
            header('Cache-Control: max-age=0');

            OpenXML::finalizeExcel($sml);
            exit();
        }
    } else {
        // all students

        $query = "select
    ifnull(s.idSsg, ''),
    ns.idName,
    ns.Name_First,
    ns.Name_Last,
    ifnull(s.Start_Date, '') as Start_Date,
    ifnull(s.Graduation_Date, '') as Graduation_Date,
    ifnull(s.Fund_Code, '') as Fund_Code,
    ifnull(s.Current_Amount, 0) as Current_Amount,
    ifnull(s.Max_Amount, 0) as Max_Amount
from
    name ns left join ssg s ON s.idStudent = ns.idName
where ns.Member_Status = 'a' and ns.Member_Type = '" . MemBasis::Student . "';";

        $stmt = $dbh->query($query);

        if ($local) {
            $tbl = new HTMLTable();
            $tbl->addHeaderTr(HTMLTable::makeTh('Student Id')
                    . HTMLTable::makeTh('First')
                    . HTMLTable::makeTh('Last')
                    . HTMLTable::makeTh('Start')
                    . HTMLTable::makeTh('Graduation')
                    . HTMLTable::makeTh('Fund Code')
                    . HTMLTable::makeTh('Donations')
                    . HTMLTable::makeTh('Scholarship')
            );

        } else {

            require CLASSES . 'OpenXML.php';

            $reportRows = 1;
            $file = 'SSGReport';
            $sml = OpenXML::createExcel($uS->username, 'Student Support Group Report');

            // build header
            $hdr = array();
            $n = 0;

            $hdr[$n++] = "Student Id";
            $hdr[$n++] = "First";
            $hdr[$n++] = "Last";
            $hdr[$n++] = "Start";
            $hdr[$n++] = "Graduation";
            $hdr[$n++] = "Fund Code";
            $hdr[$n++] = "Donation";
            $hdr[$n++] = "Scholarship";

            OpenXML::writeHeaderRow($sml, $hdr);
            $reportRows++;
        }

        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $curAmt = 0;
            if ($r['Current_Amount'] != '') {
                $curAmt = floatval($r['Current_Amount']);
            }
            $maxAmt = 0;
            if ($r['Max_Amount'] != '') {
                $maxAmt = floatval($r['Max_Amount']);
            }

            if ($local) {

                $idsMarkup = HTMLContainer::generateMarkup('a', $r['idName'], array('href' => 'NameEdit.php?id=' . $r['idName']));

                $tbl->addBodyTr(
                        HTMLTable::makeTd($idsMarkup)
                        . HTMLTable::makeTd($r['Name_First'])
                        . HTMLTable::makeTd($r['Name_Last'])
                        . HTMLTable::makeTd($r['Start_Date'] == '' ? '' : date('Y', strtotime($r['Start_Date'])))
                        . HTMLTable::makeTd($r['Graduation_Date'] == '' ? '' : date('Y', strtotime($r['Graduation_Date'])))
                        . HTMLTable::makeTd($r['Fund_Code'])
                        . HTMLTable::makeTd(number_format($curAmt, 2), array('style' => 'text-align:right;'))
                        . HTMLTable::makeTd(number_format($maxAmt, 2), array('style' => 'text-align:right;'))
                );

            } else {

                $n = 0;
                $flds = array(
                    $n++ => array('type' => "n",
                        'value' => $r["idName"]
                    ),
                    $n++ => array('type' => "s",
                        'value' => $r['Name_First']
                    ),
                    $n++ => array('type' => "s",
                        'value' => $r["Name_Last"]
                    ),
                    $n++ => array('type' => "n",
                        'value' => PHPExcel_Shared_Date::PHPToExcel(new Datetime($r['Start_Date'])),
                        'style' => PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14
                    ),
                    $n++ => array('type' => "n",
                        'value' => PHPExcel_Shared_Date::PHPToExcel(new Datetime($r['Graduation_Date'])),
                        'style' => PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14
                    ),
                    $n++ => array('type' => "s",
                        'value' => $r['Fund_Code']
                    ),
                    $n++ => array('type' => "n",
                        'value' => $curAmt
                    ),
                    $n++ => array('type' => "n",
                        'value' => $maxAmt
                    )
                );

                $reportRows = OpenXML::writeNextRow($sml, $flds, $reportRows);
            }
        }

        if ($local) {


            $dataTable = $tbl->generateMarkup(array('id' => 'tblrpt'));
            $mkTable = 1;
        } else {

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $file . '.xlsx"');
            header('Cache-Control: max-age=0');

            OpenXML::finalizeExcel($sml);
            exit();
        }
    }

}



if ($who == 's') {
    $sAttr = array('name' => 'cbwho', 'id' => 'cbStudent', 'type' => 'radio', 'checked' => 'checked');
    $aAttr = array('name' => 'cbwho', 'id' => 'cbAllStudent', 'type' => 'radio');
    $dAttr = array('name' => 'cbwho', 'id' => 'cbDonor', 'type' => 'radio');
} else if ($who == 'd') {
    $sAttr = array('name' => 'cbwho', 'id' => 'cbStudent', 'type' => 'radio');
    $aAttr = array('name' => 'cbwho', 'id' => 'cbAllStudent', 'type' => 'radio');
    $dAttr = array('name' => 'cbwho', 'id' => 'cbDonor', 'type' => 'radio', 'checked' => 'checked');
} else {
    $sAttr = array('name' => 'cbwho', 'id' => 'cbStudent', 'type' => 'radio');
    $aAttr = array('name' => 'cbwho', 'id' => 'cbAllStudent', 'type' => 'radio', 'checked' => 'checked');
    $dAttr = array('name' => 'cbwho', 'id' => 'cbDonor', 'type' => 'radio');

}

$memSel = HTMLInput::generateMarkup('a', $aAttr) . HTMLContainer::generateMarkup('label', ' All Students', array('for' => 'cbAllStudent'));
$memSel .= '<br/>' . HTMLInput::generateMarkup('s', $sAttr) . HTMLContainer::generateMarkup('label', ' Students needing funding', array('for' => 'cbStudent'));
$memSel .= '<br/>' . HTMLInput::generateMarkup('d', $dAttr) . HTMLContainer::generateMarkup('label', ' All Scholarship Donors', array('for' => 'cbDonor'));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $pageTitle; ?></title>
        <link href="<?php echo JQ_UI_CSS; ?>" rel="stylesheet" type="text/css" />
        <link href="css/default.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo JQ_DT_CSS; ?>" rel="stylesheet" type="text/css" />
<?php echo TOP_NAV_CSS; ?>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo JQ_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo JQ_UI_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo JQ_DT_JS ?>"></script>
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo PRINT_AREA_JS ?>"></script>
        <script type="text/javascript">
            function saveDialog(id, diag) {
                var parms = {dons: dons, id: id, cmd: 'saveDiag'};
                $.post('SSGReport.php', parms, function (data) {
                    try {
                        data = $.parseJSON(data);
                    } catch (err) {
                        alert('Bad JSON Encoding');
                        return;
                    }
                    if (data.message && data.message != '') {
                        $('#divAlertMsg').text(data.message);
                    }
                    diag.dialog('close');
                });
            }
            function getDialog(who, id) {
                $.post('SSGReport.php', {'id': id, 'who': who, 'cmd': 'getdiag'}, function (data) {
                    try {
                        data = $.parseJSON(data);
                    } catch (err) {
                        alert('Bad JSON Encoding');
                        return;
                    }
                    if (data.markup) {
                        var diag = $("#fundDialog");
                        diag.children().remove().end().append($(data.markup))
                                .dialog('option', 'buttons', {
                                    'Save': function () {
                                        saveDialog(id, diag)
                                    },
                                    "Cancel": function () {
                                        $(this).dialog("close");
                                    }
                                });
                        $('.hhk-fund-take').change(function () {
                            var amtStr = $('#spnMaxAmt').text();
                            var maxAmt = parseFloat(amtStr.replace(',', ''));
                            if (isNaN(maxAmt)) {
                                maxAmt = 0;
                            }
                            amtStr = $('#spnSubAmt').text();
                            var curAmt = parseFloat(amtStr.replace(',', ''));
                            if (isNaN(curAmt)) {
                                curAmt = 0;
                            }
                            var curTake = 0, n = 0;
                            dons = [];
                            $('.hhk-fund-take').each(function (index) {
                                var id = $(this).data('fc').toString() + $(this).data('id').toString();
                                var did = parseInt($(this).data('id'));
                                var fc = parseInt($(this).data('fc'));
                                if (isNaN(id)) {
                                    id = 0;
                                }
                                var balStr = $('#txtbal_' + id).text();
                                var bal = parseFloat(balStr.replace(',', ''));
                                if (isNaN(bal)) {
                                    bal = 0;
                                }
                                var takeStr = $(this).val();
                                var take = takeStr.replace(',', '');
                                if (take === 'a' || take === 'all') {
                                    take = bal;
                                } else {
                                    take = parseFloat(take);
                                    if (isNaN(take)) {
                                        take = 0;
                                    }
                                }
                                if (take > bal) {
                                    take = bal;
                                }
                                if (take > maxAmt - curAmt) {
                                    take = maxAmt - curAmt;
                                }
                                if (take > 0) {
                                    curAmt = curAmt + take;
                                    dons[n++] = {'take': take, 'fc': fc, 'id': did};
                                    curTake += take;
                                }
                                if (take === 0) {
                                    $(this).val('');
                                } else {
                                    $(this).val(take);
                                }
                            });
                            $('#spnTotalTake').text(curTake);
                        });
                        diag.dialog('option', 'title', 'Distribute Funding');
                        diag.dialog('open');
                    }
                });
            }
            var dons = [];
            $(document).ready(function () {
                var makeTable = '<?php echo $mkTable; ?>';
                $('#btnHere, #btnExcel').button();
                if (makeTable === '1') {
                    $('div#printArea').css('display', 'block');
                    try {
                        $('#tblrpt').dataTable({
                            "iDisplayLength": 50,
                            "aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
                            "dom": '<"top"ilf>rt<"bottom"lp><"clear">',
                        });
                    }
                    catch (err) {
                    }
                    $('#printButton').button().click(function () {
                        $("div#printArea").printArea();
                    });
                }
                $('.hhk-fund').button();
                $('#reportArea').on('click', '.hhk-fund', function () {
                    getDialog($(this).data('who'), $(this).data('id'));
                });
                $("#fundDialog").dialog({
                    autoOpen: false,
                    resizable: true,
                    width: 750,
                    modal: true,
                });
            });
        </script>
    </head>
    <body <?php if ($wInit->testVersion) {echo "class='testbody'";} ?>>
<?php echo $menuMarkup; ?>
        <div id="contentDiv">
            <h2><?php echo $wInit->pageHeading; ?></h2>
            <div id="divAlertMsg"><?php echo $resultMessage; ?></div>
            <form id="fcat" action="SSGReport.php" method="post">
                <div class="ui-widget ui-widget-content ui-corner-all hhk-member-detail" style="clear:left; min-width: 400px; padding:10px;">
                    <table style="float: left;margin-left:.5em;">
                        <tr>
                            <th>Report Type</th>
                        </tr>
                        <tr>
                            <td><?php echo $memSel; ?></td>
                        </tr>
                    </table>
                    <table style="width:100%; clear:both;">
                        <tr>
                            <td style="width:30%;"></td>
                            <td><input type="submit" name="btnHere" id="btnHere" value="Run Here"/></td>
                            <td><input type="submit" name="btnExcel" id="btnExcel" value="Download to Excel"/></td>
                        </tr>
                    </table>
                </div>
            </form>
            <div style="clear:both;"></div>
            <div id="printArea" class="ui-widget ui-widget-content hhk-tdbox" style="display:none; float:left; min-width: 900px; font-size: .9em; padding: 5px;">
                <div><input id="printButton" value="Print" type="button"/></div>
                <div id="reportArea">
<?php echo $dataTable; ?>
                </div>
            </div>
            <div id="fundDialog" class="hhk-tdbox hhk-visitdialog" style="display:none;font-size:.9em;"></div>
        </div>
    </body>
</html>
