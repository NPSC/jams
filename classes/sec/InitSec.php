<?php
/**
 * InitSec.php
 *
 *
 *
 * @category  member
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

/**
 * Description of InitSec
 * @package name
 * @author Eric
 */
class InitSec {

    public static function startUp() {

        $ssn = Session::getInstance();
        $ssn->destroy();

        // Get the site configuration object
        $config = new Config_Lite(ciCFG_FILE);

        // Run as test?
        $ssn->testVersion = $config->getBool('site', 'Run_As_Test', true);
        $ssn->resourceURL = $config->getString("site","Site_URL","");
        $ssn->siteName = $config->getString("site", "Site_Name", "House");
        $ssn->futureLimit = $config->getString("calendar", "futureLimit", "1");
        $ssn->maxRepeatEvents = $config->getString("calendar", "maxRepeatEvents", "52");

        // Volunteer time zone
        $ssn->tz = $config->getString('calendar', 'VolTimeZone', "America/Los Angeles");

        // Set Timezone
        date_default_timezone_set($ssn->tz);

        try {
            $dbConfig = $config->getSection('db');
        } catch (Config_Lite_Exception $e) {
            die("Database Configurtion missing.");
        }

       if (is_array($dbConfig)) {
            $ssn->databaseURL = $dbConfig['URL'];
            $ssn->databaseUName = $dbConfig['User'];
            $ssn->databasePWord = decryptMessage($dbConfig['Password']);
            $ssn->databaseName = $dbConfig['Schema'];
        } else {
            die("Bad Database Configurtion variable");
        }

    }
}

?>
