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
 * Description of Student
 *
 * @author Eric
 */
class StudentFactory {

    const VOL_CAT = 'Vol_Type';
    const VOL_CODE = 'stu';

    public static function getObject(\PDO $dbh, \Member $name) {

        if ($name->get_type() == MemBasis::Student) {
            return new Student($dbh, $name->get_idName());
        } else {
            return new StudentSupporter($dbh, $name->get_idName());
        }
    }


    public static function isStudent(\PDO $dbh, $idName) {

        $stmt = $dbh->query("Select Member_Type from name where idName = $idName; ");

        $mType = '';

        $rows = $stmt->fetchAll();
        if (count($rows) > 0) {
            $mType = $rows[0][0];
        }

        if ($mType == MemBasis::Student) {
            return TRUE;
        }

        return FALSE;
    }
}

interface iStudent {
    public function createMarkup($page);
    public function save(\PDO $dbh, $post, $uname);
    public function getTabTitle();
    public function isStudent();
    public function numberMembers();
    public function addRelationship(\PDO $dbh, $idName, $username);
    public function removeRelationship(\PDO $dbh, $idName);
}

class Student implements iStudent {

    protected $idStudent;
    protected $amountTotal;
    /**
     *
     * @var \Ssg
     */
    protected $ssg;

    public function __construct(\PDO $dbh, $idName) {

        $this->idStudent = $idName;
        $this->ssg = new Ssg($dbh, 0, $idName);

        $this->amountTotal = $this->ssg->getCurrentAmount();
    }

    public function getAmountTotal() {
        return $this->amountTotal;
    }

    public function getFundCode() {
        return $this->ssg->getFundCode();
    }

    public function getMaxAmount() {
        return $this->ssg->getMaxAmount();
    }


    public function getTabTitle() {
        return 'Donors';
    }

    public function isStudent() {
        return TRUE;
    }

    public function save(\PDO $dbh, $post, $uname) {

        $uS = Session::getInstance();

        $gradDate = '';
        if (isset($post['txtStuGradDate'])) {
            $gradDate = filter_var($post['txtStuGradDate'], FILTER_SANITIZE_STRING);

            if ($gradDate != '') {
                $gradDate = date('Y-m-d 0:0:0', strtotime($gradDate));
            }

            $this->setGraduationDate($gradDate);
        }

        $startDate = '';
        if (isset($post['txtStuStartDate'])) {
            $startDate = filter_var($post['txtStuStartDate'], FILTER_SANITIZE_STRING);

            if ($startDate != '') {
                $startDate = date('Y-m-d 0:0:0', strtotime($startDate));
            }

            $this->setStartDate($startDate);
        }

        $addYear = FALSE;
        if (isset($post['cbStuAddYear'])) {
            $addYear = TRUE;
        }

        if ($addYear) {
            $this->setMaxAmount($this->getMaxAmount() + $uS->ScholarMax);
        }

        $this->ssg->save($dbh, $uname);
    }

    public function setGraduationDate($strDate) {
        $this->ssg->setGraduationDate($strDate);
    }

    public function setStartDate($strDate) {
        $this->ssg->setStartDate($strDate);
    }

    public function setMaxAmount($amount) {
        $this->ssg->setMaxAmount($amount);
    }

    public function numberMembers() {
        return count($this->ssg->ssgMembers);
    }

