<?php
/**
 * step2.php
 *
 * @category  Installer
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
require_once ("InstallIncludes.php");
require_once(SEC . 'Login.php');
require_once(FUNCTIONS . 'mySqlFunc.php');

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

$driver = $dbh->getAttribute(PDO::ATTR_DRIVER_NAME);
if ($driver != 'mysql') {
    echo('Driver not mysql.  Manually load the database.');
    exit();
}

//$stmt = $dbh->query("show variables like 'max_allowed_packet';");
//$rows = $stmt->fetchAll();
//$maxPacketSize = $rows[0][1];

$errorMsg = '';
$resultAccumulator = "";

// Check for returns
if (isset($_POST['btnSave'])) {


    try {
        $mysqli = new mysqli($ssn->databaseURL, $ssn->databaseUName, $ssn->databasePWord, $ssn->databaseName);
    } catch (mysqli_sql_exception $ex) {
        $errorMsg = "Connect failed: " . $ex->getMessage();
    }

    /* check connection */
    if ($mysqli->connect_error) {

        $errorMsg .= "Connect failed: " . $mysqli->connect_error;

    } else {

        $filename = '../sql/CreateAllTables.sql';
        $tableFile = file_get_contents($filename);

        $errorMsg .= multiQuery($mysqli, $tableFile);

        if ($errorMsg == '') {
            $resultAccumulator .= "Tables Loaded ... ";
        }

        if ($errorMsg == '') {

            $viewFile = file_get_contents('../sql/CreateAllViews.sql');
            $errorMsg .= multiQuery($mysqli, $viewFile);
        }

        if ($errorMsg == '') {

            $resultAccumulator .= "Views Loaded ... ";

            $spFile = file_get_contents('../sql/CreateAllRoutines.sql');
            $errorMsg .= multiQuery($mysqli, $spFile, '$$', '-- ;');
        }

        if ($errorMsg == '') {
            $resultAccumulator .= "Stored Procedures loaded!  All done.";
        }
    }
}

if (isset($_POST['btnNext'])) {
    header('location:step3.php');
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $pageTitle; ?></title>
    </head>
    <body>
        <div id="page" style="width:900px;">
            <div>
                <h2 class="logo">Hospitality HouseKeeper Installation Process</h2>
                <h3>Step Two: Install Database</h3>
            </div><div class='pageSpacer'></div>
            <div id="content" style="margin:10px; width:100%;">
                <div><span style="color:red;"><?php echo $errorMsg; ?></span></div>
                <form method="post" action="step2.php" name="form1" id="form1">
                    <p>URL: <?php echo $ssn->databaseURL; ?></p>
                    <p>Schema: <?php echo $ssn->databaseName; ?></p>
                    <p>User: <?php echo $ssn->databaseUName; ?></p>
                    <p><?php echo $resultAccumulator; ?></p>
                    <input type="submit" name="btnSave" id="btnSave" value="Install DB" style="margin-left:700px;margin-top:20px;"/>
                    <input type="submit" name="btnNext" id="btnNext" value="Next" style="margin-left:7px;margin-top:20px;"/>
                </form>
            </div>
        </div>
    </body>
</html>

