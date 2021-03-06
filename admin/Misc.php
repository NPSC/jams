<?php
/**
 * Misc.php
 *
 * @category  Configuraton
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

require_once ("AdminIncludes.php");


try {
    $wInit = new webInit();
} catch (Exception $exw) {
    die($exw->getMessage());
}

$dbh = $wInit->dbh;
$dbcon = initDB();


$pageTitle = $wInit->pageTitle;
$testVersion = $wInit->testVersion;
// get session instance
$uS = Session::getInstance();

$menuMarkup = $wInit->generatePageMenu();

$config = new Config_Lite(ciCFG_FILE);
$uname = $uS->username;

addslashesextended($_POST);

function getGenLookups(PDO $dbh) {
    $stmt = $dbh->query("select distinct Table_Name from gen_lookups;");
    $rows = $stmt->fetchAll(PDO::FETCH_NUM);

    $markup = "<option value=''>Select</option>";

    foreach ($rows as $rw) {
        if ($rw[0] != "") {
            $markup .= "<option value='" . $rw[0] . "'>".$rw[0] . "</option>";
        }
    }
    return $markup;
}

function getChangeLog($dbcon, $naIndex, $stDate = "", $endDate = "") {

    // sanity test for how much data you want
    if ($stDate == "" && $endDate == "" && $naIndex < 1) {
        return "Set a Start date. ";
    }

    $logDates = "";
    $whDates = "";
    $whereName = "";

    if ($stDate != "") {
        $logDates = " and Date_Time >= '$stDate' ";
        $whDates = " and a.Effective_Date >= '$stDate' ";
    }

    if ($endDate != "") {
        $logDates .= " and Date_Time <= '$endDate' ";
        $whDates .= " and a.Effective_Date <= '$endDate' ";
    }

    if ($naIndex == 0) {
        $whereName = "";
    } else {
        $whereName = " and idName = " . $naIndex;
    }


    $query = "SELECT * FROM name_log WHERE 1=1 " . $whereName . $logDates . " order by Date_Time desc limit 100;";

    $result2 = queryDB($dbcon, $query, true);

    $data = "<table id='dataTbl' class='display'><thead><tr>
            <th>Date</th>
            <th>Type</th>
            <th>Sub-Type</th>
            <th>User Id</th>
            <th>Member Id</th>
            <th>Log Text</th></tr></thead><tbody>";

    while ($row2 = mysqli_fetch_array($result2)) {

        $data .= "<tr>
                <td>" . date("Y-m-d H:i:s", strtotime($row2['Timestamp'])) . "</td>
                <td>" . $row2['Log_Type'] . "</td>
                <td>" . $row2['Sub_Type'] . "</td>
                <td>" . $row2['WP_User_Id'] . "</td>
                <td>" . $row2['idName'] . "</td>
                <td>" . $row2['Log_Text'] . "</td></tr>";
    }
    mysqli_free_result($result2);

    // activity table has volunteer data
    $query = "select a.idName, a.Effective_Date, a.Action_Codes, a.Other_Code, a.Source_Code, g.Description as Code, g2.Description as Category, ifnull(g3.Description, '') as Rank
from activity a left join gen_lookups g on substring_index(Product_Code, '|', 1) = g.Table_Name and  substring_index(Product_Code, '|', -1) = g.Code
left join gen_lookups g2 on g2.Table_Name = 'Vol_Category' and substring_index(Product_Code, '|', 1) = g2.Code
left join gen_lookups g3 on g3.Table_Name = 'Vol_Rank' and g3.Code = a.Other_Code
        where a.Type = 'vol' $whereName $whDates order by a.Effective_Date desc limit 100;";

    $result2 = queryDB($dbcon, $query, true);
    while ($row2 = mysqli_fetch_array($result2)) {

        $data .= "<tr>
                <td>" . date("Y-m-d H:i:s", strtotime($row2['Effective_Date'])) . "</td>
                <td>Volunteer</td>
                <td>" . $row2['Action_Codes'] . "</td>
                <td>" . $row2['Source_Code'] . "</td>
                <td>" . $row2['idName'] . "</td>
                <td>" . $row2['Category'] . "/" . $row2["Code"] . ", Rank = " . $row2["Rank"] . "</td></tr>";
    }
    mysqli_free_result($result2);


    return $data . "</tbody></table>";
}

//
// catch service calls
if (isset($_POST["table"])) {

    $tableName = substr(filter_var($_POST["table"], FILTER_SANITIZE_STRING), 0, 45);

    $res = queryDB($dbcon, "Select Code, Description, Substitute from gen_lookups where Table_Name='" . $tableName . "'");
    $code = array(
        "Code" => "",
        "Description" => "",
        "Substitute" => ""
    );

    $tabl = array();
    while ($rw = mysqli_fetch_row($res)) {

        $code["Code"] = $rw[0];
        $code["Description"] = $rw[1];
        $code["Substitute"] = $rw[2];
        $tabl[] = $code;
    }

    echo( json_encode($tabl));
    exit();
}

if (isset($_POST["cmd"])) {
    if ($_POST["cmd"] == "move") {


        $list = arrayify(filter_var_array($_POST["list"]));
        $query = "";

        foreach ($list as $item) {
            //if (ValidateNameId::isValidId($dbcon, $item["donToId"])) {
            if ($item["donToId"] > 0) {
                $query .= " call sp_move_donation (" . $item["donToId"] . ", " . $item["delId"] . ", '$uname'); ";
            }
        }

        if ($query != "") {
            if (mysqli_multi_query($dbcon, $query)) {
                do {
                    if (($res = mysqli_store_result($dbcon))) {
                        while ($rw = mysqli_fetch_assoc($res)) {

                        }
                        mysqli_free_result($res);
                    } else {
                        // db problem
//                        $rtrn = array("error"=>"Database Error: row retrieval error");
//                        echo( json_encode($rtrn));
//                        exit();
                    }
                } while (mysqli_next_result($dbcon));

                $rtrn = array("success" => "ok");
                echo( json_encode($rtrn));
                exit();
            } else {
                // db problem
                $rtrn = array("error" => "Database Error: query failure");
                echo( json_encode($rtrn));
                exit();
            }
        } else {
            $rtrn = array("success" => "But nothing was updated");
            echo( json_encode($rtrn));
            exit();
        }
    }
    $rtrn = array("error" => "bad command");
    echo( json_encode($rtrn));
    exit();
}
// End of service calls
//
//
//
//$menuMarkup = $page->generateMenu($dbcon, $uS, $testHeader);

$lookupErrMsg = "";

// Maintain the accordian index accross posts
$accordIndex = 0;
$cookieReply = '';

if (isset($_COOKIE['housepc'])) {
    $cookieReply = "Access is set on this PC" . $_COOKIE['housepc']['value'];
}

if (isset($_POST['setCookie'])) {
    $accordIndex = 6;
    $sites = $uS->siteList;

    $cookVal = encryptMessage($_SERVER['REMOTE_ADDR'] . 'eric');

    if (SecurityComponent::is_TheAdmin()) {
        if ( setcookie('volpc', $cookVal, time()+60*60*24*370, $sites[WebSiteCode::Volunteer]['Relative_Address'], $sites[WebSiteCode::Volunteer]['HTTP_Host']) ) {
            $cookieReply = "Access Set.  PC should work now.";
        }
        setcookie('housepc', $cookVal, time()+60*60*24*370, $sites[WebSiteCode::House]['Relative_Address'], $sites[WebSiteCode::House]['HTTP_Host']);
    } else {
        $cookieReply = "Must be logged in as THE admin to set access.";
    }

} else if (isset($_POST['removeCookie'])) {
    $accordIndex = 6;
    $sites = $uS->siteList;

    if ( setcookie('housepc', "", time() - 3600, $sites['v']['Relative_Address'], $sites['v']['HTTP_Host']) ) {
        $cookieReply = "Access Deleted.";
    }

}

function getFieldLengths($con, $table) {
    // get the field lengths
    $query = "select * from $table limit 1";
    $colLength = array();
    $res = queryDB($con, $query);
    if (!is_array($res)) {
        $finfo = mysqli_fetch_fields($res);
        foreach ($finfo as $field) {
           $colLength[$field->name] = $field->length;
        }
    }
    return $colLength;
}


// Check for Gen_Lookups post
if (isset($_POST["btnGenLookups"])) {
    $accordIndex = 0;
    $lookUpAlert = new alertMessage("lookUpAlert");
    $lookUpAlert->set_Context(alertMessage::Alert);
    $flen = getFieldLengths($dbcon, "gen_lookups");

    $code = filter_var($_POST["txtCode"], FILTER_SANITIZE_STRING);
    //$code = substr($code, 0, $flen["Code"]);
    $desc = filter_var($_POST["txtDesc"], FILTER_SANITIZE_STRING);
    //$desc = substr($desc, 0, $flen["Description"]);
    $subt = filter_var($_POST["txtAddl"], FILTER_SANITIZE_STRING);
    //$subt = substr($subt, 0, $flen["Substitute"]);
    $selTbl = filter_var($_POST["selLookup"], FILTER_SANITIZE_STRING);
    //$selTbl = substr($selTbl, 0, $flen["Table_Name"]);
    $selCode = filter_var($_POST["selCode"], FILTER_SANITIZE_STRING);
    $selCode = substr($selCode, 0, $flen["Code"]);

    if ($selTbl == "") {
        $lookUpAlert->set_Text("The Table_Name must be filled in");
    } else if (strlen($selTbl) > $flen["Table_Name"]) {
        $lookUpAlert->set_Text("The Table_Name too long: " . $selTbl);
    } else if ($code == "") {
        $lookUpAlert->set_Text("The Code must be filled in");
    } else if (strlen($code) > $flen["Code"]) {
        $lookUpAlert->set_Text("The Code is too long: " . $code);
    } else if (strlen($subt > $flen["Substitute"])) {
        $lookUpAlert->set_Text("The Additional Text is too long: " . $subt);
    } else if ($desc != "" && strlen($desc) < $flen["Description"]) {

        // Is the table_name there?
        $query = "select count(*) from gen_lookups where Table_Name='" . $selTbl . "';";
        $res = queryDB($dbcon, $query);
        $row = mysqli_fetch_row($res);

        if ($row[0] == 0) {
            $lookUpAlert->set_Text("That Table_Name does not exist.");
        } else {

            // Is the Code there?
            $query = "select count(*) from gen_lookups where Table_Name='" . $selTbl . "' and Code='" . $code . "';";
            $res1 = queryDB($dbcon, $query);
            $row = mysqli_fetch_row($res1);

            $query = "";
            if ($row[0] == 0 && $selCode == "n_$") {
                // add a new code with desc.
                $query = "insert into gen_lookups (Table_Name, Code, Description, Substitute) values ('" . $selTbl . "', '" . $code . "', '" . $desc . "', '" . $subt . "');";
            } else if ($row[0] > 0 && $selCode != "n_$") {
                // just update the description
                $query = "update gen_lookups set Description='" . $desc . "', Substitute='" . $subt . "' where Table_Name='" . $selTbl . "' and Code='" . $code . "';";
            } else {
                $lookUpAlert->set_Text("sorry, don't understand (been a long day)");
            }

            if ($query != "") {
                queryDB($dbcon, $query);
                $lookUpAlert->set_Context(alertMessage::Success);
                $lookUpAlert->set_Text("Okay");
            }
        }
    }
    $lookupErrMsg = $lookUpAlert->createMarkup();
}

/*
 * Change Log Output
 */
