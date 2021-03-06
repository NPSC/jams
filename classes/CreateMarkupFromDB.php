<?php
/**
 * CreateMarkupFromDB.php
 *
 * @category  Utility
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */




class CreateMarkupFromDB {


    public static function generateHTML_Table(array $rows, $tableId) {

        $thead = "";
        $tbody = "";

        if (count($rows) < 1) {
            return "<table $tableId class='display'><thead></thead><tbody></tbody></table>";
        }

        // HEader row
        $keys = array_keys($rows[0]);
        foreach ($keys as $k) {
            $thead .= "<th>$k</th>";
        }
        if ($thead != "") {
            $thead = "<thead><tr>" . $thead . "</tr></thead>";
        }


        foreach ($rows as $r) {

            $mkupRow = "";

            foreach ($r as $col) {

                $mkupRow .= "<td>" . ($col == '' ? '&nbsp;' : $col) . "</td>";
            }

            $tbody .= "<tr>" . $mkupRow . "</tr>";

        }

        $tbody = "<tbody>" . $tbody . "</tbody>";
        if ($tableId != "") {
            $tableId = " id='$tableId' ";
        }

        return "<table $tableId cellpadding='0' cellspacing='0' border='0' class='display'>" . $thead . $tbody . "</table>";

    }

    public static function generateHTMLSummary($sumaryRows, $reportTitle) {
        $summaryRowsTxt = "";
        $txtHeader = "<tr><th colspan='2'>" . $reportTitle . " <input id='Print_Button' type='button' value='Print'/></th></tr>";

        // create summary table
        foreach ($sumaryRows as $key => $val) {

            if ($key != "" && $val != "") {
                $summaryRowsTxt .= "<tr><td class='tdlabel'>" . $key . "</td><td>" . $val . "</td></tr>";

            }
        }
        return "<table style='margin-top:40px; margin-bottom:10px; min-width: 350px;'>" . $txtHeader . $summaryRowsTxt . "</table>";
    }


}

?>