    public function createMarkup($page = 'NameEdit.php') {

        $uS = Session::getInstance();

        // List of sponsors
        $table = new HTMLTable();
        $trash = HTMLContainer::generateMarkup('span', '', array('class'=>'ui-icon ui-icon-trash', 'title'=>'Delete Link', 'style'=>'float: left; margin-right:.3em;'));

        $table->addHeaderTr(HTMLTable::makeTh('Supporters', array('colspan'=>'2')));

        if (count($this->ssg->ssgMembers) > 0) {

            foreach ($this->ssg->ssgMembers as $rName) {

                $deceasedClass = '';
                if ($rName['Member_Status'] == MemStatus::Deceased) {
                    $deceasedClass = ' hhk-deceased';
                }

                $name = $rName["Name_First"] . ' ' . $rName['Name_Last'];

                $table->addBodyTr(HTMLTable::makeTd(HTMLContainer::generateMarkup('a', $name, array('href'=>$page.'?id='.$rName['idName'], 'class'=>$deceasedClass, 'title'=>'Click to Edit this Member')), array('class'=>'hhk-rel-td'))
                    .HTMLTable::makeTd($trash, array('name'=>$rName['idName'], 'class'=>'hhk-rel-td hhk-deletelink', 'title'=>'Delete Link to ' . $name)));
            }
        }

        $table->addBodyTr(HTMLTable::makeTd('New Supporter', array('class'=>'hhk-newlink', 'title'=>'Link a new Supporter', 'colspan'=>'2', 'style'=>'text-align: center;')));

        $asTbl = new HTMLTable();
        $asTbl->addBodyTr(HTMLTable::makeTd('Total Donations:', array('class'=>'tdlabel', 'style'=>'background-color:transparent;'))
                . HTMLTable::makeTd(HTMLContainer::generateMarkup('span', '$'.number_format($this->amountTotal, 2)), array('style'=>'background-color:transparent;')));

        $asTbl->addBodyTr(HTMLTable::makeTd('Starting Date:', array('class'=>'tdlabel', 'style'=>'background-color:transparent;'))
                . HTMLTable::makeTd(HTMLInput::generateMarkup(($this->ssg->getStartDate() == '' ? '' : date('M j, Y', strtotime($this->ssg->getStartDate()))), array('name'=>'txtStuStartDate', 'class'=>'ckdate')), array('style'=>'background-color:transparent;')));

        $asTbl->addBodyTr(HTMLTable::makeTd('Graduation Date:', array('class'=>'tdlabel', 'style'=>'background-color:transparent;'))
                . HTMLTable::makeTd(HTMLInput::generateMarkup(($this->ssg->getGraduationDate() == '' ? '' : date('M j, Y', strtotime($this->ssg->getGraduationDate()))), array('name'=>'txtStuGradDate', 'class'=>'ckdate')), array('style'=>'background-color:transparent;')));

        $asTbl->addBodyTr(HTMLTable::makeTd('Scholarship Amount:', array('class'=>'tdlabel', 'style'=>'background-color:transparent;'))
                . HTMLTable::makeTd(
                        HTMLContainer::generateMarkup('span', '$'.number_format($this->getMaxAmount(), 2), array('id'=>'spnStuMax'))
                         . HTMLContainer::generateMarkup('label', '(+1 Year', array('style'=>'margin-left:.5em;margin-right:.3em;'))
                         . HTMLInput::generateMarkup('', array('type'=>'checkbox', 'name'=>'cbStuAddYear', 'data-udid'=>'spnStuMax', 'data-amt'=>number_format($this->getMaxAmount(), 2, '.', ''), 'data-addnl'=>$uS->ScholarMax)) . ')', array('style'=>'background-color:transparent;')));


        $div = HTMLContainer::generateMarkup('div', $asTbl->generateMarkup(), array('style'=>'float:left; margin-left:15px; min-width:290px;'));

        return HTMLContainer::generateMarkup('div', $table->generateMarkup(array('style'=>'float:left;', 'class'=>'hhk-relations')) . $div, array('id'=>'acmm', 'name'=>'m'));

    }

    public function addRelationship(\PDO $dbh, $idName, $username) {
        // add a supporter/donor
        $this->ssg->save($dbh, $username);
        $this->ssg->addMember($dbh, $idName, 'm');

        return 'Supporter Added.  ';
    }

    public function removeRelationship(\PDO $dbh, $idName) {
        // Remove a supporter
        $this->ssg->removeMember($dbh, $idName);

        return 'Supporter Removed.  ';
    }

