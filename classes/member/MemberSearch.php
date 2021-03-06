<?php

/**
 * memberSearch.php
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
 * Description of MemberSearch
 * @package name
 * @author Eric
 */
class MemberSearch {

    public static function volunteerCmteFilter(PDO $dbh, $letters, $basis, $fltr, $additional = '') {
        $events = array();

        if (strlen($letters) > 0) {

            if (strlen($letters) > 12) {
                $letters = substr($letters, 0, 12);
            }

            $q = strtolower($letters) . '%';

            if ($basis == "m") {
                $prts = explode("|", $fltr);
                if (count($prts) >= 2) {

                    $query2 = "SELECT n.idName, n.Name_Last, n.Name_First, n.Name_Nickname
        FROM name_volunteer2 v left join name n on v.idName = n.idName
        where v.Vol_Status = 'a' and Vol_Category = :vcat and Vol_Code = :vcode
        and n.idName>0 and n.Member_Status='a' and (LOWER(n.Name_Last) like :ltrln
        OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk)
        order by n.Name_Last, n.Name_First;";

                    $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $stmt->execute(array(':vcat' => $prts[0], ':vcode' => $prts[1], ':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));

                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rows as $row2) {
                        $namArray = array();

                        $namArray['id'] = $row2["idName"];
                        $namArray['value'] = $row2["Name_Last"] . ", " . $row2["Name_First"] . ($row2['Name_Nickname'] != '' ? ' (' . $row2['Name_Nickname'] . ')' : '' );
                        $namArray['last'] = $row2["Name_Last"];
                        $namArray['first'] = $row2['Name_Nickname'] != '' ? $row2['Name_Nickname'] : $row2["Name_First"];

                        $events[] = $namArray;
                    }
                    if (count($events) == 0) {
                        $events[] = array("id" => 0, "value" => "Nothing Returned");
                    }
                } else {
                    $events[] = array("error" => "Bad filter: " . $fltr);
                }


            } else if ($basis == "g" || $basis == 'ra' || $basis == 'g,p') {

                $andVc = " and nv.Vol_Code = 'g' ";
                if ($basis == 'ra') {
                    $andVc = " and nv.Vol_Code = 'ra' ";
                } else if ($basis == 'g,p') {
                    $andVc = " and nv.Vol_Code in ('g','p') ";
                }
                $q = trim($q);
                if ($additional == 'phone') {
                    $query2 = "SELECT distinct n.idName, n.Name_Last, n.Name_First, n.Name_Nickname, ifnull(np.Phone_Num, '') as `Phone`
    FROM name n join name_volunteer2 nv on n.idName = nv.idName and nv.Vol_Category = 'Vol_Type' $andVc
    left join name_phone np on n.idName = np.idName and np.Phone_Num <> ''
    where n.idName>0 and n.Member_Status='a' and n.Record_Member = 1  and (LOWER(n.Name_Last) like :ltrln
    OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk) order by n.Name_Last, n.Name_First;";
                } else {
                    $query2 = "SELECT distinct n.idName, n.Name_Last, n.Name_First, n.Name_Nickname, '' as `Phone`
    FROM name n join name_volunteer2 nv on n.idName = nv.idName and nv.Vol_Category = 'Vol_Type' $andVc
    where n.idName>0 and n.Member_Status='a' and n.Record_Member = 1  and (LOWER(n.Name_Last) like :ltrln
    OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk) order by n.Name_Last, n.Name_First;";
                }
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2["idName"];
                    $namArray['value'] = $row2["Name_Last"] . ", " . $row2["Name_First"] . ($row2['Name_Nickname'] != '' ? ' (' . $row2['Name_Nickname'] . ')' : '' );
                    if (isset($row2['Phone'])) {
                        $namArray['value'] .= '  ' . $row2['Phone'];
                        $namArray['first'] = ($row2['Name_Nickname'] != '' ? $row2['Name_Nickname'] : $row2["Name_First"] );
                        $namArray['last'] = $row2["Name_Last"];
                        $namArray['phone'] = $row2["Phone"];
                    }

                    $events[] = $namArray;
                }

                if ($additional != 'phone') {
                    $events[] = array("id" => 0, "value" => "New Guest");
                } else if (count($events) == 0) {
                    $events[] = array("id" => 0, "value" => "Nothing Found");
                }
            } else if ($basis == "p") {
                // Search patient
                $q = trim($q);
                $query2 = "SELECT n.idName, n.Name_Last, n.Name_First, n.Name_Nickname
    FROM name n join name_volunteer2 nv on n.idName = nv.idName and nv.Vol_Category = 'Vol_Type' and nv.Vol_Code ='p'
    where n.idName>0 and n.Member_Status='a' and n.Record_Member = 1  and (LOWER(n.Name_Last) like :ltrln
    OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk) order by n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2["idName"];
                    $namArray['value'] = $row2["Name_Last"] . ", " . $row2["Name_First"] . ($row2['Name_Nickname'] != '' ? ' (' . $row2['Name_Nickname'] . ')' : '' );

                    $events[] = $namArray;
                }
                $events[] = array("id" => 0, "value" => "New Patient");
            } else {
                $events[] = array("error" => "Bad Basis Code: " . $basis);
            }
        }

