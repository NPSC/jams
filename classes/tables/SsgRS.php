<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SsgRS
 *
 * @author Eric
 */
class SsgRS extends TableRS {

    public $idSsg;   // int(11) NOT NULL AUTO_INCREMENT,
    public $idStudent;   // int(11) NOT NULL,
    public $Fund_Code;   // int(11) NOT NULL DEFAULT '0',
    public $Title;   // varchar(145) NOT NULL DEFAULT '',
    public $Start_Date;   // date DEFAULT NULL,
    public $Graduation_Date;   // date DEFAULT NULL,
    public $IsFunded;   // varchar(4) NOT NULL DEFAULT '',
    public $Status;   // varchar(4) NOT NULL DEFAULT '',
    public $Max_Amount;   // decimal(10,2) DEFAULT '0.00',
    public $Current_Amount;   // decimal(10,2) DEFAULT '0.00',
    public $Last_Updated;   // datetime DEFAULT NULL,
    public $Updated_By;   // varchar(45) NOT NULL DEFAULT '',
    public $Timestamp;   // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,


     function __construct($TableName = 'ssg') {
        $this->idSsg = new DB_Field('idSsg', 0, new DbIntSanitizer());
        $this->idStudent = new DB_Field('idStudent', 0, new DbIntSanitizer());
        $this->Fund_Code = new DB_Field('Fund_Code', 0, new DbIntSanitizer());
        $this->Title = new DB_Field('Title', '', new DbStrSanitizer(145));
        $this->Start_Date = new DB_Field('Start_Date', null, new DbDateSanitizer("Y-m-d H:i:s"));
        $this->Graduation_Date = new DB_Field('Graduation_Date', null, new DbDateSanitizer("Y-m-d H:i:s"));
        $this->IsFunded = new DB_Field('IsFunded', '', new DbStrSanitizer(4));
        $this->Status = new DB_Field('Status', '', new DbStrSanitizer(4));
        $this->Max_Amount = new DB_Field('Max_Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Current_Amount = new DB_Field('Current_Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Updated_By = new DB_Field('Updated_By', '', new DbStrSanitizer(45), FALSE);
        $this->Last_Updated = new DB_Field('Last_Updated', NULL, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        $this->Timestamp = new DB_Field("Timestamp", NULL, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);

        parent::__construct($TableName);
     }
}


class NameStudentRS extends TableRS {


    public $idName;   // int(11) NOT NULL,
    public $idSsg;   // int(11) NOT NULL AUTO_INCREMENT,
    public $Rel;
    public $Timestamp;   // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    function __construct($TableName = "name_student") {
        $this->idSsg = new DB_Field("idSsg", 0, new DbIntSanitizer());
        $this->idName = new DB_Field("idName", 0, new DbIntSanitizer());
        $this->Rel = new DB_Field("Rel", "", new DbStrSanitizer(4));
        $this->Timestamp = new DB_Field("Timestamp", NULL, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);

        parent::__construct($TableName);
     }
}


class ScholarshipRS extends TableRS {

    public $idscholarship;   // int(11) NOT NULL AUTO_INCREMENT,
    public $idName;   // int(11) NOT NULL,
    public $idDonation;   // int(11) NOT NULL,
    public $Fund_Code;   // int(11) NOT NULL DEFAULT '0',
    public $Original_Amount;   // decimal(10,2) NOT NULL DEFAULT '0.00',
    public $Balance;   // decimal(10,2) NOT NULL DEFAULT '0.00',
    public $Is_Deleted;  // INT NOT NULL DEFAULT 0
    public $Last_Updated;   // datetime DEFAULT NULL,
    public $Updated_By;   // varchar(45) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    public $Timestamp;   // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    function __construct($TableName = "scholarship") {
        $this->idscholarship = new DB_Field("idscholarship", 0, new DbIntSanitizer());
        $this->idName = new DB_Field("idName", 0, new DbIntSanitizer(), TRUE);
        $this->idDonation = new DB_Field("idDonation", 0, new DbIntSanitizer(), TRUE);
        $this->Fund_Code = new DB_Field("Fund_Code", 0, new DbIntSanitizer(), TRUE);
        $this->Original_Amount = new DB_Field('Original_Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Balance = new DB_Field('Balance', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Is_Deleted = new DB_Field("Is_Deleted", 0, new DbIntSanitizer(), TRUE);
        $this->Updated_By = new DB_Field('Updated_By', '', new DbStrSanitizer(45), FALSE);
        $this->Last_Updated = new DB_Field('Last_Updated', NULL, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        $this->Timestamp = new DB_Field("Timestamp", NULL, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);

        parent::__construct($TableName);
     }

}