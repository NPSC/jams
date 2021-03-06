<?php
/**
 * step3.php
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



if (isset($_POST['btnNext'])) {
    header('location:../index.php');
}


// Check for returns
if (isset($_POST['btnSave'])) {

    $filedata = file_get_contents('initialdata.sql');
    $parts = explode('-- ;', $filedata);

    foreach ($parts as $q) {
        if ($q != '') {
            try {
                $dbh->exec($q);
            } catch (PDOException $pex) {
                $errorMsg .= $pex->getMessage();
            }
        }
    }

    if ($errorMsg == '') {
        $resultAccumulator = 'Okay.';
    }


}

$hostURL = $config->getString('site', 'Site_URL');

$url = parse_url($hostURL);

if (isset($_POST['btnWeb'])) {
    // Update website table
    $webRS = new Web_SitesRS();
    $rows = EditRS::select($dbh, $webRS, array());

    foreach ($rows as $w) {

        $webRS = new Web_SitesRS();
        EditRS::loadRow($w, $webRS);

        $host = '';

        switch ($webRS->Site_Code->getStoredVal()) {

            case 'a':
                $host = $config->getString('site', 'Admin_URL', '');
                break;


            case 'h':
                $host = $config->getString('site', 'House_URL', '');
                break;

            case 'v':
                $host = $config->getString('site', 'Volunteer_URL', '');
                break;

            case 'r':
                $host = $config->getString('site', 'Site_URL', '');
                break;
        }

        if ($host == '') {
            continue;
        }

        $url = parse_url($host);

        $webRS->HTTP_Host->setNewVal($url['host']);

        if (isset($url['path'])) {
            $webRS->Relative_Address->setNewVal($url['path']);
        }

        EditRS::update($dbh, $webRS, array($webRS->idweb_sites));
    }

}



?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $pageTitle; ?></title>
        <style>
            .tblhdr {background-color: tomato}
            .tdtitle {width: 22%; text-align: right; margin-right:3px;}
        </style>
    </head>
    <body>
        <div id="page" style="width:900px;">
            <div>
                <h2 class="logo">Hospitality HouseKeeper Installation Process</h2>
                <h3>Step Three: Load Meta-data</h3>
            </div><div class='pageSpacer'></div>
            <div id="content" style="margin:10px; width:100%;">
                <div><span style="color:red;"><?php echo $errorMsg; ?></span></div>
                <form method="post" action="step3.php" name="form1" id="form1">
                    <p>URL: <?php echo $ssn->databaseURL; ?></p>
                    <p>Schema: <?php echo $ssn->databaseName; ?></p>
                    <p>User: <?php echo $ssn->databaseUName; ?></p>
                    <p>Host: <?php echo $url['host'] ?></p>
                    <p><?php echo $resultAccumulator; ?></p>

                    <input type="submit" name="btnSave" id="btnSave" value="Load Metadata" style="margin-left:200px;margin-top:20px;"/>
                    <input type="submit" name="btnWeb" id="btnSave" value="Update Web-Sites Table" style="margin-left:7px;margin-top:20px;"/>
                    <input type="submit" name="btnNext" id="btnNext" value="Next" style="margin-left:7px;margin-top:20px;"/>
                </form>
            </div>
        </div>
    </body>
</html>