$markup = "";
if (isset($_POST["btnGenLog"])) {
    $accordIndex = 2;
    $sDate = filter_var($_POST["sdate"], FILTER_SANITIZE_STRING);
    if ($sDate != '') {
        $sDate = date("Y-m-d", strtotime($sDate));
    }
    $eDate = filter_var($_POST["edate"], FILTER_SANITIZE_STRING);
    if ($eDate != '') {
        $eDate = date("Y-m-d 23:59:59", strtotime($eDate));
    }

    $markup = getChangeLog($dbcon, 0, $sDate, $eDate);

}

$cleanMsg = '';
if (isset($_POST['btnClnPhone'])) {
    // Clean up phone numbers
    $accordIndex = 1;
    $stmt = $dbh->query("select idName, Phone_Code, Phone_Num from name_phone where Phone_Num <> '';");
    $n = 0;

    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $new = preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4}).*~', '($1) $2-$3', $r['Phone_Num']);

        $srch = str_replace('(', '', str_replace(')', '', str_replace('-', '', str_replace(' ', '', $new))));

        $n += $dbh->exec("update name_phone set Phone_Num = '$new', Phone_Search = '$srch' where idName = " . $r['idName'] . " and Phone_Code='".$r['Phone_Code']."'");
    }

    $cleanMsg = $n . " phone records cleaned.";
}


