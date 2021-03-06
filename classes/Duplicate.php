<?php
/**
 * Duplicate.php
 *
 *
 * @category  Admin
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
  */

/**
 * Description of Duplicate
 *
 * @author Eric
 */
class Duplicate {

    public $dupNames;

    public function getNameDuplicates(\PDO $dbh, $mType) {

        if ($mType == 'ra') {

        // get duplicate names
        $stmt = $dbh->query("select
    Name_Full, count(n.idName) as `dups`
from
    `name` n left join name_volunteer2 nv on n.idName = nv.idName and nv.Vol_Category = 'Vol_Type' and nv.Vol_Code = 'ra'
where
    n.Member_Status = 'a' and nv.Vol_Code = 'ra'
        and n.Record_Member = 1
group by n.Name_Full
having count(n.idName) > 1;");

        } else {

        $stmt = $dbh->prepare("select
    Name_Full, count(n.idName) as `dups`
from
    `name` n left join name_volunteer2 nv on n.idName = nv.idName and nv.Vol_Category = 'Vol_Type' and nv.Vol_Code = :c
where
    n.Member_Status = 'a' and nv.Vol_Code = :d
        and n.Record_Member = 1
group by n.Name_Full
having count(n.idName) > 1;");

        $stmt->execute(array(':c'=>$mType, ':d'=>$mType));

        }

        if ($stmt->rowCount() == 0) {
            return "No duplicate names were found.  ";
        }

        $this->dupNames = $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public static function expandName(\PDO $dbh, $name) {

        $stmt = $dbh->prepare("select n.idName as `Id`, n.Name_Full as `Name`, concat(na.Address_1, na.Address_2) as `Address`, na.City, na.State_Province as `St`, np.Phone_Num as Phone,
            ng.idPsg, ng.Relationship_Code as `Rel`, hs.idHospital_stay as `Hs id`, n2.idName as `P id`, n2.Name_Full as `Patient`, r.idRegistration, nv.Vol_Code
from `name` n left join name_address na on n.idName = na.idName and n.Preferred_Mail_Address = na.Purpose
	left join name_phone np on n.idName = np.idName and n.Preferred_Phone = np.Phone_Code
        left join name_guest ng on n.idName = ng.idName
        left join hospital_stay hs on ng.idPsg = hs.idPsg
        left join name n2 on hs.idPatient = n2.idName
        left join registration r on ng.idPsg = r.idPsg
        left join name_volunteer2 nv on n.idName = nv.idName and nv.Vol_Category = 'Vol_Type' and nv.Vol_Code = 'ra'
where n.Name_Full = :name");

        $stmt->execute(array(':name'=>$name));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


}