        return $events;
    }

    public static function searchLinks(PDO $dbh, $letters, $basis, $id, $namesOnly = FALSE) {
        $events = array();

        if ($letters == '') {
            return $events;
        }

        if (strlen($letters) > 15) {
            $letters = substr($letters, 0, 15);
        }

        $q = strtolower($letters) . '%';

        switch ($basis) {

            case "m":
                //                  0          1             2             3            4                 5             6
                $query2 = "SELECT n.idName, n.Name_Last, n.Name_First, n.Company, n.Record_Member, ifnull(n.Member_Status,'x'), ifnull(g.Description,'Undefined!') as `Descrip`
                FROM name n left join gen_lookups g on g.Table_Name='mem_status' and g.Code = n.Member_Status
                WHERE n.idName>0 and n.Member_Status not in ('u','TBD','p') and (LOWER(n.Name_Last) like :ltrln OR LOWER(n.Name_NickName) like :ltrnk
                OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Company) like :ltrco) order by n.Member_Status, n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q, ':ltrco' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];

                    if ($row2[4] == '1' || ord($row2[4]) == 1) {
                        $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                            return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                        }, $row2[1] . ", " . $row2[2]);
                    } else {
                        $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                            return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                        }, $row2[3]);
                    }

                    $namArray['stat'] = $row2[6];
                    $namArray['scode'] = $row2[5];

                    $events[] = $namArray;
                }

                break;

            case "ind":
                //                  0          1             2             3            4                 5             6
                $query2 = "SELECT n.idName, n.Name_Last, n.Name_First, n.Company, n.Record_Member, ifnull(n.Member_Status,'x'), ifnull(g.Description,'Undefined!') as `Descrip`
                FROM name n left join gen_lookups g on g.Table_Name='mem_status' and g.Code = n.Member_Status
                WHERE n.idName>0 and n.idName <> :id and n.Member_Status<>'u' and n.Member_Status<>'TBD' and (LOWER(n.Name_Last) like :ltrln
                OR LOWER(n.Name_First) like :ltrfn  OR LOWER(n.Name_NickName) like :ltrnk) order by n.Member_Status, n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':id' => $id, ':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {

                    $namArray = array();

                    $namArray['id'] = $row2[0];

                    if ($row2[4] == '1' || ord($row2[4]) == 1) {
                        $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                            return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                        }, $row2[1] . ", " . $row2[2]);
                    } else {
                        $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                            return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                        }, $row2[3]);
                    }

                    $namArray['stat'] = $row2[6];
                    $namArray['scode'] = $row2[5];

                    $events[] = $namArray;
                }
                break;

            case "e":
                $query2 = "select e.idName, e.Email, ifnull(n.Member_Status,'x'), ifnull(g.Description,'Undefined!') as `Descrip`
                from name_email e join name n on e.idName = n.idName
                left join gen_lookups g on g.Table_Name='mem_status' and g.Code = n.Member_Status
                where n.idName>0 and n.Member_Status<>'u' and Member_Status<>'TBD' and  LOWER(e.Email) like :ltr order by n.Member_Status, e.Email";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':ltr' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];
                    $namArray['value'] = $row2[1];
                    $namArray['scode'] = $row2[2];
                    $namArray["stat"] = $row2[3];
                    $events[] = $namArray;
                }
                break;

            case RelLinkType::Parnt:
                //  parents                    0               1           2               3                               4
                $query2 = "SELECT n.idName as Id, n.Name_Last, n.Name_First, ifnull(n.Member_Status,'x'), ifnull(g.Description,'Undefined!') as `Descrip`
            FROM name n left join relationship r on n.idName = r.Target_Id and :id = r.idName and r.Relation_Type = 'par'
            left join gen_lookups g on g.Table_Name='mem_status' and g.Code = n.Member_Status
            WHERE (LOWER(n.Name_Last) like :ltrln OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk) and n.Record_Member = 1
                and n.Member_Status in ('a','d','in') and n.idName <> :id2 and r.idRelationship is null order by n.Member_Status, n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':id' => $id, ':id2' => $id, ':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];
                    $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                    }, $row2[1] . ", " . $row2[2]);
                    $namArray['scode'] = $row2[3];
                    $namArray["stat"] = $row2[4];
                    $events[] = $namArray;
                }
                break;

            case RelLinkType::Child:
                // chekdren                   0               1           2                   3                           4
                $query2 = "SELECT n.idName as Id, n.Name_Last, n.Name_First, ifnull(n.Member_Status,'x'), ifnull(g.Description,'Undefined!') as `Descrip`
                FROM name n left join relationship r on n.idName = r.idName and :id = r.Target_Id and r.Relation_Type = 'par'
                left join gen_lookups g on g.Table_Name='mem_status' and g.Code = n.Member_Status
                WHERE (LOWER(n.Name_Last) like :ltrln OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk) and n.Record_Member = 1
                and n.Member_Status in ('a','d','in') and n.idName <> :id2 and r.idRelationship is null order by n.Member_Status, n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':id' => $id, ':id2' => $id, ':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];
                    $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                    }, $row2[1] . ", " . $row2[2]);
                    $namArray['scode'] = $row2[3];
                    $namArray["stat"] = $row2[4];
                    $events[] = $namArray;
                }
                break;

            case RelLinkType::Sibling:
                //                      0               1              2            3                               4
                $query2 = "SELECT n.idName as Id, n.Name_Last, n.Name_First, ifnull(n.Member_Status,'x'), ifnull(g.Description,'Undefined!') as `Descrip`
            FROM name n left join relationship r on n.idName = r.idName and r.Relation_Type = 'sib'
            left join gen_lookups g on g.Table_Name='mem_status' and g.Code = n.Member_Status
                WHERE (LOWER(n.Name_Last) like :ltrln OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk) and n.Record_Member = 1 and ifnull(r.Group_Code,'0') not in
                (Select Group_Code from relationship where idname = :id)
                and n.Member_Status in ('a','d','in') and n.idName <> :id2 order by n.Member_Status, n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':id' => $id, ':id2' => $id, ':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];
                    $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                    }, $row2[1] . ", " . $row2[2]);

                    $namArray['scode'] = $row2[3];
                    $namArray["stat"] = $row2[4];
                    $events[] = $namArray;
                }
                break;

            case RelLinkType::Company:
                $query2 = "select idName as Id, Company from name where Record_Company=1 and Member_Status ='a' and idName>0 and idName <> :id and LOWER(Company) like :ltr order by Member_Status, Company;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':id' => $id, ':ltr' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];
                    $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                    }, $row2[1]);

                    $events[] = $namArray;
                }
                break;

            case RelLinkType::Spouse:
                $query2 = "SELECT n.idName as Id, n.Name_Last, n.Name_First, ifnull(n.Member_Status,'x'), ifnull(g.Description,'Undefined!') as `Descrip`
                FROM name n left join relationship r on (n.idName = r.idName or n.idName = r.Target_Id) and r.Relation_Type = 'sp'
                left join gen_lookups g on g.Table_Name='mem_status' and g.Code = n.Member_Status
                WHERE (LOWER(n.Name_Last) like :ltrln OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk) and n.Record_Member = 1
                and n.Member_Status in ('a','d','in') and n.idName>0 and n.idName <> :id and r.idRelationship is null order by n.Member_Status, n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':id' => $id, ':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];
                    $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                    }, $row2[1] . ", " . $row2[2]);
                    $namArray['scode'] = $row2[3];
                    $namArray["stat"] = $row2[4];
                    $events[] = $namArray;
                }
                break;

            case RelLinkType::Employee:
                $query2 = "SELECT n.idName, n.Name_Last, n.Name_First, ifnull(n.Member_Status,'x'), ifnull(g.Description,'Undefined!') as `Descrip`
            FROM name n left join gen_lookups g on g.Table_Name='mem_status' and g.Code = n.Member_Status
            WHERE n.Company_Id = 0 and n.Member_Status in ('a','in') and n.Record_Member = 1 and n.idName <> :id and (LOWER(n.Name_Last) like :ltrln
                OR LOWER(n.Name_First) like :ltrfn  OR LOWER(n.Name_NickName) like :ltrnk) order by n.Member_Status, n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':id' => $id, ':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];
                    $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                    }, $row2[1] . ", " . $row2[2]);
                    $namArray['scode'] = $row2[3];
                    $namArray["stat"] = $row2[4];
                    $events[] = $namArray;
                }
                break;

            case RelLinkType::Relative:
                //                  0                   1           2                   3                       4
                $query2 = "SELECT n.idName as Id, n.Name_Last, n.Name_First, ifnull(n.Member_Status,'x'), ifnull(g.Description,'Undefined!') as `Descrip`
            FROM name n left join relationship r on n.idName = r.idName and r.Relation_Type = 'rltv'
            left join gen_lookups g on g.Table_Name='mem_status' and g.Code = n.Member_Status
                WHERE (LOWER(n.Name_Last) like :ltrln OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk) and n.Record_Member = 1 and ifnull(r.Group_Code,'0') not in
                (Select Group_Code from relationship where idname = :id)
                and n.Member_Status in ('a','d','in') and n.idName <> :id2 order by n.Member_Status, n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':id' => $id, ':id2' => $id, ':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];
                    $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                    }, $row2[1] . ", " . $row2[2]);
                    $namArray['scode'] = $row2[3];
                    $namArray["stat"] = $row2[4];
                    $events[] = $namArray;
                }
                break;

            case 'stu':
                // Looking for students
                //                  0             1           2                   3                       4
                $query2 = "SELECT n.idName, n.Name_Last, n.Name_First
        FROM name n
        where n.idName>0 and n.Member_Status='a' and n.Member_Type='bs' and (LOWER(n.Name_Last) like :ltrln
        OR LOWER(n.Name_First) like :ltrfn OR LOWER(n.Name_NickName) like :ltrnk)
        order by n.Name_Last, n.Name_First;";
                $stmt = $dbh->prepare($query2, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $stmt->execute(array(':ltrln' => $q, ':ltrfn' => $q, ':ltrnk' => $q));
                $rows = $stmt->fetchAll(PDO::FETCH_NUM);

                foreach ($rows as $row2) {
                    $namArray = array();

                    $namArray['id'] = $row2[0];
                    $namArray['value'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) {
                        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
                    }, $row2[1] . ", " . $row2[2]);
                    $namArray['scode'] = 'a';
                    $namArray["stat"] = 'Active';
                    $events[] = $namArray;
                }
                break;

            default:
                $events = array("error" => "Bad Basis Code: " . $basis);
        }

        if ($namesOnly === FALSE) {
            $events[] = array("id" => MemDesignation::Individual, "value" => "New Individual");
            $events[] = array("id" => 'stu', "value" => "New Student");
            $events[] = array("id" => MemDesignation::Organization, "value" => "New Organization");
        }

        if (count($events) == 0) {
            $events[] = array("id" => 'x', 'value' => 'Nothing Returned');
        }

        return $events;
    }


    /**
     * Searches for a previous occurance of the supplied name.
     * Duplicate prevention.
     *
     * @param PDO $dbh
     * @param array $post
     * @throws Hk_Exception_Runtime
     */
    public static function searchName(PDO $dbh, $memDesignation, $nameLast, $nameFirst = '', $email = '', $phone = '') {

        $email = strtolower($email);
        $phone = strtolower($phone);
        $nl = strtolower($nameLast);

        // Check for individual
        if ($memDesignation == MemDesignation::Individual) {

            $nf = strtolower($nameFirst);

            $query = "SELECT n.idName, concat(n.Name_Last, ', ', n.Name_First, case when n.Name_Nickname is null
                then '' else concat('(' , n.Name_nickname, ')') end) as `Name`, n.Company, n.Member_Status, ne.Email, np.Phone_Num,
                concat(na.Address_1, case when na.Address_2 = '' then '' else concat(' ', na.Address_2) end, ', ', na.City, ', ', na.State_Province, ', ', na.Postal_Code) as `Address`
            FROM name n left join name_email ne on n.idName = ne.idName
                left join name_phone np on n.idName = np.idName
                left join name_address na on n.idName = na.idName
            WHERE n.idName>0 and n.Member_Status not in ('u','TBD','p') and n.Record_Member = 1
            AND ((LOWER(n.Name_Last) = :nl AND (LOWER(n.Name_NickName) = :nf OR LOWER(n.Name_First) = :nf2)) ";

            $parms = array(':nl'=>$nl, ':nf'=>$nf, ':nf2'=>$nf);

            if ($email != '') {
                $query .= " or LOWER(ne.Email) = :em ";
                $parms[':em'] = $email;
            }

            if ($phone != '') {
                $query .= " or LOWER(np.Phone_Num) = :ph ";
                $parms[':ph'] = $phone;
            }

            $query .= ") order by n.Member_Status, n.Name_Last;";

        // Check for an organization
        } else if ($memDesignation == MemDesignation::Organization) {

            $query = "SELECT n.idName, n.Company as `Name`, n.Member_Status, ne.Email, np.Phone_Num,
                concat(na.Address_1, case when na.Address_2 = '' then '' else concat(' ', na.Address_2) end, ', ', na.City, ', ', na.State_Province, ', ', na.Postal_Code) as `Address`
            FROM name n left join name_email ne on n.idName = ne.idName
                left join name_phone np on n.idName = np.idName
                left join name_address na on n.idName = na.idName
            WHERE n.idName>0 and n.Member_Status not in ('u','TBD','p') and n.Record_Member = 0
            AND (LOWER(n.Company) = :nc ";


            $parms = array(':nc'=>$nl);

            if ($email != '') {
                $query .= " or LOWER(ne.Email) = :em ";
                $parms[':em'] = $email;
            }

            if ($phone != '') {
                $query .= " or LOWER(np.Phone_Num) = :ph ";
                $parms[':ph'] = $phone;
            }

            $query .= ") order by n.Member_Status, n.Company;";

        } else {
            throw new Hk_Exception_Runtime('Bad member designation: ' . $memDesignation);
        }

        $stmt = $dbh->prepare($query);
        $stmt->execute($parms);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public static function createDuplicatesDiv(array $dups) {

        if (count($dups) !== 0) {
            // Make a nice dialog box
            $dialog = '';
            $tbl = new HTMLTable();

            foreach ($dups as $d) {
                $tbl->addBodyTr(
                        HTMLTable::makeTd(HTMLInput::generateMarkup($d['idName'], array('type'=>'radio', 'name'=>'hhk-dup-alternate', 'class'=>'hhk-replaceDupWith')))
                        .HTMLTable::makeTd($d['idName'])
                        .HTMLTable::makeTd($d['Name'])
                        .HTMLTable::makeTd($d['Address'])
                        );
            }

            $tbl->addHeaderTr(HTMLTable::makeTh('Use') . HTMLTable::makeTh('Id') . HTMLTable::makeTh('Name') . HTMLTable::makeTh('Address'));

            return HTMLContainer::generateMarkup('div', $tbl->generateMarkup(), array('id'=>'hhkPossibleDups'));
        }
    }



}

?>
