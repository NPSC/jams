<?php
/**
 * index.php
 *
 * @category  Site
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
define('DS', DIRECTORY_SEPARATOR);
define('P_ROOT', dirname(__FILE__) . DS);


define('ciCFG_FILE', P_ROOT . 'conf' . DS . 'site.cfg');

/*
 * includes
 */

require_once ('classes' . DS . 'config' . DS . 'Lite.php');


// Get the site configuration object
$config = new Config_Lite(ciCFG_FILE);

// Run as test?
/* @var $testVersion bool */
$testVersion = $config->getBool('site', 'Run_As_Test', FALSE);
$pageTitle = $config->getString("site", "Site_Name", "Hospitality House");
$adminURL = $config->getString("site", "Admin_URL", "");
$volunteerURL = $config->getString("site", "Volunteer_URL", "");
$trainingURL = $config->getString("site", "Training_URL", "");

// get version
define("VER", $config->getString("code", "Build", ""));

/*
 * if test version, put a big TEST on the page
 */
if ($testVersion == true)
    $testHeader = "<span style='color:red;'>Test Version</span>";
else
    $testHeader = "$pageTitle";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo $pageTitle; ?></title>
        <style type="text/css">
* {
    margin: 0px;
    padding: 0px;
}
body {
    font-size: 100%;
}
.hhk-logo {
        background-image: url("images/hhkLogo.png");
        background-repeat: no-repeat;
        width: 203px;
        height: 102px;
        float:left;
    }
    .nplogo {
        background-image: url("images/nplogo-sm.png");
        background-repeat: no-repeat;
        width: 99px;
        height: 53px;
        margin-top:5px;
        display:inline-block;
    }
    .pageSpacer {
        height:60px;
        width: 100%;
        position:static;
        top: 0;
        left: 0;
    }
    .topNavigation {
        clear:both;
        margin: 0;
        padding:0;
        width:100%;
        height:60px;
        z-index:99;
        background-color: #6BA5D1;
        position: fixed;
    }
    h2.hhk-title {
        position: absolute;
        top: 7px;
        color: white;
        font-size: 2em;
        margin: 7px 5px;
        z-index: 99;
    }
    </style>
    </head>
    <body>
        <div id="page">
            <div class="topNavigation"></div>
            <div>
                <h2 class="hhk-title">
                    <?php echo $pageTitle; ?>
                </h2>
            </div><div class='pageSpacer'></div>
            <div id="content" style="margin-left: 100px;">
                <div style="margin: auto; float:left; width:350px;">
                    <a href="http://nonprofitsoftwarecorp.org/" target="blank"><div class="hhk-logo"></div></a>
                    <div style="clear:left; margin-bottom: 20px;"></div>
                    <ul style="margin: 20px;">
                        <li><a href="<?php echo $adminURL; ?>">Administration Site</a></li>
                        <?php if ($volunteerURL != '') { ?>
                        <li><a href="<?php echo $volunteerURL; ?>">Volunteers' Site</a></li>
                        <?php } if ($trainingURL != '') { ?>
                        <li><a href="<?php echo $trainingURL; ?>">Training & Demonstration</a></li>
                            <?php } ?>
                    </ul>
                    <div style="padding-top: 20px;margin-left:50px;">
                          <a href ="volunteer/register_web.php" ><span style="font-size: 1em;color: #4E4E4E;">Register for access.</span></a>
                    </div>
                    <div style="margin-top: 100px;">
                        <hr>
                        <div style="float:left;"><a href ="http://nonprofitsoftwarecorp.org"><div class="nplogo"></div></a></div>
                        <div style="float:right;font-size: smaller; margin-top:10px;margin-right:.3em;">&copy; 2014 Non Profit Software</div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
