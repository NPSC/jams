<?php
/**
 * step4.php
 *
 * @category  Installer
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
require_once ("InstallIncludes.php");
require_once (CLASSES . 'PDOdata.php');
require_once (DB_TABLES . 'WebSecRS.php');

require_once(SEC . 'Login.php');

try {

    $login = new Login();
    $config = $login->initializeSession(ciCFG_FILE);
} catch (PDOException $pex) {
    echo ("Database Error.  " . $pex->getMessage());
} catch (Exception $ex) {
    echo ("<h3>Server Error</h3>" . $ex->getMessage());
}

// get session instance
$ssn = Session::getInstance();

$pageTitle = $ssn->siteName;

// define db connection obj
$dbh = initPDO();


$errorMsg = '';
$resultAccumulator = "";

// Check for returns
if (isset($_POST['btnSave'])) {


}

if (isset($_POST['btnNext'])) {
    header('location:step4.php');
}

    $wgroupsRs = new W_groupsRS();
    $groups = EditRS::select($dbh, $wgroupsRs, array());

    $tbl = new HTMLTable();

    foreach ($groups as $g) {
        $wgroupsRs = new W_groupsRS();
        EditRS::loadRow($g, $wgroupsRs);

        $tbl->addBodyTr(
                HTMLTable::makeTd($wgroupsRs->Group_Code->getStoredVal())
                .HTMLTable::makeTd($wgroupsRs->Description->getStoredVal())
                .HTMLTable::makeTd($wgroupsRs->Cookie_Restricted->getStoredVal())

                );

    }

    $tbl->addHeaderTr(HTMLTable::makeTh('Code') . HTMLTable::makeTh('Description') . HTMLTable::makeTh('Cookie Restricted'));

?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $pageTitle; ?></title>
        <script type="text/javascript" src="../js/md5-min.js"></script>
        <script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                "use strict";
                $.ajaxSetup({
                    beforeSend: function() {
                        $('body').css('cursor', "wait");
                    },
                    complete: function() {
                        $('body').css('cursor', "auto");
                    },
                    cache: false
                });
            });
        </script>
        <style>
            .tblhdr {background-color: tomato}
            .tdtitle {width: 22%; text-align: right; margin-right:3px;}
        </style>
    </head>
    <body>
        <div id="page" style="width:900px;">
            <div>
                <h2 class="logo">Hospitality HouseKeeper Installation Process

                </h2>
                <h3>Step Three: Configure Authorizations</h3>
            </div><div class='pageSpacer'></div>
            <div id="content" style="margin:10px; width:100%;">
                <div><span style="color:red;"><?php echo $errorMsg; ?></span></div>
                <form method="post" action="step4.php" name="form1" id="form1">
                    <p>URL: <?php echo $ssn->databaseURL; ?>; Schema: <?php echo $ssn->databaseName; ?>; User: <?php echo $ssn->databaseUName; ?></p>
                    <p><?php echo $resultAccumulator; ?></p>
                    <div><?php echo $tbl->generateMarkup(); ?></div>
                    <input type="submit" name="btnSave" id="btnSave" value="Save" style="margin-left:700px;margin-top:20px;"/>
                    <input type="submit" name="btnNext" id="btnNext" value="Next" style="margin-left:7px;margin-top:20px;"/>
                </form>
            </div>
        </div>
    </body>
</html>

