<?php
/**
 * commonFunc.php
 *
 * @category  Utility
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

function initPDO($dbName = '') {
    $ssn = Session::getInstance();
    /* Get Sectors from session */
    if (!isset($ssn->databaseURL)) {
        die('<br/>Missing Database Initialization (initPDO)<br/>');
    }

    $dbuName = $ssn->databaseUName;
    $dbPw = $ssn->databasePWord;
    $dbHost = $ssn->databaseURL;
    $dbName = $ssn->databaseName;
    
    try {
    	$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    	$options = [
    			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    			PDO::ATTR_EMULATE_PREPARES   => true,
    	];
    	
    	$dbh = new PDO($dsn, $dbuName, $dbPw, $options);
    	
    	$dbh->exec("SET SESSION wait_timeout = 3600;");
    	
    	// Syncromize PHP and mySQL timezones
    	syncTimeZone($dbh);
    	
//         $dbh = new PDO(
//                 "mysql:host=".$ssn->databaseURL.";dbname=".($dbName == '' ? $ssn->databaseName : $dbName).";charset=Latin1",
//                 $ssn->databaseUName,
//                 $ssn->databasePWord,
//                 array(PDO::ATTR_PERSISTENT => true)
//                 );

//         $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
//         $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         $dbh->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

//         // Syncromize PHP and mySQL timezones
//         syncTimeZone($dbh);

    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        $ssn->destroy();
        die();
    }
    return $dbh;
}

function initDB() {
    $ssn = Session::getInstance();
    /* Get Sectors from session */
    if (!isset($ssn->databaseURL)) {
        die('Missing Database Initialization (initDB)');
    }

    // Open the connection
    $mysqli = mysqli_connect($ssn->databaseURL, $ssn->databaseUName, $ssn->databasePWord) or die('initDB cannot connect to the database because: ' . mysqli_error($mysqli));
    mysqli_select_db($mysqli, $ssn->databaseName);

    return $mysqli;
}

function syncTimeZone(\PDO $dbh) {

    $now = new DateTime();
    $mins = $now->getOffset() / 60;
    $sgn = ($mins < 0 ? -1 : 1);
    $mins = abs($mins);
    $hrs = floor($mins / 60);
    $mins -= $hrs * 60;
    $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
    $dbh->exec("SET time_zone='$offset';");

}

/*
  DB Closing method.
  Pass the connection variable
  obtained through initDB().
 */
function closeDB($connection) {
        mysqli_close($connection);
}

function queryDB($dbcon, $query, $silence=true, $errCode = "0") {

    if (!$silence)
        ECHO "<br />At queryDB, query=" . $query . "<br />";

    // Connection exists?
    if ($dbcon == null) {
            trigger_error("Error  db connection object is not defined. at queryDB, Query = " . $query);
            $errors = array("error" => "db connection object is not defined");
            return $errors;
    }
    else  {
        // Connection exists, so leave it alone.
        if (($QDBres = mysqli_query($dbcon, $query)) === false) {
            trigger_error("My Error: ".mysqli_error($dbcon)."; at queryDB query= " . $query);
            $errors = array("error" => mysqli_error($dbcon));
            return $errors;
        }
    }
    return $QDBres;
}

function stripslashes_gpc(&$value) {
    $value = stripslashes($value);
}

function prepareEmail(Config_Lite $config) {


    $mail = new PHPMailer;

    switch ($config->getString('email_server', 'Type', 'mail')) {

        case 'smtp':

            $mail->isSMTP();

            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';

            $mail->Host = $config->getString('email_server', 'Host', '');
            $mail->SMTPAuth = $config->getBool('email_server', 'Auth_Required', 'true');
            $mail->Username = $config->getString('email_server', 'Username', '');
            $mail->Password = decryptMessage($config->getString('email_server', 'Password', ''));

            if ($config->getString('email_server', 'Port', '') != "") {
                $mail->Port = $config->getString('email_server', 'Port', '');
            }

            break;

        case 'mail':
            $mail->isMail();
            break;

    }

    return $mail;
}

// This is named backwards.  I'll start the new name, but it may take a while for all the code to comply
function addslashesextended(&$arr_r) {

//     if (get_magic_quotes_gpc()) {
//         array_walk_recursive($arr_r, 'stripslashes_gpc');

//     }
}
function stripSlashesExtended(&$arr_r) {

//     if (get_magic_quotes_gpc()) {
//         array_walk_recursive($arr_r, 'stripslashes_gpc');

//     }
}


function setTimeZone($uS, $strDate) {
    if ($strDate != '') {

        try {
            $theDT = new DateTime($strDate);
            $theDT->setTimezone(new DateTimeZone($uS->tz));
        } catch (Exception $ex) {
            $theDT = new DateTime();
        }

        return $theDT;

    }

    return new DateTime();

}


function incCounter(PDO $dbh, $counterName) {

        $dbh->query("CALL IncrementCounter('$counterName', @num);");

        foreach ($dbh->query("SELECT @num") as $row) {
            $rptId = $row[0];
        }

        if ($rptId == 0) {
            throw new Hk_Exception_Runtime("Increment counter not set up for $counterName.");
        }

        return $rptId;
}

function checkHijack($uS) {
    if ($uS->vaddr == "y" || $uS->vaddr == "Y") {
        return true;
    } else {
        return false;
    }
}

function setHijack(PDO $dbh, $uS, $code = "") {

    $id = $uS->uid;
    $query = "update w_users set Verify_Address = '$code' where idName = $id;";
    $dbh->exec($query);
    $uS->vaddr = $code;
    return true;
}

