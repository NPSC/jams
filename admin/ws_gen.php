<?php

/**
 * ws_gen.php
 *
 * @category  member
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
require ("AdminIncludes.php");

require (CLASSES . 'PDOdata.php');
require (DB_TABLES . 'nameRS.php');
require (DB_TABLES . 'WebSecRS.php');
require (DB_TABLES . 'SsgRS.php');
require (CLASSES . 'Relation.php');
require (CLASSES . 'AuditLog.php');

require (CLASSES . 'StudentSupport/Ssg.php');
require (CLASSES . 'StudentSupport/Student.php');
require(SEC . 'UserClass.php');
require(SEC . 'ChallengeGenerator.php');
require(SEC . 'Pages.php');



//
$wInit = new webInit(WebPageCode::Service);

$dbh = $wInit->dbh;

$uS = Session::getInstance();

$maintFlag = ComponentAuthClass::is_Authorized("ws_gen_Maint");
$donationsFlag = ComponentAuthClass::is_Authorized("NameEdit_Donations");


if (isset($_REQUEST["cmd"])) {
    $c = filter_var($_REQUEST["cmd"], FILTER_SANITIZE_STRING);
}

$events = array();
try {


    switch ($c) {

        case "zipd":
            include(HOUSE . "GuestReport.php");

            if (isset($_POST["zipf"])) {
                $zipf = filter_var($_POST["zipf"], FILTER_SANITIZE_NUMBER_INT);
            }
            if (isset($_POST["zipt"])) {
                $zipt = filter_var($_POST["zipt"], FILTER_SANITIZE_NUMBER_INT);
            }

            try{
                $events['success'] = number_format(GuestReport::calcZipDistance($dbh, $zipf, $zipt), 0);
            } catch (Hk_Exception_Runtime $hex) {
                $events['error'] = "Zip code not found.  ";
            }

            break;

    case 'schzip':

        if (isset($_GET['zip'])) {
            $zip = filter_var($_GET['zip'], FILTER_SANITIZE_NUMBER_INT);
            $events = searchZip($dbh, $zip);
        }
        break;

        case "save":

            $vaddr = "";
            if (isset($_GET["vaddr"])) {
                $vaddr = filter_var(urldecode($_GET["vaddr"]), FILTER_SANITIZE_STRING);
            }

            $role = '';
            if (isset($_GET["role"])) {
                $role = filter_var(urldecode($_GET["role"]), FILTER_SANITIZE_STRING);
            }

            $id = 0;
            if (isset($_GET["uid"])) {
                $id = intval(filter_var(urldecode($_GET["uid"]), FILTER_SANITIZE_NUMBER_INT), 10);
            }

            $status = '';
            if (isset($_GET["status"])) {
                $status = filter_var(urldecode($_GET["status"]), FILTER_SANITIZE_STRING);
            }

            $fbStatus = "";
            if (isset($_GET["fbst"])) {
                $fbStatus = filter_var(urldecode($_GET["fbst"]), FILTER_SANITIZE_STRING);
            }

            $parms = array();
            if (isset($_GET["parms"])) {
                $parms = filter_var_array($_GET["parms"], FILTER_SANITIZE_STRING);
            }


            $events = saveUname($dbh, $vaddr, $role, $id, $status, $fbStatus, $uS->username, $parms, $maintFlag);

            break;

        case "gpage":

            $site = filter_var($_REQUEST["page"], FILTER_SANITIZE_STRING);

            if ($maintFlag) {
                $events = Pages::getPages($dbh, $site);
            } else {
                $events = array("error" => "Unauthorized");
            }

            break;

        case "edsite":

            $parms = $_REQUEST["parms"];

            if (($parms = filter_var_array($parms)) === false) {
                $events = array("error" => "Bad input");
            } else if (SecurityComponent::is_TheAdmin()) {
                $events = Pages::editSite($dbh, $parms);
            } else {
                $events = array("error" => "Sites Access denied");
            }

            break;

        case "edpage":
            $parms = $_REQUEST["parms"];
            if (($parms = filter_var_array($parms)) === false) {
                $events = array("error" => "Bad input");
            } else if (SecurityComponent::is_TheAdmin()) {
                $events = Pages::editPages($dbh, $parms);
            } else {
                $events = array("error" => "Pages Access denied");
            }

            break;

        case "delpage":

            $pageId = filter_var($_REQUEST["pid"], FILTER_SANITIZE_NUMBER_INT);

            if (SecurityComponent::is_TheAdmin()) {
                $events = Pages::deletePage($dbh, $pageId);
            }
            break;

        case "recent":

            $events = recentReport($dbh, $_POST["parms"], $donationsFlag);
            break;

        case "chglog" :

            $id = filter_var(urldecode($_REQUEST["uid"]), FILTER_VALIDATE_INT);

            $events = changeLog($dbh, $id, $_GET);
            break;

        case "delRel":

            $id = 0;
            $rId = 0;
            $relCode = "";
            if (isset($_POST['id'])) {
                $id = intval(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT), 10);
            }
            if (isset($_POST['rId'])) {
                $rId = intval(filter_var($_POST['rId'], FILTER_SANITIZE_NUMBER_INT), 10);
            }

            if (isset($_POST['rc'])) {
                $rc = filter_var($_POST['rc'], FILTER_SANITIZE_STRING);
            }

            $events = deleteRelationLink($dbh, $id, $rId, $rc);
            break;

        case "newRel":

            $id = 0;
            $rId = 0;
            $relCode = "";
            if (isset($_POST['id'])) {
                $id = intval(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT), 10);
            }
            if (isset($_POST['rId'])) {
                $rId = intval(filter_var($_POST['rId'], FILTER_SANITIZE_NUMBER_INT), 10);
            }

            if (isset($_POST['rc'])) {
                $rc = filter_var($_POST['rc'], FILTER_SANITIZE_STRING);
            }

            $events = newRelationLink($dbh, $id, $rId, $rc);
            break;

        case "addcareof":

            $id = 0;
            $rId = 0;
            $relCode = "";
            if (isset($_POST['id'])) {
                $id = intval(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT), 10);
            }
            if (isset($_POST['rId'])) {
                $rId = intval(filter_var($_POST['rId'], FILTER_SANITIZE_NUMBER_INT), 10);
            }

            if (isset($_POST['rc'])) {
                $rc = filter_var($_POST['rc'], FILTER_SANITIZE_STRING);
            }

            $events = changeCareOfFlag($dbh, $id, $rId, $rc, TRUE);
            break;

        case "delcareof":

            $id = 0;
            $rId = 0;
            $relCode = "";
            if (isset($_POST['id'])) {
                $id = intval(filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT), 10);
            }
            if (isset($_POST['rId'])) {
                $rId = intval(filter_var($_POST['rId'], FILTER_SANITIZE_NUMBER_INT), 10);
            }

            if (isset($_POST['rc'])) {
                $rc = filter_var($_POST['rc'], FILTER_SANITIZE_STRING);
            }

            $events = changeCareOfFlag($dbh, $id, $rId, $rc, FALSE);
            break;

        case "adchgpw":
            $adPw = '';
            $newPw = '';
            $uid = 0;

            if (isset($_POST["adpw"])) {
                $adPw = filter_var($_POST["adpw"], FILTER_SANITIZE_STRING);
            }
            if (isset($_POST["newer"])) {
                $newPw = filter_var($_POST["newer"], FILTER_SANITIZE_STRING);
            }

            if (isset($_POST['uid'])) {
                $uid = intval(filter_var($_POST['uid'], FILTER_SANITIZE_NUMBER_INT), 10);
            }
            $events = adminChangePW($dbh, $adPw, $newPw, $uid);

            break;

        default:
            $events = array("error" => "Bad Command");
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


function searchZip(PDO $dbh, $zip) {

    $query = "select * from postal_codes where Zip_Code like :zip LIMIT 10";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array(':zip'=>$zip . "%"));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $events = array();
    foreach ($rows as $r) {
        $ent = array();

        $ent['id'] = $r['Zip_Code'];
        $ent['value'] = $r['City'] . ', ' . $r['State'] . ', ' . $r['Zip_Code'];
        $ent['City'] = $r['City'];
        $ent['State'] = $r['State'];

        $events[] = $ent;
    }

    return $events;
}


function adminChangePW(PDO $dbh, $adminPw, $newPw, $wUserId) {

    $event = array();

    if (SecurityComponent::is_Admin()) {

        if (UserClass::updateDbPassword($dbh, $wUserId, $adminPw, $newPw) === TRUE) {
            $event = array('success' => 'Password updated.');
        } else {
            $event = array('error' => 'Password is unchanged.');
        }
    } else {
        $event = array('error' => 'Insufficient authorization.  Password is unchanged.');
    }

    return $event;
}

function changeCareOfFlag(PDO $dbh, $id, $rId, $relCode, $flag) {

    $rel = Relation::instantiateRelation($dbh, $relCode, $id);

    if (is_null($rel) === FALSE) {
        $uS = Session::getInstance();
        $msh = $rel->setCareOf($dbh, $rId, $flag, $uS->username);

        $rel = Relation::instantiateRelation($dbh, $relCode, $id);

        return array('success' => $msh, 'rc' => $relCode, 'markup' => $rel->createMarkup());
    }
    return array('error' => 'Relationship is Undefined.');
}

function deleteRelationLink(PDO $dbh, $id, $rId, $relCode) {

    $rel = Relation::instantiateRelation($dbh, $relCode, $id);

    if (is_null($rel) === FALSE) {

        $msh = $rel->removeRelationship($dbh, $rId);

        $rel = Relation::instantiateRelation($dbh, $relCode, $id);

        return array('success' => $msh, 'rc' => $relCode, 'markup' => $rel->createMarkup());
    }
    return array('error' => 'Relationship is Undefined.');
}

function newRelationLink(PDO $dbh, $id, $rId, $relCode) {

    $uS = Session::getInstance();

    $rel = Relation::instantiateRelation($dbh, $relCode, $id);

    if (is_null($rel) === FALSE) {
        $msh = $rel->addRelationship($dbh, $rId, $uS->username);

        $rel = Relation::instantiateRelation($dbh, $relCode, $id);
        return array('success' => $msh, 'rc' => $relCode, 'markup' => $rel->createMarkup());
    }

    return array('error' => 'Relationship is Undefined.');
}

function changeLog(PDO $dbh, $id, $get) {

    require(CLASSES . 'DataTableServer.php');

    $aColumns = array("LogDate", "LogType", "Subtype", "User", "idName", "LogText");
    $sIndexColumn = "";
    $sTable = "vaudit_log";

    // filter by Id ...
    if ($id > 0) {
        $get["bSearchable_4"] = "true";
        $get["sSearch_4"] = $id;
    }
    $log = DataTableServer::createOutput($dbh, $aColumns, $sIndexColumn, $sTable, $get);

    // format the date column
    for ($i = 0; $i < count($log['aaData']); $i++) {

        $log['aaData'][$i]["LogDate"] = date("c", strtotime($log['aaData'][$i]["LogDate"]));
    }

    return $log;
}

function recentReport(PDO $dbh, $parms, $donationsFlag) {

    // exit on bad dates
    if (isset($parms["sdate"]) === FALSE || $parms["sdate"] == "") {
        return array("success" => "Fill in Start Date");
    }

    $dStart = date("Y-m-d", strtotime(filter_var($parms["sdate"], FILTER_SANITIZE_STRING)));

    if (isset($parms["edate"]) && $parms["edate"] != '') {
        $dEnd = date("Y-m-d 23:59:59", strtotime(filter_var($parms["edate"], FILTER_SANITIZE_STRING)));
    } else {
        $dEnd = date("Y-m-d 23:59:59");
    }

    $incNew = false;
    $incUpd = false;
    $sParms = array();

    if (isset($parms["incnew"])) {
        $incNew = filter_var($parms["incnew"], FILTER_VALIDATE_BOOLEAN);
    }

    if (isset($parms["incupd"])) {
        $incUpd = filter_var($parms["incupd"], FILTER_VALIDATE_BOOLEAN);
    }

    // exit if neither is checked
    if (!$incUpd && !$incNew) {
        return array("success" => "Check 'Include Updates to Existing Members' or 'New'");
    }

//    $members = array();
//    $member = array();
    $newWClause = "";
    $uptWClause = "";

    // set up where clauses
    if ($incNew) {
        if ($dStart != "") {
            $newWClause = " (`Created On` >= :dstart ";
            $sParms[':dstart'] = $dStart;
            if ($dEnd != "") {
                $newWClause .= " and `Created On` <= :dEnd) ";
                $sParms[':dEnd'] = $dEnd;
            } else {
                $newWClause .= ") ";
            }
        }
    }

    if ($incUpd) {
        if ($dStart != "") {
            $uptWClause = " (Last_Updated >= :upstart ";
            $sParms[':upstart'] = $dStart;
            if ($dEnd != "") {
                $uptWClause .= " and Last_Updated <= :upEnd) ";
                $sParms[':upEnd'] = $dEnd;
            } else {
                $uptWClause .= ") ";
            }
        }
    }

    // Combine the where clauses
    if ($newWClause != "" && $uptWClause != "") {
        $whereClause = " (" . $newWClause . " or " . $uptWClause . ") ";
    } else {
        // one of these is empty, or both
        $whereClause = $newWClause . $uptWClause;
    }

    // Exit if no where clause?
    if ($whereClause == "") {
        return array("success" => "Check one of Categories");
    }


    // Create an array to store data in order to orient data to names instead of tables.
    $tableNames = array("[name]", "[address]", "[phone]", "[email]", "[volunteer]", "[web]", "[calendar]", "[donations]");
    $controlNames = array("cbname", "cbaddr", "cbphone", "cbemail", "cbvol", "cbweb", "cbevents", "cbdonations");
    $tables = array("vdump_name", "vdump_address", "vdump_phone", "vdump_email", "vdump_volunteer", "vdump_webuser", "vdump_events", "vdump_donations");
    $names = array();

    // run through checkboxes
    for ($i = 0; $i < count($tableNames); $i++) {

        if (isset($parms[$controlNames[$i]]) && filter_var($parms[$controlNames[$i]], FILTER_VALIDATE_BOOLEAN) === TRUE) {
            if ($tableNames[$i] == "[donations]" && !$donationsFlag) {
                continue;
            }
            $query = "select * from " . $tables[$i] . " where $whereClause;";
            $stmt = $dbh->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute($sParms);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $names = makeTable($rows, $tableNames[$i]);
        }
    }

    $markup = createRecentReportMU($dbh, $names);
    return array("success" => $markup);
}

function makeTable($rows, $tableName) {

    $names = array();

    foreach ($rows as $rw) {

        $names[$rw["Id"]][$tableName][] = $rw;
    }
    return $names;
}

function createRecentReportMU(PDO $dbh, $names) {
    $markup = "";

    // array have data?
    if (empty($names)) {
        return "No Data";
    }

    // header
    $markup .= "<p>" . count($names) . " Members Listed</p><br/>";

    foreach ($names as $id => $data) {
        // get member name
        $stmt = $dbh->prepare("select case when Record_Member = 1 then concat(Name_First,' ',Name_Last)
                else Company end as `name` from name where idName = :id;");
        $stmt->execute(array(':id' => $id));
//        $res = queryDB($dbcon, "select case when Record_Member = 1 then concat(Name_First,' ',Name_Last)
//                else Company end as `name` from name where idName = $id;");
        $rows = $stmt->fetchAll(PDO::FETCH_NUM);
        $nameStr = "";
        if (count($rows) > 0) {
            $nameStr = $rows[0][0];
        }

        // member id and name
        $markup .= "<a style='text-decoration:none;' href='NameEdit.php?id=$id'>$id: $nameStr</a><div class='hhk-recent'>";


        foreach ($data as $tname => $rows) {
            $numcols = count($rows[0]);

            $markup .= "<table>";
            $markup .= "<tr><td colspan='$numcols'><span class='hhk-recent-tablenames'>$tname</span></td></tr>";

            // make the column titles
            $markup .= "<tr>";
            foreach ($rows[0] as $title => $val) {
                if ($val != "") {
                    $markup .= "<th>" . $title . "</td>";
                } else {
                    $markup .= "<th></td>";
                }
            }

            $markup .= "</tr><tr>";
            foreach ($rows as $row) {

                $markup .= "<tr>";
                foreach ($row as $title => $val) {
                    $markup .= "<td>" . $val . "</td>";
                }
                $markup .= "</tr>";
            }
            $markup .= "</table>";
        }
        $markup .= "</div>";
    }

    return $markup;
}

function saveUname(PDO $dbh, $vaddr, $role, $id, $status, $fbStatus, $admin, $parms, $maintFlag) {

    $reply = array();

    // fbx table
    $stmt = $dbh->query("select * from fbx where idName=$id;");
    $fbxRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($fbxRows) == 1) {

        if (strtolower($fbxRows[0]["Status"]) != $fbStatus) {

            $stmt = $dbh->prepare("update fbx set Approved_Date=now(), Approved_By=:admin, Status=:stat where idName = $id;");
            $stmt->execute(array(':admin' => $admin, ':stat' => $fbStatus));
        }
    }
    // else we dont care.
    // w_users table
    $usersRS = new W_usersRS();
    $usersRS->idName->setStoredVal($id);
    $userRows = EditRS::select($dbh, $usersRS, array($usersRS->idName));

    if (count($userRows) == 1) {
        EditRS::loadRow($userRows[0], $usersRS);
        // update existing entry

        $usersRS->Status->setNewVal($status);
        $usersRS->Verify_Address->setNewVal($vaddr);
        $usersRS->Last_Updated->setNewVal(date('Y-m-d H:i:s'));
        $usersRS->Updated_By->setNewVal($admin);

        $n = EditRS::update($dbh, $usersRS, array($usersRS->idName));

        if ($n == 1) {

            NameLog::writeUpdate($dbh, $usersRS, $id, $admin);
            $reply[] = array("success" => "Update web users.  ");
        }
    } else {
        $reply[] = array("error", "Record not found");
    }



    if ($maintFlag) {

        // update w_auth table with new role
        $authRS = new W_authRS();
        $authRS->idName->setStoredVal($id);
        $authRows = EditRS::select($dbh, $authRS, array($authRS->idName));

        if (count($authRows) == 1) {
            // update existing entry
            EditRS::loadRow($authRows[0], $authRS);

            $authRS->Role_Id->setNewVal($role);

            $authRS->Last_Updated->setNewVal(date('Y-m-d H:i:s'));
            $authRS->Updated_By->setNewVal($admin);

            $n = EditRS::update($dbh, $authRS, array($authRS->idName));

            if ($n == 1) {

                NameLog::writeUpdate($dbh, $authRS, $id, $admin);
                $reply[] = array("success" => "Update web authorization.  ");
            }
        } else {
            $reply[] = array("error", "Record not found");
        }


        // Group Code security table
        //$sArray = readGenLookups($dbh, "Group_Code");
        $stmt = $dbh->query("select Group_Code as Code, Description from w_groups");
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($groups as $g) {
            $sArray[$g['Code']] = $g;
        }



        $secRS = new Id_SecurityGroupRS();
        $secRS->idName->setStoredVal($id);
        $rows = EditRS::select($dbh, $secRS, array($secRS->idName));

        foreach ($rows as $r) {
            $sArray[$r['Group_Code']]["exist"] = "t";
        }

        foreach ($sArray as $g) {

            if (isset($parms["grpSec_" . $g["Code"]])) {

                if (!isset($g["exist"]) && $parms["grpSec_" . $g["Code"]] == "checked") {

                    // new group code to put into the database
                    $secRS = new Id_SecurityGroupRS();
                    $secRS->idName->setNewVal($id);
                    $secRS->Group_Code->setNewVal($g["Code"]);
                    $n = EditRS::insert($dbh, $secRS);

                    NameLog::writeInsert($dbh, $secRS, $id, $admin);

                } else if (isset($g["exist"]) && $parms["grpSec_" . $g["Code"]] != "checked") {

                    // group code to delete from the database.
                    $secRS = new Id_SecurityGroupRS();
                    $secRS->idName->setStoredVal($id);
                    $secRS->Group_Code->setStoredVal($g["Code"]);
                    $n = EditRS::delete($dbh, $secRS, array($secRS->idName, $secRS->Group_Code));

                    if ($n == 1) {
                        NameLog::writeDelete($dbh, $secRS, $id, $admin);
                    }
                }
            }
        }
    }
    return $reply;
}


