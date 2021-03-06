<?php
/**
 * VolNameEdit.php
 *
 * @category  Volunteer
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
require_once ("VolIncludes.php");

require_once (CLASSES . 'PDOdata.php');

require_once (MEMBER . 'Member.php');
require_once (MEMBER . 'IndivMember.php');
require_once (MEMBER . 'OrgMember.php');
require_once (MEMBER . "Addresses.php");

require_once (CLASSES . 'volunteer.php');
require_once (CLASSES . 'selCtrl.php');
require_once (CLASSES . 'Relation.php');
require_once (DB_TABLES . 'nameRS.php');
require_once (CLASSES . 'CleanAddress.php');
require_once (CLASSES . 'AuditLog.php');

require_once (SEC . 'UserClass.php');
require_once (CLASSES . 'UserCategories.php');
require_once(SEC . 'ChallengeGenerator.php');

$wInit = new webInit();
$dbh = $wInit->dbh;

$pageTitle = $wInit->pageTitle;

// get session instance
$uS = Session::getInstance();

$menuMarkup = $wInit->generatePageMenu();
$resourceURL = $uS->resourceURL;


$id = intval($uS->uid, 10);
if ($id < 1) {
    $wInit->logout();
}

$uname = $uS->username;


// Load the session with member - based lookups
$wInit->sessionLoadGenLkUps();



// Instantiate the alert message control
//$alertMsg = new alertMessage("divAlert1");
$resultMessage = "";



// Instantiate the member object
try {

    $name = Member::GetDesignatedMember($dbh, $id, MemBasis::Indivual);

} catch (Exception $ex) {

    $wInit->logout();

}


// the rest
try {

    $address = new Address($dbh, $name, $uS->nameLookups[GL_TableNames::AddrPurpose]);
    $phones = new Phones($dbh, $name, $uS->nameLookups[GL_TableNames::PhonePurpose]);
    $emails = new Emails($dbh, $name, $uS->nameLookups[GL_TableNames::EmailPurpose]);

} catch (Exception $ex) {
    exit("Error opening supporting objects: " . $ex->getMessage());
}



// test and handle Name Edit save
if (isset($_POST["btnSavePI"])) {

    $msg = "";

    // Strip slashes
    addslashesextended($_POST);

try {

        // Name
        $msg .= $name->saveChanges($dbh, $_POST);
        $id = $name->get_idName();

        // Address
        $msg .= $address->savePost($dbh, $_POST, $uname);

        // Phone number
        $msg .= $phones->savePost($dbh, $_POST, $uname);

        // Email Address
        $msg .= $emails->savePost($dbh, $_POST, $uname);

        if (checkHijack($uS)) {
            setHijack($dbh, $uS, "done");
            $msg .= " Name and Address verified.  Thank you.";
        }

        // success
        if ($msg != '') {
            $resultMessage = AlertControl::createMarkup('alm', AlertControl::Success, $msg, TRUE);
        }

    } catch (Exception $ex) {

//        $alertMsg->set_Context(alertMessage::Alert);
//        $alertMsg->set_Text($ex->getMessage());
        $resultMessage = AlertControl::createMarkup('alm', AlertControl::Alert, $ex->getMessage(), TRUE);
    }
}

//
// Name Edit Row
$nameMarkup = $name->createMarkupTable($dbh, 'hhk-hideStatus', 'hhk-hideBasis');


// Excludes, Demographics and admin tabs
//$miscTabs = $name->createMiscTabsMarkup($dbh);


//
// Addresses
$addrPanelMkup = $address->createMarkup('', FALSE, $uS->county);

//
// Phone Numbers
$phoneMkup = $phones->createMarkup();

//
// Email addresses
$emailMkup = $emails->createMarkup();


// Mark Preferred phone and email
$prefMkup = Addresses::getPreferredPanel($phones, $emails);


// member data object to pass to the javascript on the page
$memberData = array();
$memberData["id"] = $id;
$memberData["coId"] = $name->get_companyId();
$memberData["coName"] = $name->get_company();
$memberData["memDesig"] = $name->getMemberDesignation();
$memberData['addrPref'] = ($name->get_preferredMailAddr() == '' ? '1' : $name->get_preferredMailAddr());
$memberData['phonePref'] = $name->get_preferredPhone();
$memberData['emailPref'] = $name->get_preferredEmail();
$memberData['memName'] = $name->getMemberName();
$memberData['memStatus'] = $name->get_status();

// Hijack user to verify hte addrss
if (checkHijack($uS)) {
    $memberData['verifyAddress'] = "true";
} else {
    $memberData['verifyAddress'] = "no";
}


$memDataJSON = json_encode($memberData);




// Instantiate the alert message control
$pwMsg = AlertControl::createMarkup('pw');

$pwAlertJSON = json_encode(AlertControl::makeJsonPackage('pw'));



// instantiate a ChallengeGenerator object
try {
    $chlgen = new ChallengeGenerator();
    // register challenge variable
    //$chlgen->setChallengeVar();
    $challengeVar = $chlgen->getChallengeVar("challenge");
} catch (Exception $e) {
    die("Uh-oh.  Error.");
}


//// Squirms
//$plus5 = time() + (1 * 60 * 60);
//$squirm = date("Y/m/d H:i:s", $plus5);
//$squirm = encryptMessage($squirm);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $pageTitle; ?></title>
        <link href="<?php echo JQ_DT_CSS; ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo JQ_UI_CSS; ?>" rel="stylesheet" type="text/css" />
        <link href="css/publicStyle.css" rel="stylesheet" type="text/css" />
<?php echo TOP_NAV_CSS; ?>
        <script type="text/javascript" src="<?php echo $resourceURL; ?><?php echo JQ_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $resourceURL; ?><?php echo JQ_UI_JS; ?>"></script>
    </head>
    <body>
<?php echo $menuMarkup; ?>
        <div id="page">
            <div id="volNameEdit">
                <h2 style="padding: 2px 5px;text-align: center;" class="ui-corner-all ui-state-active">Manage My Contact Information</h2>

                    <div id="editTab" class="ui-widget ui-widget-content ui-corner-all hhk-member-detail">
<?php echo $resultMessage ?>
                        <form action="VolNameEdit.php" method="post" id="formEd" name="formEd">
                            <div class="ui-widget ui-widget-content ui-corner-all" style="font-size:0.95em; float:left; padding: 0.7em 1.0em;">
<?php echo $nameMarkup; ?>
                            </div>
                            <div style="clear:both;"></div>
                            <div id="phEmlTabs" class="hhk-member-detail">
                                <ul>
                                    <li><a href="#prefTab" title="Show only preferred phone and Email">Summary</a></li>
                                    <li><a href="#phonesTab" title="Edit the Phone Numbers and designate the preferred number">Phone</a></li>
                                    <li><a href="#emailTab" title="Edit the Email Addresses and designate the preferred address">Email</a></li>
                                </ul>
                                <div id="prefTab" >
                                <?php echo $prefMkup; ?>
                                </div>
                                <div id="phonesTab" style="display:none;" >
<?php echo $phoneMkup; ?>
                                </div>
                                <div id="emailTab" style="display:none;">
<?php echo $emailMkup; ?>
                                </div>
                            </div>
                            <div id="addrsTabs" class="hhk-member-detail">
<?php echo $addrPanelMkup; ?>
                            </div>
                            <div style="clear:both;"></div>
                            <input type="button" id="chgPW" value="Change Your Password..." /><input type="submit"  style="float:right;" value="Save" id="btnSavePI" name="btnSavePI" /><input id="btnResetAddr" type="reset" style="float:right;" value="Reset" />
                        </form>
                    </div>  <!-- end name edit tab -->

                <div id="dchgPw" style="display:none;">
                    <table><tr>
                            <td class="tdlabel">User Name:</td><td style="background-color: white;"><span id="txtUserName"><?php echo $uname; ?></span></td>
                        </tr><tr>
                            <td class="tdlabel">Enter Old Password:</td><td><input id="txtOldPw" type="password" value=""  /></td>
                        </tr><tr>
                            <td class="tdlabel">Enter New Password:</td><td><input id="txtNewPw1" type="password" value=""  /></td>
                        </tr><tr>
                            <td class="tdlabel">New Password Again:</td><td><input id="txtNewPw2" type="password" value=""  /></td>
                        </tr><tr>
                            <td colspan ="2"><span id="pwChangeErrMsg"></span></td>
                        </tr>
                    </table>
                </div>
                <div id="submit"></div>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo $resourceURL; ?>js/stateCountry.js"></script>
        <script type="text/javascript" src="<?php echo $resourceURL; ?><?php echo JQ_DT_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $resourceURL; ?>js/md5-min.js"></script>
        <script type="text/javascript"><?php include_once("js/vNameEdit.js") ?></script>
    </body>
</html>
