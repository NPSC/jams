<?php
/**
 * Ssg.php
 *
 *
 * @category  Admin
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

/**
 * Description of Ssg
 *
 * @author Eric
 */
class Ssg {

    protected $idSsg;
    protected $idStudent;
    protected $amount;
    protected $maxAmount;
    /**
     *
     * @var \SsgRS
     */
    protected $ssgRs;
    public $ssgMembers = array();

    public function __construct(\PDO $dbh, $idSsg = 0, $idStudent = 0) {

        $ssgRs = new SsgRS();
        $rows = array();

        if ($idSsg > 0) {

            $ssgRs->idSsg->setStoredVal($idSsg);
            $rows = EditRS::select($dbh, $ssgRs, array($ssgRs->idSsg));

        } else if ($idStudent > 0) {

            $ssgRs->idStudent->setStoredVal($idStudent);
            $rows = EditRS::select($dbh, $ssgRs, array($ssgRs->idStudent));
        }

        if (count($rows) > 0) {
            EditRS::loadRow($rows[0], $ssgRs);
        }

        $this->idSsg = $ssgRs->idSsg->getStoredVal();
        $this->idStudent = $ssgRs->idStudent->getStoredVal();
        $this->ssgRs = $ssgRs;
        $this->amount = $this->findAmount($dbh);
        $this->maxAmount = $ssgRs->Max_Amount->getStoredVal();
        $this->loadMembers($dbh);

    }

    protected function findAmount(\PDO $dbh) {

        $amt = 0.00;

        $fc = $this->getFundCode();

        if ($this->idSsg > 0) {

            $stmt = $dbh->query("select sum(Original_Amount - Balance) from scholarship where Is_Deleted = 0 and Fund_Code = $fc");
            $rows = $stmt->fetchAll();

            if (count($rows) > 0) {
                $amt = floatval($rows[0][0]);
            }
        }

        return $amt;
    }

    public function isFunded() {

        if ($this->getCurrentAmount() >= $this->maxAmount) {
            return TRUE;
        }
        return FALSE;
    }

    public function getCurrentAmount() {
        return $this->amount;
    }

    public function updateCurrentAmount($deltaAmount) {
        if ($deltaAmount > 0) {
            $this->amount += $deltaAmount;
        }
    }

    public function getMaxAmount() {
        return $this->maxAmount;
    }

    public function setMaxAmount($amount) {
        $this->maxAmount = $amount;
        $this->ssgRs->Max_Amount->setNewVal($amount);
    }

    public function getStartDate() {
        return $this->ssgRs->Start_Date->getStoredVal();
    }

    public function setStartDate($strDate) {
        $this->ssgRs->Start_Date->setNewVal($strDate);
    }

    public function getGraduationDate() {
        return $this->ssgRs->Graduation_Date->getStoredVal();
    }

    public function setGraduationDate($strDate) {
        $this->ssgRs->Graduation_Date->setNewVal($strDate);
    }

    public function getFundCode() {
        return $this->ssgRs->Fund_Code->getStoredVal();
    }

    public function makeDonation(\PDO $dbh, $amount, $idDonation, $idDonor, $uname = '') {

        $donAmt = floatval($amount);
        $delta = $this->getMaxAmount() - $this->amount;

        if ($donAmt > $delta) {
            // put some into undecided
            $extraAmount = $donAmt - $delta;
            $donAmt = $delta;

            // make a unallocataed record.

            // insert into scholarship
            $schRs = new ScholarshipRS();

            $schRs->idName->setNewVal($idDonor);
            $schRs->idDonation->setNewVal($idDonation);
            $schRs->Fund_Code->setNewVal(0);
            $schRs->Original_Amount->setNewVal($extraAmount);
            $schRs->Is_Deleted->setNewVal(0);
            $schRs->Balance->setNewVal($extraAmount);

            $schRs->Updated_By->setNewVal($uname);
            $schRs->Last_Updated->setNewVal(date('Y-m-d H:i:s'));

            $schId = EditRS::insert($dbh, $schRs);
            $fc = 0;

            if ($schId > 0) {
                $dbh->exec("insert into scholarship_log (`Log_Date`,`idscholarship`,`Orig_Bal`, `Target_Fund_Code`, `Amount_Taken`, `Updated By`) values "
                        . "(now(), $schId, '$extraAmount', $fc, '0', '$uname')");
            }

        }

        $this->amount += $donAmt;

        if ($donAmt > 0) {

            // insert into scholarship
            $schRs = new ScholarshipRS();

            $schRs->idName->setNewVal($idDonor);
            $schRs->idDonation->setNewVal($idDonation);
            $schRs->Fund_Code->setNewVal($this->getFundCode());
            $schRs->Original_Amount->setNewVal($donAmt);
            $schRs->Is_Deleted->setNewVal(0);
            $schRs->Balance->setNewVal(0);

            $schRs->Updated_By->setNewVal($uname);
            $schRs->Last_Updated->setNewVal(date('Y-m-d H:i:s'));

            $schId = EditRS::insert($dbh, $schRs);
            $fc = $this->getFundCode();

            if ($schId > 0) {
                $dbh->exec("insert into scholarship_log (`Log_Date`,`idscholarship`,`Orig_Bal`, `Target_Fund_Code`,`Amount_Taken`,`Updated By`) values "
                        . "(now(), $schId, '$donAmt', $fc, '0', '$uname')");
            }
        }

    }

