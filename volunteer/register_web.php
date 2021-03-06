<?php
/**
 * register_web.php
 *
 * @category  Volunteer
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
require_once ('VolIncludes.php');
require_once (SEC . 'UserClass.php');
require_once (FUNCTIONS . 'recaptchalib.php');
require_once(SEC . 'Login.php');

try {

    $login = new Login();
    $config = $login->initializeSession(ciCFG_FILE);

} catch (PDOException $pex) {
    exit("<h3>Database Error.  </h3>");
} catch (Exception $ex) {
    exit ("<h3>Server Error</h3>" . $ex->getMessage());
}


// get session instance
$ssn = Session::getInstance();

if ($ssn->logged && isset($ssn->uid)) {
    header( "location:index.php");
    exit();
}

$pageTitle = $ssn->siteName;
$houseTitle = $ssn->siteName;
$logoLink = $config->getString("site", "Public_URL", "");
$resourceURL = $ssn->resourceURL;

// define db connection obj
$dbh = initPDO();

// Load the page information
try {

    $page = new ScriptAuthClass($dbh);

} catch (PDOException $ex) {

    $ssn->destroy();
    exit("Error - Database problem accessing page.");
}


// hhk.org key is default
$publickey = $config->getString("recaptcha", "Public_Key", "6Ld_MNISAAAAAIxS1IyiEBWeriUKN8j0wHF_PqRH");  //"6Ld_MNISAAAAAIxS1IyiEBWeriUKN8j0wHF_PqRH"; // you got this from the signup page
$ssl = $config->getBool('site', 'SSL', FALSE);
$recapMarkup = recaptcha_get_html($publickey, null, $ssl);


$donAlert = new alertMessage("donateResponseContainer");
$donAlert->set_DisplayAttr("none");
$donAlert->set_Context(alertMessage::Success);
$donAlert->set_iconId("donateResponseIcon");
$donAlert->set_styleId("donateResponse");
$donAlert->set_txtSpanId("donResultMessage");
$donAlert->set_Text("working");

$getDonReplyMessage = $donAlert->createMarkup();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $pageTitle; ?></title>
        <link href="<?php echo JQ_UI_CSS; ?>" rel="stylesheet" type="text/css" />
        <link href="css/publicStyle.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?php echo $resourceURL; ?><?php echo JQ_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $resourceURL; ?><?php echo JQ_UI_JS; ?>"></script>
        <script type="text/javascript" src="<?php echo $resourceURL; ?>js/md5-min.js"></script>
        <style type="text/css">
            .lblFor {
                text-align: right;
            }
            div.dispNone {
                display:none;
            }
            div.dispBlock {
                display:block;
                background-color: #FFFFFF;
                border: 3px solid #CDC8B2;
                padding: 25px;
            }
            .underNotes {
                font-size: .85em;
                font-style: italic;
                color: #4E4E4E;
            }
            input.ui-state-error {
                background:  repeat scroll 50% 50% #EFCFC2;
                color: #4C3000;
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function() {
                var tips = $('.validateTips'), tfn = $('#txtFirstName'), tln = $('#txtLastName'), tpun = $('#txtPun'), tpw = $('#txtPW'), tph = $('#txtPhone'), tem = $('#txtEmail');
                var allFields = $([]).add(tfn).add(tln).add(tpun).add(tpw).add(tph).add(tem);
                var rexEmail = /^[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
                var regPhone = /^(?:(?:[\+]?([\d]{1,3}(?:[ ]+|[\-.])))?[(]?([2-9][\d]{2})[\-\/)]?(?:[ ]+)?)?([2-9][0-9]{2})[\-.\/)]?(?:[ ]+)?([\d]{4})(?:(?:[ ]+|[xX]|(i:ext[\.]?)){1,2}([\d]{1,5}))?$/;
                var pwMatch = false;

                $.ajaxSetup({
                    beforeSend: function() {
                        //$('#loader').show()
                        $('body').css('cursor', "wait");
                    },
                    complete: function() {
                        $('body').css('cursor', "auto");
                        //$('#loader').hide()
                    },
                    cache: false
                });
                $(':input:first').focus();

                $('button', '.btnCancel').button();
                $(".btnCancel").click(function() {
                    // This is a one-way trip.
                    $('#cancelDiv').removeClass("dispNone").addClass("dispBlock");
                    $('#regFormDiv').removeClass("dispBlock").addClass("dispNone");
                });

                $('#txtPW').change(function() {
                    if ($('#txtPW2').val() != "" && $('#txtPW2').val() != this.value) {
                        updateTips("New passwords do not match");
                    }
                });
                $('#txtPW2').change(function() {
                    if ($('#txtPW').val() != "" && $('#txtPW').val() != this.value) {
                        updateTips("New passwords do not match");
                    }
                });
                $('#txtPhone').change(function() {
                    regPhone.lastIndex = 0;
                    // 0 = matached, 1 = 1st capturing group, 2 = 2nd, etc.
                    var numarry = regPhone.exec(this.value);
                    if (numarry != null && numarry.length > 3) {
                        this.value = "";
                        // Country code?
                        if (numarry[1] != null && numarry[1] != "")
                            this.value = '+' + numarry[1];
                        // The main part
                        this.value = '(' + numarry[2] + ') ' + numarry[3] + '-' + numarry[4].substr(0, 4);
                    }
                });
                $('button', '.btnReg').button();
                $(".btnReg").click(function() {

                    allFields.removeClass("ui-state-error");
                    setAlert('');

                    if (!checkLength($('#txtFirstName'), 'First name', 1, 45))
                        return;
                    if (!checkLength($('#txtLastName'), 'Last name', 1, 45))
                        return;
                    if (!checkLength($('#txtEmail'), 'Email address', 5, 100))
                        return;
                    if (!checkRegexp($('#txtEmail'), rexEmail, 'Incorrect Email'))
                        return;
                    if (!checkLength($('#txtPun'), 'User Name', 6, 45))
                        return;
                    if (!checkLength($('#txtPW'), 'Password', 7, 45))
                        return;

                    if ($('#txtPW').val() != $('#txtPW2').val()) {
                        updateTips("Passwords do not match");
                        return;
                    }
                    $(this).prop('disable', true);
                    var parms = {
                        c: "web",
                        fn: $('#txtFirstName').val(),
                        ln: $('#txtLastName').val(),
                        ph: $('#txtPhone').val(),
                        em: $('#txtEmail').val(),
                        pun: $('#txtPun').val(),
                        recaptcha_challenge_field: $('#recaptcha_challenge_field').val(),
                        recaptcha_response_field: $('#recaptcha_response_field').val(),
                        pw: hex_md5($('#txtPW').val())
                    };

                    $.post(
                            "ws_reg.php",
                            parms,
                            function (data) {
                                if (data != null && data != "") {

                                    try {
                                        var data = $.parseJSON(data);
                                    } catch (e) {
                                        alert("error - " + e.description);
                                        return;
                                    }

                                    if (data.success) {
                                        $('#regCompleteDiv').removeClass("dispNone").addClass("dispBlock");
                                        $('#regFormDiv').removeClass("dispBlock").addClass("dispNone");

                                    }
                                    else if (data.warning) {
                                        $('p.validateTips').val(data.warning);
                                        alert("Warning: " + data.warning);
                                    }
                                    else if (data.error) {
                                        if (data.error === "captcha") {
                                            alert("Error: " + data.error);
                                            //updateTips("'Captcha' words do not match the image.");
                                        }
                                        else
                                            alert("Server error: " + data.error);
                                    }
                                    else {
                                        alert('Server Error');
                                    }
                                }
                                else {
                                    alert('Nothing was returned from the server');
                                }
                            }
                    );
                });
                function setAlert(msgText) {
                    msgText = msgText.replace(/^\s+|\s+$/g, "");

                    var spn = document.getElementById('donResultMessage');

                    if (msgText == '') {
                        // hide the control
                        $('#donateResponseContainer').attr("style", "display:none;");

                    }
                    else {
                        // define the error message markup
                        $('#donateResponse').removeClass("ui-state-highlight").addClass("ui-state-error");
                        $('#donateResponseContainer').attr("style", "display:block;");
                        $('#donateResponseIcon').removeClass("ui-icon-info").addClass("ui-icon-alert");
                        spn.innerHTML = "<strong>Error: </strong>" + msgText;
                    }
                }

                function updateTips(t) {
                    setAlert(t);

                }

                function checkLength(o, n, min, max) {
                    if (o.val().length > max || o.val().length < min) {
                        o.addClass("ui-state-error");
                        if (min == max)
                            updateTips("Length of the " + n + " must be " + max + ".");
                        else
                            updateTips("Length of the " + n + " must be between " +
                                    min + " and " + max + ".");
                        return false;
                    } else {
                        return true;
                    }
                }

                function checkRegexp(o, regexp, n) {
                    if (!regexp.test(o.val()) && o.val() != "") {
                        o.addClass("ui-state-error");

                        updateTips(n);
                        return false;
                    } else {
                        return true;
                    }
                }
                var RecaptchaOptions = {
                    theme: 'clean'
                };

            });
        </script>
    </head>
    <body>
        <div id="wrapper">
            <a href="<?php echo $logoLink; ?>"><div id="logoLT"></div></a>
            <div style="clear:both;"></div>
            <div id="cancelDiv" class="dispNone">
                <h3>Registration Canceled.  <a href="index.php">Back to Login Page</a></h3>
            </div>
            <div id="regCompleteDiv" class="dispNone" >
                <h1  style="padding:10px 10px 10px 150px;">Registration Complete</h1>
                <p  style="padding:10px;">The <?php echo $houseTitle; ?> must approve you.  An email has been sent. Please wait until they contact you.</p>
            </div>
            <div id="regFormDiv" style="padding:0 25px 25px; font-size: .9em;" class="ui-widget ui-widget-content ui-corner-all">
                <h1>Register for On-Line Access</h1>
                <span style="font-size:.8em;">* Required</span>
                <table style="margin-bottom:10px;">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>*<input id="txtFirstName" title="Enter your first name here" type="text" value=""  /></td>
                            <td>*<input id="txtLastName" title="Enter your Last name here" type="text" value="" /></td>
                        </tr>
                    </tbody>
                </table>
                <table style="margin-bottom:10px;">
                    <thead>
                        <tr>
                            <th>Phone</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input name='txtPhone' id='txtPhone' type='text'  size='20' value="" /></td>
                            <td>*<input name='txtEmail' id='txtEmail' type='text' size='25' value="" /></td>
                        </tr>
                    </tbody>
                </table>
                <table style="margin-bottom:10px;">
                    <thead>
                        <tr>
                            <th title="Pick a user name with at least 6 alphanumeric characters">User Name</th>
                            <th title="Choose a password with at least 7 characters">Password</th>
                            <th title="Enter your new password again">Password Again</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>*<input title="Pick a user name with at least 6 alphanumeric characters"  id="txtPun" type="text" value="" size='15' /></td>
                            <td>*<input title="Choose a password with at least 7 characters" name='txtPW' id='txtPW' type='password' size='15' value="" /></td>
                            <td>*<input title="Enter your new password again" name='txtPW2' id='txtPW2' type='password' size='15' value="" /></td>
                        </tr>
                    </tbody>
                </table>
                <div class="validateTips"><?php echo $getDonReplyMessage; ?></div>
                <table style="width:100%">
                    <tr>
                        <td>
                            <div align="center"> </div>
                            <div>
<?php echo $recapMarkup; ?>
                            </div>
                            <div style="float: left; margin-left: 15px;">
                                <p>Your participation helps digitize books.</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right; "><div class="btnReg" style="float:right;"><button>Register</button></div><div class="btnCancel"><button>Cancel</button></div></td>
                </table>
            </div>
        </div>
    </body>
</html>