$to = $config->get("backup", "BackupEmailAddr");

$bkupMsg = "";
if (isset($_POST["btnDoBackup"])) {
    $accordIndex = 2;
    $bkupAlert = new alertMessage("bkupAlert");
    $bkupAlert->set_Context(alertMessage::Alert);

    /* CONFIGURE THE FOLLOWING SEVEN VARIABLES TO MATCH YOUR SETUP */
    $dbuser = $config->get("backup", "BackupUser");            // Database username
    $dbpwd = decryptMessage($config->get("backup", "BackupPassword"));     // Database password
    $dbname = $uS->databaseName;             // Database name. Use --all-databases if you have more than one
    $dbUrl = $config->get("db", "URL", "");
    $datestamp = date("Y_m_d");      // Current date to append to filename of backup file in format of YYYY-MM-DD
    $filePath = $config->get("backup", "BackupFilePath", "/");

    $filename = $filePath . DS . $datestamp . "_" . $dbname . ".sql.gz";   // The name (and optionally path) of the dump file

    $command = "mysqldump  --host=$dbUrl --opt -u $dbuser --password=$dbpwd $dbname | gzip > $filename";
    passthru($command);

    $from = $config["vol_email"]["ReturnAddress"];      // Email address message will show as coming from.
    $subject = $config["site"]["Site_Name"] . " DB backup file";      // Subject of email


    if (file_exists($filename)) {
        $attachmentname = array_pop(explode("/", $filename));   // If a path was included, strip it out for the attachment name

        $message = "Compressed database backup file $attachmentname attached.";
        $mime_boundary = "<<<:" . md5(time());
        $data = chunk_split(base64_encode(implode("", file($filename))));

        $headers = "From: $from\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: multipart/mixed;\r\n";
        $headers .= " boundary=\"" . $mime_boundary . "\"\r\n";

        $content = "This is a multi-part message in MIME format.\r\n\r\n";
        $content.= "--" . $mime_boundary . "\r\n";
        $content.= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
        $content.= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $content.= $message . "\r\n";
        $content.= "--" . $mime_boundary . "\r\n";
        $content.= "Content-Disposition: attachment;\r\n";
        $content.= "Content-Type: Application/Octet-Stream; name=\"$attachmentname\"\r\n";
        $content.= "Content-Transfer-Encoding: base64\r\n\r\n";
        $content.= $data . "\r\n";
        $content.= "--" . $mime_boundary . "\r\n";

        if (mail($to, $subject, $content, $headers) === false) {
            $bkupAlert->set_Text("Email send error - Message not sent");
        } else {
            $bkupAlert->set_Context(alertMessage::Success);
            $bkupAlert->set_Text("Ok - Email sent.");
        }

        //unlink($filename);
    } else {
        $bkupAlert->set_Text("Error - The Backup File did not get created. Message not sent.");
    }

    $bkupMsg = $bkupAlert->createMarkup();
}