function getYearArray() {

    $curYear = intval(date("Y"));

    $yrs = array();
    // load years
    for ($i = $curYear - 5; $i <= $curYear; $i++) {
        $yrs[$i] = array($i, $i);
    }
    return $yrs;
}

function getYearOptionsMarkup($slctd, $startYear, $fyMonths) {
    $markup = "";

    $curYear = intval(date("Y"));

    // Get month number of start of FY
    $fyDate = 12 - $fyMonths;

    // Show next year in list if we are already into the new FY
    if ($fyDate <= intval(date("n"))) {
        $curYear++;
    }


    if ($slctd == "all" || $slctd == "") {
        $markup .= "<option value='all' selected='selected'>All Years</option>";
    } else {
        $markup .= "<option value='all'>All Years</option>";
    }

    // load years
    for ($i = $startYear; $i <= $curYear; $i++) {
        if ($slctd == $i) {
            $slctMarkup = "selected='selected'";
        } else {
            $slctMarkup = "";
        }
        $markup .= "<option value='" . $i . "' $slctMarkup>" . $i . "</option>";
    }
    return $markup;
}


function getKey()
{
	return "017d609a4b2d8910685595C8df";
}

function getIV()
{
	return "fYfhHeDmf j98UUy4";
}


function encryptMessage($input)
{
	$key = getKey();
	$iv = getIV();
	
	return encrypt_decrypt('encrypt', $input, $key, $iv);
}

function getNotesKey($keyPart) {
    return "E4HD9h4DhS56DY" . trim($keyPart) . "3Nf";
}


function encryptNotes($input, $pw)
{
	$crypt = "";
	if ($pw != "" && $input != "") {
		$key = getNotesKey($pw);
		$iv = getIV();
		
		$crypt = encrypt_decrypt('encrypt', $input, $key, $iv);
	}
	
	return $crypt;
}

function decryptNotes($encrypt, $pw)
{
	$clear = "";
	
	if ($pw != "" && $encrypt != "") {
		
		$key = getNotesKey($pw);
		$clear = encrypt_decrypt('decrypt', $encrypt, $key, getIV());
	}
	
	return $clear;
}

function decryptMessage($encrypt) {
	return encrypt_decrypt('decrypt', $encrypt, getKey(), getIV());
}

/**
 * simple method to encrypt or decrypt a plain text string
 * initialization vector(IV) has to be the same when encrypting and decrypting
 *
 * @param string $action:
 *            can be 'encrypt' or 'decrypt'
 * @param string $string:
 *            string to encrypt or decrypt
 *
 * @return string
 */
function encrypt_decrypt($action, $string, $secret_key, $secret_iv)
{
	$output = false;
	$encrypt_method = "AES-256-CBC";
	// $secret_key = 'This is my secret key';
	// $secret_iv = 'This is my secret iv';
	// hash
	$key = hash('sha256', $secret_key);
	
	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', $secret_iv), 0, 16);
	if ($action == 'encrypt') {
		$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
	} else if ($action == 'decrypt') {
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}
	return $output;
}



function readGenLookups($con, $tbl, $orderBy = "Code") {

    $query = "SELECT Code, Description, Substitute FROM gen_lookups WHERE Table_Name = '" . $tbl . "' order by $orderBy;";

    if (!is_a($con, 'mysqli')) {
        return readGenLookupsPDO($con, $tbl, $orderBy);
    } else {
        $res = queryDB($con, $query, true);
    }

    $genArray = array();

    while ($row = mysqli_fetch_array($res)) {
        $genArray[$row["Code"]] = $row;
    }
    mysqli_free_result($res);
    return $genArray;
}

function readGenLookupsPDO(PDO $dbh, $tbl, $orderBy = "Code") {

    $query = "SELECT Code, Description, Substitute FROM gen_lookups WHERE Table_Name = :tbl order by `$orderBy`;";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':tbl', $tbl, PDO::PARAM_STR);

    $genArray = array();

    if ($stmt->execute()) {
        foreach ($stmt->fetchAll() as $row) {
            $genArray[$row["Code"]] = $row;
        }
    } else {

    }
    return $genArray;
}


function readLookups(PDO $dbh, $tbl, $orderBy = "Code") {

    $query = "SELECT Code, Title FROM lookups WHERE Category = :tbl and `Use` = 'y' order by `$orderBy`;";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':tbl', $tbl, PDO::PARAM_STR);

    $genArray = array();

    if ($stmt->execute()) {
        foreach ($stmt->fetchAll() as $row) {
            $genArray[$row["Code"]] = $row;
        }
    } else {

    }
    return $genArray;
}

function doOptionsMkup($gArray, $sel, $offerBlank = true) {
    $data = "";
    if ($offerBlank) {
        $sel = trim($sel);
        if (is_null($sel) || $sel == "") {
            $data = "<option value='' selected='selected'></option>";
        } else {
            $data = "<option value=''></option>";
        }
    }
    foreach ($gArray as $row) {

        if ($sel == $row[0]) {
            $data = $data . "<option value='" . $row[0] . "' selected='selected'>" . $row[1] . "</option>";
        } else {
            $data = $data . "<option value='" . $row[0] . "'>" . $row[1] . "</option>";
        }
    }

    return $data;

}

function DoLookups($con, $tbl, $sel, $offerBlank = true) {

    $g = readGenLookups($con, $tbl);

    return doOptionsMkup($g, $sel, $offerBlank);
}

function removeOptionGroups($gArray) {
    $clean = array();
    if (is_array($gArray)) {
        foreach ($gArray as $s) {
            $clean[$s[0]] = array($s[0],$s[1]);
        }
    }
    return $clean;
}