    public function getStudentOptions() {
        return '';
    }
}


class StudentSupporter implements iStudent {

    protected $idName;
    protected $groups;

    public function __construct(\PDO $dbh, $idName) {

        $this->idName = $idName;
        $this->groups = $this->loadStudentGroups($dbh, $idName);
    }

    public function getTabTitle() {
        return 'Students';
    }

    public function isStudent() {
        return FALSE;
    }

    public function numberMembers() {
        return count($this->groups);
    }

    protected static function loadStudentGroups(\PDO $dbh, $idName) {

        $grps = array();

        $stmt = $dbh->query("Select ns.idSsg, ns.Rel, s.idStudent, s.Start_Date, s.Graduation_Date, s.IsFunded, n.Name_First, n.Name_Last, n.Member_Status
from name_student ns left join ssg s on ns.idSsg = s.idSsg left join `name` n on s.idStudent = n.idName where ns.idName = " . $dbh->quote($idName));

        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

           $grps[$r['idSsg']] = $r;

        }

        return $grps;

    }

    public function getStudentOptions() {

        // add "No Student" option
        $opts = '<option value="0">(Unallocated)</option>';

        foreach ($this->groups as $g) {

            $opts .= '<option value="' . $g['idStudent'] . '">' . $g['Name_First'] . ' ' . $g['Name_Last'] . '</option>';
        }

        return $opts;
    }

    public function createMarkup($page = 'NameEdit.php') {

        // List of sponsors
        $table = new HTMLTable();
        $trash = HTMLContainer::generateMarkup('span', '', array('class'=>'ui-icon ui-icon-trash', 'title'=>'Delete Link', 'style'=>'float: left; margin-right:.3em;'));

        $table->addHeaderTr(HTMLTable::makeTh('Students', array('colspan'=>'2')));

        if (count($this->groups) > 0) {

            foreach ($this->groups as $rName) {

                $deceasedClass = '';
                if ($rName['Member_Status'] == MemStatus::Deceased) {
                    $deceasedClass = ' hhk-deceased';
                }

                $name = $rName["Name_First"] . ' ' . $rName['Name_Last'];

                $table->addBodyTr(HTMLTable::makeTd(HTMLContainer::generateMarkup('a', $name, array('href'=>$page.'?id='.$rName['idStudent'], 'class'=>$deceasedClass, 'title'=>'Click to Edit this Student')), array('class'=>'hhk-rel-td'))
                    .HTMLTable::makeTd($trash, array('name'=>$rName['idStudent'], 'class'=>'hhk-rel-td hhk-deletelink', 'title'=>'Delete Link to ' . $name)));
            }
        }

        $table->addBodyTr(HTMLTable::makeTd('New Student', array('class'=>'hhk-newlink', 'title'=>'Link a new Student', 'colspan'=>'2', 'style'=>'text-align: center;')));

        return HTMLContainer::generateMarkup('div', $table->generateMarkup(), array('id'=>'acmstu', 'name'=>'stu', 'class'=>'hhk-relations'));

    }

    public function addRelationship(\PDO $dbh, $idName, $username) {

        // Add a student
        $message = 'New Student Failed.  ';

        if (StudentFactory::isStudent($dbh, $idName)) {

            $student = new Student($dbh, $idName);
            $student->addRelationship($dbh, $this->idName, $username);
            $message = 'Student Added.  ';
        }

        return $message;
    }

    public function removeRelationship(\PDO $dbh, $idName) {

        // Remove a student

        $message = 'Remove Student Failed.  ';

        if (StudentFactory::isStudent($dbh, $idName)) {

            $student = new Student($dbh, $idName);
            $student->removeRelationship($dbh, $this->idName);
            $message = 'Student Removed.  ';
        }

        return $message;
    }

    public function save(\PDO $dbh, $post, $uname) {

    }

}