/*
 *  Delete Name records.
 */
$delIdListing = "";

$query = "select idName from name where name.Member_Status = 'u' || name.Member_Status = 'TBD';";
$res = queryDB($dbcon, $query);
if (!is_array($res)) {
    while ($r = mysqli_fetch_row($res)) {
        $delIdListing .= "<a href='NameEdit.php?id=" . $r[0] . "'>" . $r[0] . "</a> ";
    }
    mysqli_free_result($res);
}
if ($delIdListing == "") {
    $delIdListing = "No records.";
}

$ids = "";
$total = 0;
$numStays = 0;
$stayIds = '';
$donMoveNames = "";

// Check for existing donation records
$query = "select d.Donor_Id, sum(d.Amount), n.Name_Last_First from donations d left join name n on d.Donor_Id = n.idName where d.Status='a' and (n.Member_Status = 'u' or n.Member_Status = 'TBD') group by d.Donor_Id;";
$res = queryDB($dbcon, $query);

if (!is_array($res)) {
    while ($r = mysqli_fetch_row($res)) {
        $donMoveNames .= "<tr><td>($r[0]) $r[2]</td><td class='tdBox'><input type='text' id='t_$r[0]' name='$r[0]' size='5' class='srchChars' title='Enter at least 3 characters to invoke search' />
          <select id='s_$r[0]' name='$r[0]' class='Selector'><option value='0'></option></select></td></tr>";
        $ids .= $r[0] . ",  ";
        $total += $r[1];
    }
    mysqli_free_result($res);
}


