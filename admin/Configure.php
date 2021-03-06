<?php
/**
 * Configure.php
 *
 * @category  Configuraton
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
require_once ("AdminIncludes.php");
require_once CLASSES . 'PDOdata.php';
require_once CLASSES . 'SiteConfig.php';
require_once CLASSES . 'Patch.php';
require_once CLASSES . 'US_Holidays.php';
require_once DB_TABLES . 'MercuryRS.php';
require_once DB_TABLES . 'HouseRS.php';
require_once(FUNCTIONS . 'mySqlFunc.php');


function readZipFile($file) {
    $zip = Zip_open($file);

    if (is_resource($zip)) {

        $entry = zip_read($zip);
        $na = zip_entry_name($entry);

        $content = zip_entry_read($entry, zip_entry_filesize($entry));

        Zip_entry_close($entry);
        zip_close($zip);

        if ($content === FALSE) {
            throw new Hk_Exception_Runtime("Problem reading zip file: $na.  ");
        }
    } else {
        throw new Hk_Exception_Runtime("Problem opening zip file.  Error code = $zip.  ");
    }

    return $content;
}

try {
    $wInit = new webInit();
} catch (Exception $exw) {
    die($exw->getMessage());
}

$dbh = $wInit->dbh;


$pageTitle = $wInit->pageTitle;
$testVersion = $wInit->testVersion;
$resultMsg = '';
$tabIndex = 0;
$resultAccumulator = '';
$ccResultMessage = '';
$holResultMessage = '';


// get session instance
$uS = Session::getInstance();

$menuMarkup = $wInit->generatePageMenu();

$config = new Config_Lite(ciCFG_FILE);


if (isset($_POST["btnSiteCnf"])) {

    addslashesextended($_POST);
    SiteConfig::saveConfig($config, $_POST);
    SiteConfig::saveSysConfig($dbh, $_POST);
}

if (isset($_FILES['patch']) && $_FILES['patch']['name'] != '') {
    $tabIndex = 1;
    $uploadfile = '..' . DS .'patch' . DS . basename($_FILES['patch']['name']);

    if (move_uploaded_file($_FILES['patch']['tmp_name'], $uploadfile)) {

        // patch system
        try {
            $resultAccumulator .= Patch::loadFiles('../', $uploadfile);
            $resultAccumulator .= Patch::loadConfigUpdates('../patch/patchSite.cfg', $config);

            $mysqli = openMysqli($dbh, $uS);

            $vquery = file_get_contents('../patch/patchSQL.sql');
            $result = multiQuery($mysqli, $vquery);

            if ($result != '') {

                $resultMsg .= $result;
            } else {

                $resultAccumulator .= Patch::updateViewsSps($mysqli, '../sql/CreateAllTables.sql', '../sql/CreateAllViews.sql', '../sql/CreateAllRoutines.sql');
            }
        } catch (Exception $ex) {
            $resultMsg = $hkex->getMessage(). "; ". $hkex->getTraceAsString();
        }

    } else {
        $resultMsg = "Problem moving uploaded file.  ";
    }
}

if (isset($_FILES['zipfile'])) {
    $tabIndex = 4;

    if ($_FILES['zipfile']['error'] > 0) {
        $resultMsg = "Error code=" . $_FILES['zipfile']['error'];
    } else {

        //$uploadfile = basename($_FILES['zipfile']['name']);
        $uploadfile = $_FILES['zipfile']['tmp_name'];

        $content = readZipFile($uploadfile);

        $lines = explode("\n", $content);
        // Remove the first line - headings
        $garbage = array_shift($lines);

        $content = '';

        $query = '';

        $indx = 0;
        $recordCounter = 0;
        $maxRecords = 10000;

        foreach ($lines as $line) {

            $fields = str_getcsv($line);

            if (count($fields) > 11) {

                $query .= "('"
                        . filter_var(trim($fields[0]), FILTER_SANITIZE_NUMBER_INT) . "','"    // Zip_Code
                        . filter_var(trim($fields[2]), FILTER_SANITIZE_STRING) . "','"
                        . filter_var(trim($fields[5]), FILTER_SANITIZE_STRING) . "','"
                        . filter_var(trim($fields[9]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) . "','"
                        . filter_var(trim($fields[10]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) . "','"
                        . filter_var(trim(substr($fields[1], 0, 2)), FILTER_SANITIZE_STRING) . "','"
                        . filter_var(trim($fields[3]), FILTER_SANITIZE_STRING)
                        . "'),";
                $indx++;
                $recordCounter++;
            }

            if ($indx > $maxRecords) {
                $indx = 0;
                if ($query != "") {

                    $dbh->exec("insert into postal_codes values " . substr($query, 0, -1));
                }
                $query = '';
            }
        }


        // Insert the remaining records.
        if ($indx > 0 && $query != "") {

            $dbh->exec("insert into postal_codes values " . substr($query, 0, -1));
        }

        $resultMsg = "Success, " . $recordCounter . " zip codes loaded.";
    }
}

if (isset($_POST['btnDelBak'])) {
    $tabIndex = 1;
    Patch::deleteBakFiles('../');
}

if (isset($_POST['btnSaveSQL'])) {

    $tabIndex = 1;

    $mysqli = openMysqli($dbh, $uS);
    $resultAccumulator = Patch::updateViewsSps($mysqli, '../sql/CreateAllTables.sql', '../sql/CreateAllViews.sql', '../sql/CreateAllRoutines.sql');

}

if (isset($_POST['btnPay'])) {
    $tabIndex = 2;
    $ccResultMessage = SiteConfig::savePaymentCredentials($dbh, $_POST);
}

try {
    $payments = SiteConfig::createPaymentCredentialsMarkup($dbh, $ccResultMessage);
} catch (PDOException $pex) {

}


$conf = SiteConfig::createMarkup($dbh, $config);



$webAlert = new alertMessage("webContainer");
$webAlert->set_DisplayAttr("none");
$webAlert->set_Context(alertMessage::Success);
$webAlert->set_iconId("webIcon");
$webAlert->set_styleId("webResponse");
$webAlert->set_txtSpanId("webMessage");
$webAlert->set_Text("oh-oh");

$getWebReplyMessage = $webAlert->createMarkup();
?>
<!DOCTYPE html>
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
            $(document).ready(function() {
                var tabIndex = '<?php echo $tabIndex; ?>';
                var tbs = $('#tabs').tabs();
                tbs.tabs("option", "active", tabIndex);
                $('#tabs').show();
            });
        </script>
    </head>
    <body <?php if ($testVersion) echo "class='testbody'"; ?>>
<?php echo $menuMarkup; ?>
        <div id="contentDiv">
            <h1><?php echo $wInit->pageHeading; ?></h1>
        <?php echo $getWebReplyMessage; ?>
        <div id="tabs" class="hhk-member-detail" style="display:none;">
                <ul>
                    <li><a href="#config">View Site Configuration</a></li>
                    <li><a href="#patch">Patch</a></li>
                    <li><a href="#pay">Credit Card Processor</a></li>
                     <li><a href="#loadZip">Load Zip Code Distance Data</a></li>
                </ul>
                <div id="config" class="ui-tabs-hide" >
                    <form method="post" name="form4" action="">
                        <?php echo $conf; ?>
                        <div style="float:right;margin-right:40px;"><input type="reset" name="btnreset" value="Reset" style="margin-right:5px;"/><input type="submit" name="btnSiteCnf" value="Save Site Configuration"/></div>
                    </form>
                </div>
                <div id="pay" class="ui-tabs-hide" >
                    <form method="post" name="form2" action="">
                        <?php echo $payments; ?>
                        <div style="float:right;margin-right:40px;"><input type="submit" name="btnPay" value="Save"/></div>
                    </form>
                </div>
                <div id="patch" class="ui-tabs-hide">
                    <div class="hhk-member-detail">
                        <!-- The data encoding type, enctype, MUST be specified as below -->
                        <form enctype="multipart/form-data" action="" method="POST" name ="formp">
                            <!-- MAX_FILE_SIZE must precede the file input field -->
                            <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
                            <!-- Name of input element determines name in $_FILES array -->
                            <p style="color:red;"><?php echo $resultMsg; ?></p>
                            <p>Select Patch File: <input name="patch" type="file" /></p><br/>

                            <div style="float:right;margin-right:40px;"><input type="submit" name='btnUlPatch' value="Upload & Execute Patch" /></div>
                        </form>

                    </div>
                    <div style='clear:both;'>
                        <form method="post" action="" name="form1">
                            <p>URL: <?php echo $uS->databaseURL; ?></p>
                            <p>Schema: <?php echo $uS->databaseName; ?></p>
                            <p>User: <?php echo $uS->databaseUName; ?></p>
                            <?php echo $resultAccumulator; ?>

                            <input type="submit" name="btnSaveSQL" id="btnSave" value="Re-Create Tables, Views and SP's" style="margin-left:100px;margin-top:20px;"/>
                            <input type="submit" name="btnDelBak" id="btnSave" value="Delete .bak Files" style="margin-left:20px;margin-top:20px;"/>
                        </form>

                    </div>

                </div>
                <div id="loadZip" class="ui-tabs-hide">
                    <form enctype="multipart/form-data" action="" method="POST" name="formz">
                        <!-- MAX_FILE_SIZE must precede the file input field -->
                        <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
                        <!-- Name of input element determines name in $_FILES array -->
                        <p style="color:red;"><?php echo $resultMsg; ?></p>
                        <p><input name="zipfile" type="file" /></p><br/>

                        <div style="float:right;margin-right:40px;"><input type="submit" value="Go" /></div>
                    </form>

                </div>
            </div>
        </div>
    </body>
</html>