class StudentFunding {

    public static function getDialog(\PDO $dbh, $id, $who) {

        $markup = '';

        if ($who == 's') {

            $name = Member::GetDesignatedMember($dbh, $id, MemBasis::Student);
            $student = StudentFactory::getObject($dbh, $name);

            $markup = HTMLContainer::generateMarkup('h3', $name->getMemberName(). ',  Current Funding = $' . HTMLContainer::generateMarkup('span', number_format($student->getAmountTotal(),2), array('id'=>'spnSubAmt')));
            $markup .= HTMLContainer::generateMarkup('p', 'Target:  $' . HTMLContainer::generateMarkup('span', number_format($student->getMaxAmount(),2), array('id'=>'spnMaxAmt')) . ',  Balance Needed: '.number_format($student->getMaxAmount() - $student->getAmountTotal(), 2));

            $tbl = self::getDonorsTbl($dbh, $student->getFundCode());


            $markup .= $tbl->generateMarkup();

        }

        return $markup;
    }

    public static function saveDialog(\PDO $dbh, $studentId, $dons, $uname = '') {

        if ($studentId < 1) {
            return 'Student Id not defined.  ';
        }

        $ssg = new Ssg($dbh, 0, $studentId);
        $targetFundCode = $ssg->getFundCode();


        $rtnMessage = '';
        $studentDeltaAmt = 0.00;


        foreach ($dons as $ar) {

            $id = 0;
            $fc = -1;
            $take = 0;

            if (isset($ar['id'])) {
                $id = intval(filter_var($ar['id'], FILTER_SANITIZE_NUMBER_INT), 10);
            }

            if (isset($ar['fc'])) {
                $fc = intval(filter_var($ar['fc'], FILTER_SANITIZE_NUMBER_INT), 10);
            }

            if (isset($ar['take'])) {
                $take = floatval(filter_var($ar['take'], FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND));
            }

            if ($id < 1 || $fc < 0 || $take < 0.01) {
                continue;
            }

            $scRs = new ScholarshipRS();
            $scRs->idName->setStoredVal($id);
            $scRs->Fund_Code->setStoredVal($fc);
            $scRs->Is_Deleted->setStoredVal(0);
            $rows = EditRS::select($dbh, $scRs, array($scRs->idName, $scRs->Fund_Code, $scRs->Is_Deleted));

            $takeBal = $take;

            foreach ($rows as $r) {

                if ($takeBal <= 0) {
                    break;
                }

                $scRs = new ScholarshipRS();
                EditRS::loadRow($r, $scRs);

                $bal = floatval($scRs->Balance->getStoredVal());
                $oBal = floatval($scRs->Balance->getStoredVal());

                if ($bal <= 0) {
                    continue;
                }

                if ($bal > $takeBal) {
                    // split this into two
                    $bal = $bal - $takeBal;

                    $ssg->makeDonation($dbh, $takeBal, $scRs->idDonation->getStoredVal(), $id, $uname);
                    $takeBal = 0;

                } else {
                    // this record becomes targeted to student
                    $scRs->Fund_Code->setNewVal($targetFundCode);
                    $scRs->Original_Amount->setNewVal($bal);
                    $takeBal = $takeBal - $bal;
                    $bal = 0;
                }

                // update table
                $scRs->Updated_By->setNewVal($uname);
                $scRs->Balance->setNewVal($bal);
                $rcrds = EditRS::update($dbh, $scRs, array($scRs->idscholarship));

                $amtTaken = $oBal - $bal;
                $studentDeltaAmt += $amtTaken;
                $schId = $scRs->idscholarship->getStoredVal();


                if ($rcrds > 0) {
                    $dbh->exec("insert into scholarship_log (`Log_Date`,`idscholarship`,`Orig_Bal`, `Target_Fund_Code`,`Amount_Taken`,`Updated By`) values "
                            . "(now(), $schId, '$oBal', '$targetFundCode', '$amtTaken', '$uname')");
                }

            }

        }

        // finalize amount for student
        if ($studentDeltaAmt > 0) {

            $ssg->updateCurrentAmount($studentDeltaAmt);
            $ssg->save($dbh, $uname);
            $rtnMessage .= 'Ok';

        }

        return $rtnMessage;
    }

