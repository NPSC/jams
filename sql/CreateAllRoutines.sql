-- --------------------------------------------------------
--
-- Procedure `delete_names_u_tbd`
--
DROP procedure IF EXISTS `delete_names_u_tbd`; -- ;

CREATE PROCEDURE `delete_names_u_tbd`()

BEGIN
delete na from name_address na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from name_demog na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from name_email na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from name_phone na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from name_student na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from name_crypto na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from name_volunteer2 na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
update donations d left join name n on d.Care_Of_Id = n.idName set d.Care_Of_Id = 0 where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
update donations d left join name n on d.Assoc_Id = n.idName set d.Assoc_Id = 0 where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from relationship na left join name n on (na.idName = n.idName or na.Target_Id = n.idName) where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from w_auth na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from w_users na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from id_securitygroup na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from mcalendar na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from mail_listing na left join name n on na.id = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from fbx na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from member_history na left join name n on na.idName = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
delete na from guest_token na left join name n on na.idGuest = n.idName where (n.Member_Status = 'u' or n.Member_Status = 'TBD');
update name n join name n1 on n.Company_Id = n1.idName set n.Company_Id=0, n.Company='' where  n1.Member_Status = 'u' or n1.Member_Status = 'TBD';
delete from name where name.Member_Status = 'u' or name.Member_Status = 'TBD';
END -- ;




-- --------------------------------------------------------
--
-- Procedure `IncrementCounter`
--
DROP procedure IF EXISTS `IncrementCounter`; -- ;

CREATE PROCEDURE `IncrementCounter`
(
    IN tabl varchar(75),
    OUT num int(11)
)
BEGIN

    UPDATE counter SET Next = (@n:= Next) + 1 WHERE Table_Name = tabl;
    select @n into num;

END
 -- ;





-- --------------------------------------------------------
--
-- Procedure `getVolCategoryCodes`
--
DROP procedure IF EXISTS `getVolCategoryCodes` -- ;

CREATE PROCEDURE `getVolCategoryCodes`
(
    IN id int(11),
    IN category varchar(15)
)

BEGIN
select distinct
    g.Table_Name as `Vol_Title`,
    g.Code as `Vol_Code`,
    ifnull(nv.Vol_Notes, '') as `Vol_Notes`,
    ifnull(nv.Vol_Status, 'z') as `Vol_Status`,
    nv.Vol_Begin,
    nv.Vol_End,
    nv.Vol_Check_Date,
    ifnull(g.Description, '') as `Description`,
    ifnull(nv.Vol_Trainer, '') as `Vol_Trainer`,
    nv.Vol_Training_Date,
    ifnull(nv.Dormant_Code, '') as `Dormant_Code`,
    ifnull(nv.Vol_Rank, '') as `Vol_Rank`,
    ifnull(nv.Updated_By, '') as `Updated_By`,
    nv.Last_Updated
from
    gen_lookups g
        left join
    name_volunteer2 nv ON g.Table_Name = nv.Vol_Category and g.Code = nv.Vol_Code and nv.idName = id
where
    g.Table_Name = category
order by Vol_Status,Description;
END -- ;





-- --------------------------------------------------------
--
-- Procedure `InsertDonor`
--
DROP procedure IF EXISTS `InsertDonor`; -- ;

CREATE PROCEDURE `InsertDonor`
(
    IN id INT
)
BEGIN
declare n int;
set n=0;
select 1 into n from name_volunteer2 where Vol_Code = 'd' and Vol_Category = 'Vol_Type' and idName = id;

IF n <> 1 then
    insert into name_volunteer2 (idName, Vol_Category, Vol_Code, Vol_Begin, Vol_Rank, Vol_Status, Updated_By, Last_Updated)
        values (id, 'Vol_Type', 'd', now(), 'm', 'a', 'sp_InsertDonor', now());
    Insert into activity (idName, Status_Code, Type, Product_Code, Other_Code, Action_Codes, Effective_Date, Source_Code)
        values (id, 'a', 'vol', 'Vol_Type|d', 'm', 'join', now(), 'sp_InsertDonor');
else
    update name_volunteer2 set Vol_Status = 'a', Last_Updated = now(), Updated_By = 'sp_InsertDonor'
    where Vol_Code = 'd' and Vol_Category = 'Vol_Type' and idName = id and Vol_Status<>'a';

    if  ROW_COUNT() > 0 then
        Insert into activity (idName, Status_Code, Type, Product_Code, Action_Codes, Effective_Date, Source_Code)
            values (id, 'a', 'vol', 'Vol_Type|d', 'rejoin', now(), 'sp_InsertDonor');
    end if;

end if;

END -- ;




-- --------------------------------------------------------
--
-- Procedure `insertHistory`
--
DROP procedure IF EXISTS `insertHistory`; -- ;

CREATE PROCEDURE `insertHistory`
(
    IN myid int
)
BEGIN
    declare n int;
    set n=0;
    select count(*) into n from admin_history where Id = myid;
    if n = 0 then
    insert into admin_history (Id) values (myid);
    else
    update admin_history set Timestamp = now() where Id = myid;
    end if;
