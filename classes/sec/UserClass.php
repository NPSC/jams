<?php
/**
 * UserClass.php
 *
 * @category  Site
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
class UserClass {

    public static function _checkLogin(PDO $dbh, $username, $password, $remember = FALSE) {
         // instantiate a ChallengeGenerator object
        $chlgen = new ChallengeGenerator(false);

        // get challenge variable
        $challenge = $chlgen->getChallengeVar('challenge');
        if ($challenge === false) {
            return false;
        }

        $r = self::getUserCredentials($dbh, $username);

        if ($r != NULL) {

            // check to see if user credentials are valid
            if(md5($r['Enc_PW'].$challenge) == $password ) {
                //Regenerate session ID to prevent session fixation attacks
                $ssn = Session::getInstance();
                $ssn->regenSessionId();

                // Get magic PC cookie
                $housePc = FALSE;
                if (isset($_COOKIE["housepc"])) {
                    if (decryptMessage($_COOKIE['housepc']) == $_SERVER['REMOTE_ADDR'] . 'eric') {
                        $housePc = TRUE;
                    }
                }

                self::_setSession($dbh, $ssn, $r, $remember);
                self::setSecurityGroups($dbh, $ssn, $r['idName'], $housePc);
                return true;
            }
        }

        return false;
    }

    public static function updateDbPassword(PDO $dbh, $id, $oldPw, $newPw) {

        $ssn = Session::getInstance();

        $success = self::_checkLogin($dbh, $ssn->username, $oldPw);

        if ($success) {
            $query = "update w_users set Last_Updated = now(), Updated_By = :uname, Enc_PW = :newPw where idName = :id and Status='a';";
            $stmt = $dbh->prepare($query);
            $stmt->execute(array(':uname'=>$ssn->username, ':newPw'=>$newPw, ':id'=>$id));

            if ($stmt->rowCount() == 1) {

                return TRUE;

            }
        }
        return FALSE;
    }

    protected static function getUserCredentials(PDO $dbh, $username) {
        $query = "SELECT u.*, a.Role_Id as Role_Id FROM w_users u join w_auth a on u.idName = a.idName  WHERE u.Status='a' and u.User_Name = :uname";
        $stmt = $dbh->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(":uname" => $username));

        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows[0];
        }
        return NULL;
    }

    protected static function setSecurityGroups(PDO $dbh, Session $ssn, $id, $housePc = FALSE) {
        $grpArray = array();
        $query = "SELECT s.Group_Code, case when w.Cookie_Restricted = 1 then '1' else '0' end as `Cookie_Restricted` FROM id_securitygroup s join w_groups w on s.Group_Code = w.Group_Code WHERE s.idName = :id";
        $stmt = $dbh->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $stmt->execute(array(":id" => $id));

        if ($stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $r) {

                if ($r["Group_Code"] != "" && ($r['Cookie_Restricted'] == "0" || $housePc)) {
                    $grpArray[] = $r["Group_Code"];
                }
            }

        }
        $ssn->groupcodes = $grpArray;
    }

    protected static function _setSession(PDO $dbh, Session $ssn, &$r, $remember = false, $init = true) {

        $ssn->uid = $r["idName"];
        $ssn->username = htmlspecialchars($r["User_Name"]);
        $ssn->cookie = $r["Cookie"];
        $ssn->vaddr = $r["Verify_Address"];
        if ($r["Role_Id"] == "") {
            $ssn->rolecode = WebRole::DefaultRole;
        } else {
            $ssn->rolecode = $r["Role_Id"];
        }
        $ssn->logged = true;
        if ($remember) {
//            $this->updateCookie($r["Cookie"], true);
        }

        if ($init) {
            $session = session_id();
            $ip = $_SERVER['REMOTE_ADDR'];

            $query = "UPDATE w_users SET Session = :ssn, Ip = :adr, Last_Login=now() WHERE User_Name = :uname;";
            $stmt = $dbh->prepare($query);
            $stmt->execute(array(":ssn" => $session, ":adr" => $ip, ":uname" => $r["User_Name"]));

        }
    }

    protected static function _checkSession(PDO $dbh, Session $ssn) {

        if (isset($ssn->username)) {
            $parms = array(
                ":uname" => $ssn->username,
                ":cook" => $ssn->cookie,
                ":ssn" => session_id(),
                ":adr" => $_SERVER['REMOTE_ADDR']
                );

            $query = "SELECT u.*, a.Role_Id as Role_Id FROM w_users u join w_auth a on u.idName = a.idName WHERE u.Status='a' and " .
            "(u.User_Name = :uname) AND (u.Cookie = :cook) AND " .
            "(u.Session = :ssn) AND (u.Ip = :adr)";
            $stmt = $dbh->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute($parms);


            if ($stmt->rowCount() > 0) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $this->_setSession($dbh, $ssn, $rows[0], false, false);
                return true;

            }
        }
        $this->_logout();
        return false;
    }

    public static function _logout() {
        $uS = Session::getInstance();
        $uS->destroy();
    }

}
?>