$delNamesMsg = "";
if (isset($_POST["btnDelDups"])) {
    $delDupsAlert = new alertMessage("delDupsAlert");
    $accordIndex = 4;

    // check for damage...
    if ($total > 0) {
        $delDupsAlert->set_Context(alertMessage::Alert);
        $delDupsAlert->set_Text("Donations Exist!  Names not deleted.  Id's with existing donations are: " . $ids . "  For a total amount of $" . $total);
    } else if ($numStays > 0) {
        $delDupsAlert->set_Context(alertMessage::Alert);
        $delDupsAlert->set_Text("Visits exist! Names not deleted. Ids with existing stays are: " . $stayIds);

    } else {

        // delete the name and associated records.
        $query = "call delete_names_u_tbd;";
        $res = queryDB($dbcon, $query);

        if ($res === false) {
            $delDupsAlert->set_Context(alertMessage::Alert);
            $delDupsAlert->set_Text("Database Error.");
        } else {

            //$numRows = mysqli_affected_rows($res);
            $delDupsAlert->set_Context(alertMessage::Success);
            $delDupsAlert->set_Text("Oday.  Uh-oh, I must have a dold.");
        }
    }
    $delNamesMsg = $delDupsAlert->createMarkup();
}

/*
 *  List Errors
 */
$contents = "";
if (isset($_POST["btnAdminErrors"]) || isset($_POST['btnHouseErrors']) || isset($_POST["btnVolErrors"])) {
    $accordIndex = 5;
    $fname = '';
    $logname = '';

    if (isset($_POST["btnAdminErrors"])) {
        $fname = "error_log";
        $logname = "Admin";
    } else if (isset($_POST['btnHouseErrors'])) {
        $fname = "../house/error_log";
        $logname = "Guest Tracking";
    } else if (isset($_POST["btnVolErrors"])) {
        $fname = "../volunteer/error_log";
        $logname = "Volunteer";
    }

    if (file_exists($fname)) {
        if ($_REQUEST['mode'] == 'del') {
            unlink($fname);
        } else {
            //$contents = "<div style='margin-bottom: 1em;'><a href='?mode=del'>Erase $logname error_log</a></div>" . nl2br(file_get_contents($fname));
            $contents = nl2br(file_get_contents($fname));
        }
    } else {
        $contents = $logname . ' Error Log is Empty.';
    }
}


$webAlert = new alertMessage("webContainer");
$webAlert->set_DisplayAttr("none");
$webAlert->set_Context(alertMessage::Success);
$webAlert->set_iconId("webIcon");
$webAlert->set_styleId("webResponse");
$webAlert->set_txtSpanId("webMessage");
$webAlert->set_Text("oh-oh");

$getWebReplyMessage = $webAlert->createMarkup();


$selLookups = getGenLookups($dbh);