END -- ;




-- --------------------------------------------------------
--
-- Procedure `register_web_user`
--
DROP procedure IF EXISTS `register_web_user`; -- ;

CREATE PROCEDURE `register_web_user`
(
    IN id int,
    IN fbid varchar(45),
    IN uname varchar(60),
    IN appr varchar(45),
    IN orgId varchar(3),
    IN roleId varchar(3),
    IN pw varchar(100),
    IN groupcode varchar(5)
)
BEGIN

    -- does idName exist in the fbx table?
    declare n int;
    declare m varchar(250);
    set n= -1;

    select idName into n from fbx where fb_id = fbid;

    if n >= 0 then
    -- fbid found, update id into fbx
        update fbx set idName=id, Status = 'a', Approved_By=appr, Approved_Date=now() where fb_id = fbid;

    end if;

    -- now check w_users table
    set n = 0;
    select count(*) into n from w_users where idName = id;

    if n >0  then
    -- update
        update w_users set Status='a', User_Name = uname, Verify_address = 'y', Enc_PW=pw, Updated_By=appr, Last_Updated=now() where idName = id;
        if  ROW_COUNT() > 0 then
            Insert into name_log (Date_Time, Log_Type, Sub_Type, WP_User_Id, idName, Log_Text)
                values (Now(), 'audit', 'update', 'sp_reg_web_user', id, concat('w_users: -> status=a, uname=',uname));
        end if;


        update w_auth set Status='a', User_Name = uname, Organization_Id = orgId, Role_Id = roleId, Updated_By = appr, Last_Updated = now()
            where idName = id;
        if  ROW_COUNT() > 0 then
            Insert into name_log (Date_Time, Log_Type, Sub_Type, WP_User_Id, idName, Log_Text)
                values (Now(), 'audit', 'update', 'sp_reg_web_user', id, concat('w_auth: -> status=a, role_id=',roleId));
        end if;

    else
    -- insert new record
        insert into w_users (idName, User_Name, Enc_PW, Status, Verify_Address, Updated_By, Last_Updated)
            values ( id, uname, pw, 'a', 'y', appr, now() );
        Insert into name_log (Date_Time, Log_Type, Sub_Type, WP_User_Id, idName, Log_Text)
            values (Now(), 'audit', 'new', 'sp_Reg_Web_User', id, concat('w_users: -> status=a, uname=',uname));

        insert into w_auth (idName, User_Name, Organization_Id, Role_Id, Updated_By, Last_Updated, Status)
            values ( id, uname, orgId, roleId, appr, now(), 'a');
        Insert into name_log (Date_Time, Log_Type, Sub_Type, WP_User_Id, idName, Log_Text)
            values (Now(), 'audit', 'new', 'sp_reg_web_user', id, concat('w_auth: -> status=a, role_id=',roleId));

    end if;

    -- group codes
        replace into id_securitygroup (idName, Group_Code)
            values (id, groupcode);

END -- ;





-- --------------------------------------------------------
--
-- Procedure `selectvolcategory`
--
drop procedure IF EXISTS `selectvolcategory` -- ;

CREATE PROCEDURE `selectvolcategory`
(
    IN category varchar(45)
)
BEGIN
    select `vm`.`Id` AS `Id`,
    (case when (`vm`.`MemberRecord` = 1) then concat(`vm`.`Name_Last`,', ',`vm`.`Name_First`) else `vm`.`Company` end) AS `Name_Last_First`,
    `nv`.`Vol_Code` AS `Vol_Code`,`nv`.`Vol_Status` AS `Vol_Status`,

    case when (ifnull(`g`.`Code`,'') <> '') then ifnull(`g`.`Description`,'') end AS `Description`,

    (case when (`vm`.`Member_Type` = 'ai') then `vm`.`Fullname` when (`vm`.`Member_Type` <> 'ai') then `vm`.`Company` else '' end) AS `Name_Full`,
    (case when ((`vm`.`Exclude_Phone` = 1) and (`vm`.`Preferred_Phone` <> '')) then 'x' else `vm`.`Preferred_Phone` end) AS `PreferredPhone`,
    (case when ((`vm`.`Exclude_Email` = 1) and (`vm`.`Preferred_Email` <> '')) then 'x' else `vm`.`Preferred_Email` end) AS `PreferredEmail`,
    concat_ws(' ',`vm`.`Address_1`,`vm`.`Address_2`,`vm`.`City`,`vm`.`StateProvince`,`vm`.`PostalCode`) AS `Full_Address`,
    ifnull(`d`.`Title`,'') AS `Title`,
    cast(`d`.`Begin_Active` as date) AS `Begin_Active`,
    cast(`d`.`End_Active` as date) AS `End_Active`,
    ifnull(`nv`.`Vol_Notes`,'') AS `Vol_Notes`,
    `vm`.`Member_Type` AS `Member_Type`,`nv`.`Vol_Begin` AS `Vol_Begin`,`nv`.`Vol_End` AS `Vol_End`,
    ifnull(`nv`.`Vol_Rank`,'') AS `Vol_Rank`

    from `vmember_listing` `vm` join `name_volunteer2` `nv` on `vm`.`Id` = `nv`.`idName` and `vm`.`MemberStatus` = 'a' and nv.Vol_Category = category
    left join `dormant_schedules` `d` on `nv`.`Dormant_Code` = `d`.`Code`
    left join `gen_lookups` `g` on `nv`.`Vol_Code` = `g`.`Code` and `g`.`Table_Name` = `nv`.`Vol_Category`;