    protected function setTitle(\PDO $dbh, $index) {

        $stmt = $dbh->query("Select concat(ifnull(Name_First, ''), ' ', ifnull(Name_Last, '')) as `Title` from name where idName = $index");
        $rows = $stmt->fetchall();

        $title = $rows[0][0];

        $this->ssgRs->Title->setNewVal(trim($title));

    }

    public function loadMembers(\PDO $dbh) {

        $this->ssgMembers = array();

        if ($this->idSsg > 0) {

            $stmt = $dbh->query("Select ssg.*, ifnull(ns.idName,0) as `idName`, ns.Rel, ns.`Timestamp`, n.Name_First, n.Name_Last, n.Member_Status
from ssg left join name_student ns on ssg.idSsg = ns.idSsg
	left join `name` n on ns.idName = n.idName
where n.Member_Status = 'a' and ssg.idSsg = " . $this->idSsg);

            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

                if ($r['idName'] > 0) {
                    $this->ssgMembers[$r['idName']] = $r;
                }
            }
        }
    }

    public function addMember(\PDO $dbh, $idName, $rel) {

        if ($this->idSsg > 0 && $idName > 0) {

            if (isset($this->ssgMembers[$idName]) === FALSE) {
                $nsRs = new NameStudentRS();
                $nsRs->idName->setNewVal($idName);
                $nsRs->idSsg->setNewVal($this->idSsg);
                $nsRs->Rel->setNewVal($rel);

                EditRS::insert($dbh, $nsRs);

            }
        }
    }

    public function removeMember(\PDO $dbh, $idName) {

        if (isset($this->ssgMembers[$idName])) {

            $nsRs = new NameStudentRS();
            $nsRs->idName->setStoredVal($idName);
            $nsRs->idSsg->setStoredVal($this->idSsg);

            EditRS::delete($dbh, $nsRs, array($nsRs->idName, $nsRs->idSsg));

            unset($this->ssgMembers[$idName]);
        }
    }

    public function save(\PDO $dbh, $userName) {

        if ($this->idStudent == 0) {
            return FALSE;
        }

        $uS = Session::getInstance();

        $this->ssgRs->Last_Updated->setNewVal(date('Y-m-d H:i:s'));
        $this->ssgRs->Updated_By->setNewVal($userName);

        $this->ssgRs->Current_Amount->setNewVal($this->getCurrentAmount());

        if ($this->getIdSsg() > 0) {
            // Update
            EditRS::update($dbh, $this->ssgRs, array($this->ssgRs->idSsg));

        } else {
            // Insert
            $this->ssgRs->idStudent->setNewVal($this->getIdStudent());
            $this->ssgRs->Start_Date->setNewVal(date('Y-m-d'));
            $this->ssgRs->Max_Amount->setNewVal($uS->ScholarMax);
            $this->ssgRs->Fund_Code->setNewVal(incCounter($dbh, 'ScholarShipFundCode'));
            $this->ssgRs->Status->setNewVal('a');
            $this->setTitle($dbh, $this->idStudent);

            $idSsg = EditRS::insert($dbh, $this->ssgRs);

            $this->ssgRs->idSsg->setNewVal($idSsg);

        }

        EditRS::updateStoredVals($this->ssgRs);

        $this->idSsg = $this->ssgRs->idSsg->getStoredVal();
        $this->idStudent = $this->ssgRs->idStudent->getStoredVal();
        $this->amount = $this->ssgRs->Current_Amount->getStoredVal();
        $this->maxAmount = $this->ssgRs->Max_Amount->getStoredVal();

    }

    public function setIdStudent($id) {
        $this->idStudent = $id;
    }

    public function getIdSsg() {
        return $this->idSsg;
    }

    public function getIdStudent() {
        return $this->idStudent;
    }



}
