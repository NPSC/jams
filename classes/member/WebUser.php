<?php
/**
 * WebUser.php
 *
 * @category  Member
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

/**
 * Description of WebUser
 * @package name
 * @author Eric
 */
class WebUser {

    public static function loadWebUserRS(PDO $dbh, $id) {
        $wUserRS = new W_usersRS();
        $wUserRS->idName->setStoredVal($id);
        $rows = EditRS::select($dbh, $wUserRS, array($wUserRS->idName));
        if (count($rows) > 0) {
            EditRS::loadRow($rows[0], $wUserRS);
        }
        return $wUserRS;
    }

    public static function getWebUserMarkup(PDO $dbh, $id, $maintFlag, $wUserRS = NULL) {
        // Web User page
        if (is_null($wUserRS)) {
            $wUserRS = self::loadWebUserRs($dbh, $id);
        }

        // No record.  Return nothing.
        if ($wUserRS->User_Name->getStoredVal() == "") {
            return "<h3>Not a web user</h3>";
        }


        $lastWebAccess = $wUserRS->Last_Login->getStoredVal() == '' ? '' : date('M j, Y', strtotime($wUserRS->Last_Login->getStoredVal()));

        $wAuthRS = new W_authRS();
        $wAuthRS->idName->setStoredVal($id);
        $rws = EditRS::select($dbh, $wAuthRS, array($wAuthRS->idName));

        if (count($rws) > 0) {
            EditRS::loadRow($rws[0], $wAuthRS);
        }

        $wVerifyAddr = readGenLookupsPDO($dbh, 'Verify_User_Address');
        $wStatusMkup = readGenLookupsPDO($dbh, 'Web_User_Status');
        $roleMkup = readGenLookupsPDO($dbh, 'Role_Codes');

        $tbl = new HTMLTable();
        $tbl->addBodyTr(HTMLTable::makeTh("Web Access", array('colspan'=>'2')));
        $tbl->addBodyTr(
                HTMLTable::makeTd("User Name:", array('class'=>'tdlable'))
                .HTMLTable::makeTd($wUserRS->User_Name->getStoredVal())
                );
        $tbl->addBodyTr(
                HTMLTable::makeTd("Web Status:", array('class'=>'tdlable'))
                .HTMLTable::makeTd(HTMLSelector::generateMarkup(HTMLSelector::doOptionsMkup($wStatusMkup, $wUserRS->Status->getStoredVal()), array('id'=>'selwStatus')))
                );
        $tbl->addBodyTr(
                HTMLTable::makeTd("Last Login:", array('class'=>'tdlable'))
                .HTMLTable::makeTd($lastWebAccess)
                );
        $tbl->addBodyTr(
                HTMLTable::makeTd("Verify Address:", array('class'=>'tdlable'))
                .HTMLTable::makeTd(HTMLSelector::generateMarkup(HTMLSelector::doOptionsMkup($wVerifyAddr, $wUserRS->Verify_Address->getStoredVal()), array('id'=>'selwVerify')))
                );

        $attr = array('id'=>'selwRole');
        if ($maintFlag === FALSE) {
            $attr['disabled'] = 'disabled';
        }
        $tbl->addBodyTr(
                HTMLTable::makeTd("Role:", array('class'=>'tdlable'))
                .HTMLTable::makeTd(HTMLSelector::generateMarkup(HTMLSelector::doOptionsMkup($roleMkup, $wAuthRS->Role_Id->getStoredVal()), $attr))
                );

        if ($maintFlag !== FALSE) {
            $tbl->addBodyTr(HTMLTable::makeTd(HTMLInput::generateMarkup('Change Password... ', array('id'=>"chgPW", 'type'=>'button')), array('colspan'=>'2')));
        }

        $webAlert = new alertMessage("webContainer");
        $webAlert->set_DisplayAttr("none");
        $webAlert->set_Context(alertMessage::Success);
        $webAlert->set_iconId("webIcon");
        $webAlert->set_styleId("webResponse");
        $webAlert->set_txtSpanId("webMessage");
        $webAlert->set_Text("oh-oh");

        $tbl->addBodyTr(HTMLTable::makeTh($webAlert->createMarkup(), array('colspan'=>'2')));

        return HTMLContainer::generateMarkup('div', $tbl->generateMarkup(), array('style'=>'float:left;'));

    }


    public static function getSecurityGroupMarkup(PDO $dbh, $id, $allowFlag) {

        $stmt = $dbh->query("select `Group_Code` as `Code`, `Title` as `Description` from w_groups");
        $grps = $stmt->fetchAll();
        foreach ($grps as $g) {
            $sArray[$g['Code']] = $g;
        }

        $aArray = array();

        $query = "select Group_Code, Timestamp from id_securitygroup where idName = $id;";
        $stmt = $dbh->query($query);

//        if ($stmt->rowCount() == 0) {
//            return "";
//        }

        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $r) {
            $aArray[$r[0]] = $r[1];
        }


        $m = "<table><tr><th colspan='2'>Security Groups</th><th>Date</th></tr>";

        foreach ($sArray as $g) {
            if (isset($aArray[$g["Code"]])) {
                $checked = " checked='checked' ";
                $LastUpdate = date("M j, Y", strtotime($aArray[$g["Code"]]));
            } else {
                $checked = "";
                $LastUpdate = "";
            }

            if ($allowFlag) {
                $enabled = "";
            } else {
                $enabled = " disabled='disabled' ";
            }

            $m .= "<tr><td><input type='checkbox' class='grpSec' $enabled id='grpSec_" . $g["Code"] . "' $checked /></td><td>" . $g["Description"] . "</td><td>$LastUpdate</td></tr>";
        }

        return HTMLContainer::generateMarkup('div', $m . "</table>", array('style'=>'float:left;'));

    }

}

?>