END -- ;





-- --------------------------------------------------------
--
-- Procedure `sp_move_donation`
--
drop procedure IF EXISTS `sp_move_donation` -- ;

CREATE PROCEDURE sp_move_donation
(
    IN toid int,
    IN delid int,
    IN uname varchar(45)
)
BEGIN

    update donations set Donor_Id = toid, Updated_By=uname, Last_Updated=now() where Donor_Id = delid;

    if  ROW_COUNT() > 0 then
        insert into activity (idName, Type, Action_Codes, Effective_Date, Source_Code, Description)
           values (toid, 'don', 'xfer', now(), uname, concat('transfer donations from Id ', delid));
    end if;

END -- ;






-- --------------------------------------------------------
--
-- Procedure `del_webuser`
--
DROP procedure IF EXISTS `del_webuser` -- ;

CREATE PROCEDURE `del_webuser`
(
    IN id int,
    IN adminName varchar(45)
)
BEGIN
    delete from w_users where idName = id;
    delete from w_auth where idName = id;
    delete from fbx where idName = id;
    delete from id_securitygroup where idName = id;

    insert into name_log (Date_Time, Log_Type, Sub_Type, WP_User_Id, idName, Log_Text)
        values (now(), 'audit', 'update', adminName, id, 'Delete Webuser Account');

END -- ;





-- --------------------------------------------------------
--
-- Procedure `sp_move_vol_categories`
--
DROP procedure IF EXISTS `sp_move_vol_categories` -- ;

CREATE PROCEDURE `sp_move_vol_categories`
(
    IN dupId int,
    IN keepId int,
    IN adminName varchar(45)
)
BEGIN
    CREATE TEMPORARY TABLE IF NOT EXISTS tbl_vol (
        Concat_String varchar(25)
    );

    insert into tbl_vol
        select concat(Vol_Category, Vol_Code) from name_volunteer2 where idName = keepId;

    update name_volunteer2 v set v.idName = keepId, Updated_By = adminName, Last_Updated = now()
        where v.idName = dupId and concat(v.Vol_Category, v.Vol_Code) not in
        (select Concat_String from tbl_vol);

    if ROW_COUNT() > 0 then
        Insert into activity (idName, Status_Code, Type, Product_Code, Action_Codes, Effective_Date, Source_Code, Description)
            values (keepId, 'a', 'vol', 'various', 'xfer', now(), adminName, concat('Transfer from Id ',dupId));

        update mcalendar set idName = keepId, Updated_By = adminName, Last_Updated = now()
            where idName = dupId and concat(E_Vol_Category, E_Vol_Code) not in
            (select Concat_String from tbl_vol);

        update mcalendar set idName2 = keepId, Updated_By = adminName, Last_Updated = now()
            where idName2 = dupId and concat(E_Vol_Category, E_Vol_Code) not in
            (select Concat_String from tbl_vol);
    end if;

    drop table tbl_vol;
END -- ;


DROP PROCEDURE IF EXISTS `move_resv_visit` -- ;

CREATE PROCEDURE `move_resv_visit`(idRegOld int, idReg int, idHs int, id int, oldId int)
BEGIN

-- update reservation/visit/stays/fees/invoice

Update visit v set v.idRegistration = idReg, v.idHospital_stay = idHs, v.idPrimaryGuest = id where v.idRegistration = idRegOld;
Update stays s set s.idName = id where s.idName = oldId;
Update fees f set f.idRegistration = idReg, f.idName = id where f.idRegistration = idRegOld;
update invoice i set i.Sold_To_Id = id, i.idGroup = idReg where i.idGroup = idRegOld; -- idReg;

update reservation rv set rv.idRegistration = idReg, rv.idGuest = id, rv.idHospital_Stay = idHs where rv.idRegistration = idRegOld;
update reservation_guest rg set rg.idGuest = id where rg.idGuest = oldId;


END -- ;



DROP PROCEDURE IF EXISTS `remove_psg_guest` -- ;
CREATE PROCEDURE `remove_psg_guest`(pid int, oldId int)
BEGIN

delete from name_guest where idPsg = pid;
delete from hospital_stay where idPsg = pid;
delete from registration where idPsg = pid;
delete from psg where idPsg = pid;

delete from name_guest where idName = oldId;

update name set Member_Status = 'TBD' where idName = oldId;

END -- ;

