<?php
/**
 * VolNameSearch.php
 *
 * @category  Volunteer
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

require_once ("VolIncludes.php");

require_once(MEMBER . 'MemberSearch.php');

$wInit = new webInit(WebPageCode::Service);

$dbh = $wInit->dbh;



addslashesextended($_GET);

if (isset($_GET['cmd'])) {
    $c = filter_var($_GET['cmd'], FILTER_SANITIZE_STRING);
}
else
    exit();

$events = array();

switch ($c) {

    case 'filter':

        //get the q parameter from URL
        $q = '';
        if (isset($_GET['letters'])) {
            $q = filter_var(urldecode($_GET['letters']), FILTER_SANITIZE_STRING);
        }

        // get basis
        $basis = '';
        if (isset($_GET['basis'])) {
            $basis = filter_var(urldecode($_GET['basis']), FILTER_SANITIZE_STRING);
        }

        $fltr = '';
        if (isset($_GET['filter'])) {
            $fltr = filter_var(urldecode($_GET['filter']), FILTER_SANITIZE_STRING);
        }

        $events = MemberSearch::volunteerCmteFilter($dbh, $q, $basis, $fltr);

        break;

    default:
        $events = array("error"=>"Bad Command:  $c");


}



echo( json_encode($events) );


?>