    public static function getDonorsTbl(\PDO $dbh, $fundCode) {

        $query = "select
    sc.Fund_Code,
    sc.idName,
    n.Name_First,
    n.Name_Last,
    ifnull(s.Title, 'Unallocated') as `Title`,
    sum(sc.Original_Amount) as Original_Amount,
    sum(sc.Balance) as Balance
from
    scholarship sc
        left join
    name n ON sc.idName = n.idName
        left join
    ssg s ON sc.Fund_Code = s.Fund_Code
where sc.Is_Deleted = 0 and (sc.Fund_Code = 0 or sc.Fund_Code = :fc) and sc.Balance > 0
group by sc.idName, sc.Fund_Code
order by sc.Fund_Code, n.Name_Last, n.Name_First;";


    $stmt = $dbh->prepare($query);
    $stmt->execute(array(':fc'=>$fundCode));

    $tbl = new HTMLTable();
    $tbl->addHeaderTr(HTMLTable::makeTh('Donor Id')
            .HTMLTable::makeTh('First')
            .HTMLTable::makeTh('Last')
            .HTMLTable::makeTh('Fund')
            .HTMLTable::makeTh('Donations')
            .HTMLTable::makeTh('Balance')
            .HTMLTable::makeTh('Take')
    );

    $totalBal = 0;
    $totalOrig = 0;

    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $origAmt = floatval($r['Original_Amount']);
        $curAmt = floatval($r['Balance']);
        $totalBal += $curAmt;
        $totalOrig += $origAmt;

        $idsMarkup = HTMLContainer::generateMarkup('a', $r['idName'], array('href'=>'NameEdit.php?id=' . $r['idName']));

        $tbl->addBodyTr(
                HTMLTable::makeTd($idsMarkup)
            .HTMLTable::makeTd($r['Name_First'])
            .HTMLTable::makeTd($r['Name_Last'])
            .HTMLTable::makeTd($r['Title'])

            .HTMLTable::makeTd(number_format($origAmt, 2), array('style'=>'text-align:right;'))
            .HTMLTable::makeTd(
                    HTMLContainer::generateMarkup('span', number_format($curAmt, 2), array('class'=>'hhk-fund-bal', 'id'=>'txtbal_' . $r['Fund_Code'].$r['idName']))
                    , array('style'=>'text-align:right;'))
            .HTMLTable::makeTd(
                    HTMLInput::generateMarkup('', array('type'=>'text', 'size'=>'6', 'class'=>'hhk-fund-take', 'style'=>'text-align:right;', 'id'=>'txtd_' . $r['Fund_Code'].$r['idName'], 'data-id'=>$r['idName'], 'data-fc'=>$r['Fund_Code']))
                    , array('style'=>'text-align:center;'))
            );

    }

    // add totals
    $tbl->addFooterTr(
            HTMLTable::makeTd('Totals:', array('colspan'=>'4', 'class'=>'tdlabel'))
            .HTMLTable::makeTd(HTMLContainer::generateMarkup('span', number_format($totalOrig,2), array('id'=>'spnTotalOrig')), array('style'=>'text-align:right;'))
            .HTMLTable::makeTd(HTMLContainer::generateMarkup('span', number_format($totalBal,2), array('id'=>'spnTotalBal')), array('style'=>'text-align:right;'))
            .HTMLTable::makeTd(HTMLContainer::generateMarkup('span', '', array('id'=>'spnTotalTake')), array('style'=>'text-align:right;'))
            );


    return $tbl;

    }

}