if ($dbcon != null)
    closeDB($dbcon);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
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
        <script type="text/javascript" src="<?php echo $wInit->resourceURL; ?><?php echo JQ_DT_JS; ?>"></script>
        <script type="text/javascript">
            var table, accordIndex;
            $(document).ready(function() {
                table = new Object();
                accordIndex = <?php echo $accordIndex; ?>;
                $.ajaxSetup ({
                    beforeSend: function() {
                        //$('#loader').show()
                        $('body').css('cursor', "wait");
                    },
                    complete: function(){
                        $('body').css('cursor', "auto");
                        //$('#loader').hide()
                    },
                    cache: false
                });
                $('#accordion').tabs();
                $( '#accordion' ).tabs("option", "active", accordIndex);
                if (accordIndex == 3){
                    $('#dataTbl').dataTable({
                        "iDisplayLength": 50,
                        "aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
                        "aoColumnDefs": [
                            { "sWidth": "121px", "aTargets": [ 0 ] }
                        ]
                        , "sDom": '<"top"ilfp>rt<"bottom"p>'
                    });
                }
                $('#divMoveDon').dialog({
                    autoOpen: false,
                    width: 550,
                    resizable: true,
                    modal: true,
                    buttons: {
                        "Move Donations": function() {
                            doMoveDon();

                        },
                        "Exit": function() {
                            $( this ).dialog( "close" );
                        }
                    },
                    close: function() {
                    }
                })
                $( "input.autoCal" ).datepicker({
                    changeMonth: true,
                    changeYear: true
                });
                $('#selLookup').change( function() {
                    $.ajax(
                    { type: "POST",
                        url: "Misc.php",
                        data: ({
                            table: $("#selLookup").val(),
                            cmd: "get"
                        }),
                        success: handleResponse,
                        error: handleError,
                        datatype: "json"
                    });
                });
                $('#selCode').change( function() {
                    if (table) {
                        for (code in table) {

                            if (table[code].Code == this.value) {
                                $('#txtCode').val(this.value).prop("readonly", true);
                                $('#txtDesc').val(table[code].Description);
                                $('#txtAddl').val(table[code].Substitute);
                            }
                        }
                    }
                });
                $('.Selector').change( function() {
                    //        id = $(this).val();
                    //
                    //        $('#memName').val($("#Select1 option:selected").text());
                    //
                    //        $('#t_'+id).val('');
                    //        $('#Select1').children().remove();
                });
                $('.srchChars').keyup( function() {
                    mm = $(this).val();
                    if (mm.length > 2) {
                        id = $(this).attr('name');
                        var slectr = 's_'+id;
                        getNames($(this), slectr, 'm', 0);
                    }
                });
                $('#btnMoveDon').click( function () {
                    $('#divMoveDon').dialog({ title: 'Edit Event' });
                    $('#divMoveDon').dialog( 'open' );
                });
                $('#accordion').show();
            });
            function doMoveDon() {
                // Command the server to move donations from one name id to another.
                var ids = new Array();
                var indx = 0;
                $('.Selector').each( function () {
                    if ($(this).val() > 0) {
                        // live one
                        ids[indx++] = new movePair($(this).attr("name"), $(this).val() );
                    }
                });
                // did we capture some live ones
                if (indx > 0) {
                    $.ajax(
                    { type: "POST",
                        url: "Misc.php",
                        data: ({
                            list: ids,
                            cmd: "move"
                        }),
                        success: function(data, statusTxt, xhrObject) {
                            if (statusTxt != "success")
                                alert('Server had a problem.  ' + xhrObject.status + ", "+ xhrObject.responseText);

                            var spn = document.getElementById('webMessage');

                            if (data) {
                                data = $.parseJSON(data);
                                if (data.error) {
                                    // define the err message markup
                                    $('webResponse').removeClass("ui-state-highlight").addClass("ui-state-error");
                                    //$('#webContainer').attr("style", "display:block;");
                                    $('#webIcon').removeClass("ui-icon-info").addClass("ui-icon-alert");
                                    spn.innerHTML = "<strong>Error: </strong>"+data.error;
                                    $( "#webContainer" ).show( "slide", {}, 200);

                                }
                                else if (data.success) {
                                    // define the  message markup
                                    $('#webResponse').removeClass("ui-state-error").addClass("ui-state-highlight")
                                    //$('#webContainer').attr("style", "display:block;");
                                    $('#webIcon').removeClass("ui-icon-alert").addClass("ui-icon-info");
                                    spn.innerHTML = "Okay: "+data.success;
                                    $( "#webContainer" ).show( "slide", {}, 200);
                                }
                            }
                        },
                        error: handleError,
                        datatype: "json"
                    });

                }
            }
            function movePair(delId, donToId) {
                this.delId = delId;
                this.donToId = donToId;
            }
            function handleResponse(dataTxt, statusTxt, xhrObject) {
                if (statusTxt != "success")
                    alert('Server had a problem.  ' + xhrObject.status + ", "+ xhrObject.responseText);

                if (dataTxt.length > 0) {
                    table = $.parseJSON(dataTxt);
                    showTable(table);
                }
            }

            function handleError(xhrObject, stat, thrwnError) {
                alert("Server error: " + stat + ", " + thrwnError);
            }
            // Search for names, place any found into the appropiriate selector
            function getNames(ctrl, slectr, code, lid) {
                if (ctrl && ctrl.val() != "") {
                    inpt = {
                        cmd: "srrel",
                        letters: ctrl.val(),
                        basis: code,
                        id: lid
                    };
                    // set the wait cursor
                    $('body').css('cursor', 'wait');

                    $.get( "liveNameSearch.php",
                    inpt,
                    function(data){
                        $('body').css('cursor', 'auto');
                        if (data) {

                            names = $.parseJSON(data);
                            if (names && names.length > 0) {
                                if (names[0].error) {
                                    alert("Server error: " + names[0].error);
                                }
                                else {
                                    sel = $('#' + slectr);
                                    sel.children().remove();

                                    if (names[0].id != 0) {
                                        if (names.length ==1)
                                            optText = "<option value=''>Retrieved "+names.length+" Name</option>";
                                        else
                                            optText = "<option value=''>Retrieved "+names.length+" Names</option>";

                                        sel.append(optText);
                                    }
                                    for(var x=0; x < names.length; x++) {
                                        evt = names[x];
                                        if (evt.name) {
                                            optText = "<option value='" + evt.id + "'>(" + evt.id + ") " + evt.name+"</option>";
                                            sel.append(optText);
                                        }
                                    }
                                }
                            }
                            else {
                                alert('Bad Data');
                            }
                        }
                        else {
                            alert('Nothing was returned from the server');
                        };
                    });
                }
            }

            function showTable(data) {
                // remove any previous entries
                $('#selCode').children().remove();

                // first option is "New"
                var objOption = document.createElement("option");

                objOption.text = "New";
                objOption.value = "n_$";

                objOption.setAttribute("selected", "selected");
                $('#selCode').append(objOption);

                for(var x=0; x < data.length; x++) {
                    tbl = data[x];
                    objOption = document.createElement("option");

                    objOption.text = tbl.Description;
                    objOption.value = tbl.Code;
                    $('#selCode').append(objOption);
                }
                // clear the other text boxes
                $('#txtCode').val('').prop("disabled", false);
                $('#txtDesc').val('');
                $('#txtAddl').val('');
            }
        </script>
    </head>
    <body <?php if ($testVersion) echo "class='testbody'"; ?>>
            <?php echo $menuMarkup; ?>
        <div id="contentDiv">
            <form action="Misc.php" method="post" id="frmLookups" name="frmLookups">
                <div id="accordion" class="hhk-member-detail" style="display:none;">
                    <ul>
                        <li><a href="#lookups">Lookups</a></li>
                        <li><a href="#clean">Clean Data</a></li>
                        <li><a href="#backup">Backup Database</a></li>
                        <li><a href="#changlog">View Change Log</a></li>
                        <li><a href="#delid">Delete Member Records</a></li>
                        <li><a href="#errors">View Server Errors</a></li>
                        <li><a href="#setCook">Set House PC</a></li>
                    </ul>
                    <div id="setCook" class="ui-tabs-hide">
                        This sets or removes access on this PC that you are using now.<br/>
                        <input name="setCookie" type="submit" value="Set House PC Access"/><input name="removeCookie" type="submit" value="Remove Access"/>
                        <h3><?php echo $cookieReply; ?></h3>
                    </div>
                    <div id="lookups" class="ui-tabs-hide" >
                        <table>
                            <tr>
                                <td colspan="3" style="background-color: transparent;"><h3>Data Lookup Values</h3></td>
                            </tr>
                            <tr>
                                <th colspan="2">Heading</th>
                                <th style="width:140px;">Description</th>
                            </tr>
                            <tr>
                                <td colspan="2"><select name="selLookup" id="selLookup" ><?php echo $selLookups ?></select></td>
                                <td><select name ="selCode" id="selCode" ></select></td>
                            </tr>
                            <tr style="margin-top: 5px;">
                                <td></td><td>Edit Values</td><td></td>
                            </tr>
                            <tr>
                                <td colspan="1" class="tdlabel">Code: </td>
                                <td colspan="2"><input type="text" name="txtCode" id="txtCode" size="10" /></td>
                            </tr>
                            <tr>
                                <td class="tdlabel">Description: </td>
                                <td colspan="2"><input type="text" name="txtDesc" id="txtDesc" style="width:100%;"/></td>
                            </tr>
                            <tr>
                                <td class="tdlabel">Additional: </td>
                                <td colspan="2"><input type="text" name="txtAddl" id="txtAddl"  style="width:100%;"/></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align:right;"><input type="submit" name="btnGenLookups" value="Save" /></td>

                            </tr>
                            <tr>
                                <td colspan="3"><span id="genErrorMessage" ><?php echo $lookupErrMsg; ?></span></td>
                            </tr>
                        </table>
                    </div>
                    <div id="backup" class="ui-tabs-hide" >
                        <table>
                            <tr>
                                <td><h3>Backup Database</h3></td>
                            </tr>
                            <tr>
                                <td>Email Address:
                                    <input type="text" id ="eAddr" name="eAddr" VALUE='<?php echo $to; ?>' size="28" disabled="disabled" />
                                </td>
                            <tr>
                                <td style="text-align:right;"><input type="submit" name="btnDoBackup" value="Run Database Backup"/></td>
                            </tr>
                            <tr>
                                <td><?php echo $bkupMsg; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div id="changlog" class="ui-tabs-hide" >
                        <table>
                            <tr><td colspan="2" style="background-color: transparent;"><h3>Change Log</h3>
                                </td></tr>
                            <tr>
                                <td>Starting:
                                    <input type="text" id ="sdate" class="autoCal" name="sdate" VALUE='' size="8" />
                                </td>
                                <td>Ending:
                                    <INPUT TYPE='text' NAME='edate' id="edate" class="autoCal"  VALUE='' size=8 />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:right;"><input type="submit" name="btnGenLog" value="Run Log Report"/></td>
                            </tr>
                        </table>
                        <div id="divMkup" style="margin-top: 10px;">
