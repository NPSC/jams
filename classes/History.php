<?php
/**
 * History.php
 *
 * Encapsulates common functionality for DB access
 *
 * @category  Utility
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

/**
 * Description of History
 * @package name
 * @author Eric
 */
class History {


    public static function addToStudentHistoryList(PDO $dbh, $id) {
        if ($id > 0) {
            $query = "INSERT INTO member_history (idName, Guest_Access_Date) VALUES ($id, now())
        ON DUPLICATE KEY UPDATE Guest_Access_Date = now();";
            //$query = "replace guest_history (idName, Access_Date) values ($id, now());";
            $stmt = $dbh->prepare($query);
            $stmt->execute();
        }
    }

    public static function addToMemberHistoryList(PDO $dbh, $id) {
        if ($id > 0) {
            $query = "INSERT INTO member_history (idName, Admin_Access_Date) VALUES ($id, now())
        ON DUPLICATE KEY UPDATE Admin_Access_Date = now();";
            //$query = "replace admin_history (idName, Access_Date) values ($id, now());";
            $stmt = $dbh->prepare($query);
            $stmt->execute();
        }
    }

    public static function getHistoryMarkup(PDO $dbh, $view, $page) {

        if ($view == "") {
            throw new Hk_Exception_InvalidArguement("Database view name must be defined.");
        }

        $query = "select * from $view;";
        $stmt = $dbh->query($query);

        $table = new HTMLTable();
        $table->addHeaderTr(
                $table->makeTh("Id")
                . $table->makeTh("Name")
                . $table->makeTh("Preferred Address")
                . $table->makeTh("Email")
                . $table->makeTh("Phone")
                . $table->makeTh("Company")
                );


        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

            // Build the address
            $addr = $row["Address_1"];
            $stateComma = ", ";
            $country = '';

            if (trim($row["Address_2"]) != "") {
                $addr .= " " . $row["Address_2"];
            }

            if (trim($row["StateProvince"]) == '') {
                $stateComma = '';
            }

            if ($row['Country_Code'] == 'US') {
                $country = '';
            } else {
                $country = "  " . $row['Country_Code'];
            }

            $addr .= " " . $row["City"] . $stateComma . $row["StateProvince"] . " " . $row["PostalCode"] . $country;


            // Build the page anchor
            if ($page != '') {
                $anchr = HTMLContainer::generateMarkup('a', $row['Id'], array('href'=>"$page?id=" . $row["Id"]));
            } else {
                $anchr = $row["Id"];
            }

            $table->addBodyTr(
                    $table->makeTd($anchr)
                    . $table->makeTd($row["Fullname"])
                    . $table->makeTd(trim($addr))
                    . $table->makeTd($row["Preferred_Email"])
                    . $table->makeTd($row["Preferred_Phone"])
                    . $table->makeTd($row["Company"]));

        }

        return HTMLContainer::generateMarkup("div", $table->generateMarkup(), array('class'=>'hhk-history-list'));
    }

    public static function getStudentHistoryMarkup(PDO $dbh, $page = "NameEdit.php") {
        return self::getHistoryMarkup($dbh, "vguest_history_records", $page);
    }
    public static function getMemberHistoryMarkup(PDO $dbh, $page = "NameEdit.php") {
        return self::getHistoryMarkup($dbh, "vadmin_history_records", $page);
    }


    public static function getVolEventsMarkup(PDO $dbh, DateTime $startDate) {

        $query = "select * from vrecent_calevents where `Last Updated` > '" .$startDate->format('Y-m-d'). "' order by Category, `Last Updated`;";
        $stmt = $dbh->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $fixedRows = array();

        foreach ($rows as $r) {

            // Date?
            $r['Start'] = date('D, M jS. g:i a', strtotime($r['Start']));
            $r['End'] = date('D, M jS. g:i a', strtotime($r['End']));
            $r['Last Updated'] = date('D, M jS. g:i a', strtotime($r['Last Updated']));

            if ($r['Status'] == 'Deleted') {
                $r['Status'] = HTMLContainer::generateMarkup('span', $r['Status'], array('style'=>'background-color:red;color:yellow;'));
            }

            $fixedRows[] = $r;

        }
        return $fixedRows;

    }

}
