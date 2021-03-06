<?php
/**
 * VolIncludes.php
 *
 * @category  Site
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

define('P_BASE', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);


define('REL_BASE_DIR', ".." . DS);
define('ciCFG_FILE', REL_BASE_DIR . 'conf' . DS . 'site.cfg' );
define( 'ADMIN_DIR', REL_BASE_DIR . "admin" . DS);
define('REL_BASE_SITE', "../");
define('CLASSES', REL_BASE_DIR . 'classes' . DS);
define('DB_TABLES', CLASSES . 'tables' . DS);
/**
 * SEC path to security classes
 */
define('SEC', CLASSES . 'sec' . DS);
/**
 * PMT path to payment classes
 */
define('PMT', CLASSES . 'Payment' . DS);
define('FUNCTIONS', REL_BASE_DIR . 'functions' .DS);
define('MEMBER', CLASSES . 'member' . DS);

// paths
define('JQ_UI_CSS', 'css/sunny/jquery-ui-1.10.3.custom.min.css');
define('JQ_DT_CSS', 'css/jquery.dataTables.min.css');
define('JQ_UI_JS', 'js/jquery-ui.min.js');
define('JQ_JS', 'js/jquery-1.11.0.min.js');
define('JQ_DT_JS', 'js/jquery.dataTables.min.js');
define('FULLC_JS', 'js/fullcalendar.min.js');
define('FULLC_CSS', 'css/fullcalendar.css');

define('PRINT_AREA_JS', "js/jquery.PrintArea.js");
define('TOP_NAV_CSS', "<link href='css/topNav.css' rel='stylesheet' type='text/css' />");

date_default_timezone_set('America/Chicago');

require_once (FUNCTIONS . 'commonFunc.php');
require_once (CLASSES . 'config'. DS . 'Lite.php');
require_once (SEC . 'sessionClass.php');
require_once (CLASSES . 'alertMessage.php');
require_once (CLASSES . 'Exception_hk/Hk_Exception.php');
require_once (CLASSES . 'HTML_Controls.php');
require_once (SEC . 'SecurityComponent.php');
require_once (SEC . 'ScriptAuthClass.php');
require_once (SEC . 'ComponentAuthClass.php');
require_once (CLASSES . 'SysConst.php');
require_once (SEC . 'webInit.php');


?>