<?php echo $markup; ?>
                        </div>
                    </div>
                    <div id="delid" class="ui-tabs-hide" >
                        <table>
                            <tr><td style="background-color: transparent;"><h3>Delete Member Records</h3></td></tr>
                            <tr>
                                <td>
                                    <p>Deletes Name Records and all connected records including phone, address and email.  Before you do this, reassign all donations to appropriate surviving members.</p>
                                    <p>Deletes only those records marked as 'Duplicate' and 'To Be Deleted' for member-status.  There is no way to undo this without retrieving a backup copy of the database.</p>
                                </td></tr>
                            <tr>
                                <td>
                                    These are the records marked for deletion:
                                </td>
                            </tr>
                            <tr>
                                <td>
<?php echo $delIdListing ?>
                                </td>
                            </tr>
                            <tr>
                                <td ><input type="submit" name="btnDelDups"  value="Delete Name Records"/></td>
                            </tr>
                            <tr>
                                <td><?php echo $delNamesMsg; ?></td>
                            </tr>
                            <tr>
                                <td><input type="button" id="btnMoveDon" value="Move Donations"/></td>
                            </tr>
                        </table>
                    </div>
                    <div id="errors" class="ui-tabs-hide" >
                        <table>
                            <tr><td colspan="2" style="background-color: transparent;"><h3>View Server Error Files</h3></td></tr>
                            <tr>
                                <td style="text-align:right;">
                                    <input type="submit" name="btnAdminErrors" value="View Admin Error Log"/>
                                    <input type="submit" name="btnHouseErrors" value="View Guest Tracking Error Log" style="margin-left:10px;"/>
                                    <input type="submit" name="btnVolErrors" value="View Volunteer Error Log" style="margin-left:10px;"/>
                                </td>
                            </tr>
                        </table>
                            <?php echo $contents; ?>
                    </div>
                    <div id="clean" class="ui-tabs-hide" >
                        <table>
                            <tr><td colspan="2" style="background-color: transparent;"><h3>Clean Data</h3></td></tr>
                            <tr>
                                <td style="text-align:right;">
                                    <input type="submit" name="btnClnPhone" value="Clean up Phone Numbers"/>
                                    <input type="submit" name="btnAddrs" value="Verify Addresses" style="margin-left:10px;"/>

                                </td>
                            </tr>
                        </table>
                            <?php echo $cleanMsg; ?>
                    </div>
                </div>
            </form>
            <div id="divMoveDon">
                <h3>Move Donations to Active Members</h3>
                <table>
                    <tr>
                        <th>To Be deleted</th><th>Move Donations To:</th>
                    </tr>
<?php echo $donMoveNames; ?>
                    <tr><td colspan="2"> <?php echo $getWebReplyMessage; ?></td></tr>
                </table>
            </div>
        </div>
    </body>
</html>
