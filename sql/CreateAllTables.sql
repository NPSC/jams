
-- -----------------------------------------------------
-- Table `activity`
-- -----------------------------------------------------
CREATE TABLE if not exists `activity` (
  `idActivity` int(11) NOT NULL AUTO_INCREMENT,
  `idName` int(11) NOT NULL,
  `Type` varchar(15) NOT NULL DEFAULT '',
  `Trans_Date` datetime DEFAULT NULL,
  `Effective_Date` datetime DEFAULT NULL,
  `Product_Code` varchar(45) NOT NULL DEFAULT '',
  `Other_Code` varchar(45) NOT NULL DEFAULT '',
  `Description` varchar(245) NOT NULL DEFAULT '',
  `Source_System` varchar(45) NOT NULL DEFAULT '',
  `Source_Code` varchar(45) NOT NULL DEFAULT '',
  `Quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Category` varchar(45) NOT NULL DEFAULT '',
  `Units` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Thru_Date` datetime DEFAULT NULL,
  `Member_Type` varchar(15) NOT NULL DEFAULT '',
  `Action_Codes` varchar(245) NOT NULL DEFAULT '',
  `Pay_Method` varchar(45) NOT NULL DEFAULT '',
  `Note` text,
  `Batch_Num` varchar(25) NOT NULL DEFAULT '',
  `Company_ID` varchar(15) NOT NULL DEFAULT '',
  `Originating_Trans_Num` int(11) NOT NULL DEFAULT '0',
  `Org_Code` varchar(5) NOT NULL DEFAULT '',
  `Campaign_Code` varchar(45) NOT NULL DEFAULT '',
  `Mail_Merge_Code` varchar(45) NOT NULL DEFAULT '',
  `Solicitation_Text` varchar(200) NOT NULL DEFAULT '',
  `Solicitor_Id` int(11) NOT NULL DEFAULT '0',
  `Salutation_Code` varchar(15) NOT NULL DEFAULT '',
  `Status_Code` varchar(5) NOT NULL DEFAULT '',
  `Grace_Period` int(11) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idActivity`)
) ENGINE=InnoDB AUTO_INCREMENT=10;





-- -----------------------------------------------------
-- Table `campaign`
-- -----------------------------------------------------
CREATE TABLE if not exists `campaign` (
  `idcampaign` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(250) NOT NULL DEFAULT '',
  `Campaign_Code` varchar(45) NOT NULL DEFAULT '',
  `Start_Date` date DEFAULT NULL,
  `End_Date` date DEFAULT NULL,
  `Target` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Min_Donation` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Max_Donation` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Category` varchar(15) NOT NULL DEFAULT '',
  `Status` varchar(5) NOT NULL DEFAULT '',
  `Campaign_Merge_Code` varchar(15) NOT NULL DEFAULT '',
  `Description` text,
  `Lump_Sum_Cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Percent_Cut` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Our_Cut_PerTx` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Campaign_Type` varchar(5) NOT NULL DEFAULT '',
  `Updated_By` varchar(25) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcampaign`),
  UNIQUE KEY `Campaign_Code_UNIQUE` (`Campaign_Code`),
  KEY `idxType` (`Campaign_Code`)
) ENGINE=InnoDB AUTO_INCREMENT=10;



-- -----------------------------------------------------
-- Table `card_id`
-- -----------------------------------------------------
CREATE TABLE if not exists `card_id` (
  `idName` int(11) NOT NULL,
  `idGroup` int(11) NOT NULL,
  `CardID` varchar(36) NOT NULL DEFAULT '',
  `Init_Date` datetime DEFAULT NULL,
  `ReturnCode` int(11) NOT NULL DEFAULT '0',
  `Frequency` varchar(9) NOT NULL DEFAULT '',
  `OperatorID` varchar(10) NOT NULL DEFAULT '',
  `ResponseCode` int(11) NOT NULL DEFAULT '0',
  `Transaction` varchar(4) NOT NULL DEFAULT '',
  `InvoiceNumber` varchar(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`idName`,`idGroup`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `cc_hosted_gateway`
-- -----------------------------------------------------
CREATE TABLE if not exists `cc_hosted_gateway` (
  `idcc_gateway` int(11) NOT NULL AUTO_INCREMENT,
  `cc_name` varchar(45) NOT NULL,
  `Merchant_Id` varchar(45) NOT NULL DEFAULT '',
  `Password` varchar(45) NOT NULL DEFAULT '',
  `Credit_Url` varchar(145) NOT NULL DEFAULT '',
  `Trans_Url` varchar(145) NOT NULL DEFAULT '',
  `CardInfo_Url` varchar(145) NOT NULL DEFAULT '',
  `Checkout_Url` varchar(145) NOT NULL DEFAULT '',
  `Mobile_CardInfo_Url` varchar(145) NOT NULL DEFAULT '',
  `Mobile_Checkout_Url` varchar(145) NOT NULL DEFAULT '',
  `CheckoutPOS_Url` varchar(145) NOT NULL DEFAULT '',
  `CheckoutPOSiFrame_Url` varchar(145) NOT NULL DEFAULT '',
  `Use_AVS_Flag` bit(1) NOT NULL DEFAULT b'0',
  `Use_Ccv_Flag` bit(1) NOT NULL DEFAULT b'0',
  `Retry_Count` int(11) NOT NULL DEFAULT '0',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcc_gateway`)
) ENGINE=InnoDB AUTO_INCREMENT=1;






-- -----------------------------------------------------
-- Table `counter`
-- -----------------------------------------------------
CREATE TABLE if not exists `counter` (
  `seqn` int(11) NOT NULL,
  `Table_Name` varchar(75) NOT NULL,
  `Next` int(11) NOT NULL,
  `Last_Updated` datetime DEFAULT NULL,
  PRIMARY KEY (`seqn`),
  UNIQUE KEY `seqn_UNIQUE` (`seqn`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `desig_holidays`
-- -----------------------------------------------------
CREATE TABLE if not exists `desig_holidays` (
  `Year` int(11) NOT NULL,
  `dh1` date DEFAULT NULL,
  `dh2` date DEFAULT NULL,
  `dh3` date DEFAULT NULL,
  `dh4` date DEFAULT NULL,
  `dh5` date DEFAULT NULL,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  PRIMARY KEY (`Year`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `donations`
-- -----------------------------------------------------
CREATE TABLE if not exists `donations` (
  `iddonations` int(11) NOT NULL AUTO_INCREMENT,
  `Donor_Id` int(11) NOT NULL,
  `Care_Of_Id` int(11) NOT NULL DEFAULT '0',
  `Assoc_Id` int(11) NOT NULL DEFAULT '0',
  `Type` varchar(15) NOT NULL DEFAULT '',
  `Date_Entered` datetime DEFAULT NULL,
  `Trans_Date` datetime DEFAULT NULL,
  `Pay_Type` varchar(15) NOT NULL DEFAULT '',
  `Member_type` varchar(15) NOT NULL DEFAULT '',
  `Donation_Type` varchar(15) NOT NULL DEFAULT '',
  `Amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Matching_Amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `Note` varchar(255) NOT NULL DEFAULT '',
  `Salutation_Code` varchar(15) NOT NULL DEFAULT '',
  `Envelope_Code` varchar(15) NOT NULL DEFAULT '',
  `Address_1` varchar(145) NOT NULL DEFAULT '',
  `Address_2` varchar(145) NOT NULL DEFAULT '',
  `City` varchar(45) NOT NULL DEFAULT '',
  `State` varchar(5) NOT NULL DEFAULT '',
  `Postal_Code` varchar(45) NOT NULL DEFAULT '',
  `Country` varchar(45) NOT NULL DEFAULT '',
  `Address_Purpose` varchar(5) NOT NULL DEFAULT '',
  `Phone` varchar(25) NOT NULL DEFAULT '',
  `Email` varchar(145) NOT NULL DEFAULT '',
  `Fund_Code` varchar(15) NOT NULL DEFAULT '',
  `Org_Code` varchar(15) NOT NULL DEFAULT '',
  `Campaign_Code` varchar(45) NOT NULL DEFAULT '',
  `Activity_Id` int(11) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Date_Acknowledged` datetime DEFAULT NULL,
  `Status` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`iddonations`),
  KEY `Activity_Id_INDEX` (`Activity_Id`),
  KEY `Donor_Id_INDEX` (`Donor_Id`)
) ENGINE=InnoDB AUTO_INCREMENT=10;




-- -----------------------------------------------------
-- Table `dormant_schedules`
-- -----------------------------------------------------
CREATE TABLE if not exists `dormant_schedules` (
  `iddormant_schedules` int(11) NOT NULL AUTO_INCREMENT,
  `Begin_Active` date DEFAULT NULL,
  `End_Active` date DEFAULT NULL,
  `Begin_Dormant` date DEFAULT NULL,
  `End_Dormant` date DEFAULT NULL,
  `Title` varchar(45) NOT NULL DEFAULT '',
  `Code` varchar(45) NOT NULL DEFAULT '',
  `Status` varchar(5) NOT NULL DEFAULT '',
  `Description` text,
  `Updated_by` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iddormant_schedules`)
) ENGINE=InnoDB AUTO_INCREMENT=2;





-- -----------------------------------------------------
-- Table `fbx`
-- -----------------------------------------------------
CREATE TABLE if not exists `fbx` (
  `fb_id` varchar(45) NOT NULL,
  `idName` int(11) NOT NULL,
  `Status` varchar(2) NOT NULL DEFAULT '',
  `fb_username` varchar(145) NOT NULL DEFAULT '',
  `Approved_By` varchar(45) NOT NULL DEFAULT '',
  `Approved_Date` datetime DEFAULT NULL,
  `Dropped_Date` datetime DEFAULT NULL,
  `fb_Address_1` varchar(145) NOT NULL DEFAULT '',
  `fb_Address_2` varchar(45) NOT NULL DEFAULT '',
  `fb_City` varchar(45) NOT NULL DEFAULT '',
  `fb_State` varchar(45) NOT NULL DEFAULT '',
  `fb_Zip` varchar(15) NOT NULL DEFAULT '',
  `fb_First_Name` varchar(45) NOT NULL DEFAULT '',
  `fb_Last_Name` varchar(45) NOT NULL DEFAULT '',
  `fb_Phone` varchar(25) NOT NULL DEFAULT '',
  `fb_Email` varchar(145) NOT NULL DEFAULT '',
  `PIFH_Username` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Enc_Password` varchar(100) NOT NULL DEFAULT '',
  `Access_Code` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`fb_id`)
) ENGINE=InnoDB;





-- -----------------------------------------------------
-- Table `gateway_transaction`
-- -----------------------------------------------------
CREATE TABLE if not exists `gateway_transaction` (
  `idgateway_transaction` int(11) NOT NULL AUTO_INCREMENT,
  `GwTransCode` varchar(64) NOT NULL DEFAULT '',
  `GwResultCode` varchar(44) NOT NULL DEFAULT '',
  `Amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Vendor_Request` varchar(900) NOT NULL DEFAULT '',
  `Vendor_Response` varchar(1000) NOT NULL DEFAULT '',
  `AuthCode` varchar(45) NOT NULL DEFAULT '',
  `idPayment_Detail` int(11) NOT NULL DEFAULT '0',
  `Created_By` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idgateway_transaction`)
) ENGINE=InnoDB AUTO_INCREMENT=7;





-- -----------------------------------------------------
-- Table `gen_lookups`
-- -----------------------------------------------------
CREATE TABLE if not exists `gen_lookups` (
  `Table_Name` varchar(45) NOT NULL,
  `Code` varchar(65) NOT NULL DEFAULT '',
  `Description` varchar(255) NOT NULL DEFAULT '',
  `Substitute` varchar(255) NOT NULL DEFAULT '',
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Table_Name`,`Code`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `gen_securitygroup`
-- -----------------------------------------------------
CREATE TABLE if not exists `gen_securitygroup` (
  `idSec` int(11) NOT NULL,
  `Group_Code` varchar(5) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSec`,`Group_Code`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `guest_token`
-- -----------------------------------------------------
CREATE TABLE if not exists `guest_token` (
  `idGuest_token` int(11) NOT NULL AUTO_INCREMENT,
  `idGuest` int(11) NOT NULL DEFAULT '0',
  `idReservation` int(11) NOT NULL DEFAULT '0',
  `idVisit` int(11) NOT NULL DEFAULT '0',
  `idRegistration` int(11) NOT NULL DEFAULT '0',
  `Token` varchar(100) NOT NULL DEFAULT '',
  `Granted_Date` datetime DEFAULT NULL,
  `LifetimeDays` int(11) NOT NULL DEFAULT '0',
  `MaskedAccount` varchar(15) NOT NULL DEFAULT '',
  `Frequency` varchar(15) NOT NULL DEFAULT '',
  `Status` varchar(10) NOT NULL DEFAULT '',
  `Response_Code` int(11) NOT NULL DEFAULT '1',
  `CardHolderName` varchar(32) NOT NULL DEFAULT '',
  `CardType` varchar(45) NOT NULL DEFAULT '',
  `CardUsage` varchar(20) NOT NULL DEFAULT '',
  `ExpDate` varchar(4) NOT NULL DEFAULT '',
  `OperatorID` varchar(10) NOT NULL DEFAULT '',
  `Tran_Type` varchar(10) NOT NULL DEFAULT '',
  `StatusMessage` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idGuest_token`)
) ENGINE=InnoDB AUTO_INCREMENT=7;




-- -----------------------------------------------------
-- Table `house`
-- -----------------------------------------------------
CREATE  TABLE if not exists `house` (
  `idHouse` INT NOT NULL AUTO_INCREMENT ,
  `Title` VARCHAR(45) NOT NULL DEFAULT '' ,
  `Description` VARCHAR(145) NOT NULL DEFAULT '' ,
  `idLocation` INT NOT NULL DEFAULT 0 ,
  `Timestamp` TIMESTAMP NOT NULL DEFAULT  CURRENT_TIMESTAMP ,
  PRIMARY KEY (`idHouse`)
  ) ENGINE = InnoDB;



-- -----------------------------------------------------
-- Table `house_log`
-- -----------------------------------------------------
CREATE  TABLE if not exists `house_log` (
  `Log_Type` VARCHAR(45) NOT NULL DEFAULT '' ,
  `Sub_Type` VARCHAR(45) NOT NULL DEFAULT '' ,
  `User_Name` VARCHAR(45) NOT NULL DEFAULT '' ,
  `Id1` INT NOT NULL DEFAULT 0 ,
  `Id2` INT NOT NULL DEFAULT 0 ,
  `Id3` INT NOT NULL DEFAULT 0 ,
  `Id4` INT NOT NULL DEFAULT 0 ,
  `Log_Text` VARCHAR(5000) NOT NULL DEFAULT '' ,
  `Timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
 ) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `id_securitygroup`
-- -----------------------------------------------------
CREATE TABLE if not exists `id_securitygroup` (
  `idName` int(11) NOT NULL,
  `Group_Code` varchar(5) NOT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName`,`Group_Code`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `invoice`
-- -----------------------------------------------------
CREATE TABLE if not exists `invoice` (
  `idInvoice` int(11) NOT NULL AUTO_INCREMENT,
  `Delegated_Invoice_Id` int(11) NOT NULL DEFAULT '0',
  `Invoice_Number` varchar(45) NOT NULL,
  `Billing_Process_Id` int(11) NOT NULL DEFAULT '0',
  `Deleted` SMALLINT default 0 NOT NULL,
  `Amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Sold_To_Id` int(11) NOT NULL DEFAULT '0',
  `idGroup` int(11) NOT NULL DEFAULT '0',
  `Invoice_Date` datetime DEFAULT NULL,
  `Payment_Attempts` int(11) NOT NULL DEFAULT '0',
  `Status` varchar(5) NOT NULL DEFAULT '',
  `Carried_Amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Order_Number` varchar(45) NOT NULL DEFAULT '',
  `Due_Date` date DEFAULT NULL,
  `In_Process_Payment` tinyint(4) NOT NULL DEFAULT '0',
  `Last_Reminder` DATETIME,
  `Overdue_Step` INTEGER NOT NULL DEFAULT '0',
  `Description` varchar(45) NOT NULL DEFAULT '',
  `Notes` varchar(450) NOT NULL DEFAULT '',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idInvoice`),
  UNIQUE KEY `Invoice_Number_UNIQUE` (`Invoice_Number`)
) ENGINE=InnoDB AUTO_INCREMENT=7;


-- -----------------------------------------------------
-- Table `invoice_line`
-- -----------------------------------------------------
CREATE TABLE if not exists `invoice_line`
(
    `idInvoice_Line` INTEGER NOT NULL AUTO_INCREMENT,
    `Invoice_Id` INTEGER NOT NULL DEFAULT '0',
    `Type_Id` INTEGER NOT NULL DEFAULT '0',
    `Amount` DECIMAL(22,10) NOT NULL,
    `Quantity` DECIMAL(22,10) NOT NULL DEFAULT '0.00',
    `Price` DECIMAL(22,10) NOT NULL DEFAULT '0.00',
    `Deleted` SMALLINT NOT NULL default 0,
    `Item_Id` INTEGER NOT NULL default 0,
    `Description` VARCHAR(1000) NOT NULL default '',
    `Source_User_Id` INTEGER NOT NULL default 0,
    `Is_Percentage` SMALLINT NOT NULL default 0,
    `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`idInvoice_Line`),
    INDEX `ix_invoice_line_invoice_id` (Invoice_Id)
) ENGINE=InnoDB AUTO_INCREMENT=7;



-- -----------------------------------------------------
-- Table `invoice_line_type`
-- -----------------------------------------------------
CREATE TABLE if not exists `invoice_line_type`
(
    `idInvoice_Line_Type` INTEGER NOT NULL AUTO_INCREMENT,
    `Description` VARCHAR(50) NOT NULL,
    `Order_Position` INTEGER NOT NULL,
    PRIMARY KEY(`idInvoice_Line_Type`)
) ENGINE=InnoDB AUTO_INCREMENT=7;


-- -----------------------------------------------------
-- Table `item`
-- -----------------------------------------------------
CREATE TABLE if not exists `item`
(
    `idItem` INTEGER NOT NULL AUTO_INCREMENT,
    `Internal_Number` VARCHAR(50) NOT NULL default '',
    `Entity_Id` INTEGER NOT NULL DEFAULT 0,
    `Percentage` DECIMAL(22,10) NOT NULL DEFAULT '0.00',
    `Deleted` SMALLINT default 0 NOT NULL DEFAULT '0',
    `Has_Decimals` SMALLINT default 0 NOT NULL DEFAULT '0',
    `Gl_Code` VARCHAR(50) NOT NULL default '',
    `Description` VARCHAR(1000) NOT NULL default '',
    PRIMARY KEY(`idItem`)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table `item_type`
-- -----------------------------------------------------
CREATE TABLE if not exists `item_type`
(
    `idItem_Type` INTEGER NOT NULL AUTO_INCREMENT,
    `Category_Type` INTEGER NOT NULL,
    `Description` VARCHAR(100),
    `Internal` BIT NOT NULL,
    `Order_Line_Type_Id` INTEGER NOT NULL,
    PRIMARY KEY(`idItem_Type`)
) ENGINE=InnoDB;

# -----------------------------------------------------------------------
# Table item_type_map
# -----------------------------------------------------------------------
CREATE TABLE if not exists `item_type_map`
(
    `Item_Id` INTEGER,
    `Type_Id` INTEGER);



-- -----------------------------------------------------
-- Table `location`
-- -----------------------------------------------------
CREATE TABLE if not exists `location` (
  `idLocation` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(45) DEFAULT '',
  `Description` varchar(245) DEFAULT '',
  `Status` varchar(5) DEFAULT '',
  `Address` varchar(145) NOT NULL DEFAULT '',
  `Phone` varchar(45) NOT NULL DEFAULT '',
  `Map` varchar(510) NOT NULL DEFAULT '',
  `Owner_Id` int(11) NOT NULL DEFAULT '0',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLocation`)
) ENGINE=InnoDB AUTO_INCREMENT=10;



-- -----------------------------------------------------
-- Table `lookups`
-- -----------------------------------------------------
CREATE TABLE if not exists `lookups` (
  `Category` varchar(45) NOT NULL,
  `Code` varchar(45) NOT NULL,
  `Title` varchar(255) NOT NULL DEFAULT '',
  `Use` varchar(2) NOT NULL DEFAULT '',
  `Show` varchar(4) NOT NULL DEFAULT '',
  `Type` varchar(255) NOT NULL DEFAULT '',
  `Other` varchar(255) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Category`,`Code`)
) ENGINE=InnoDB;





-- -----------------------------------------------------
-- Table `mail_listing`
-- -----------------------------------------------------
drop table if exists `mail_listing`;
CREATE TABLE if not exists `mail_listing` (
  `id` int(11) NOT NULL,
  `mr` varchar(5) NOT NULL DEFAULT '',
  `adr_frag` varchar(200) NOT NULL DEFAULT '',
  `street` varchar(200) NOT NULL DEFAULT '',
  `city` varchar(45) NOT NULL DEFAULT '',
  `state` varchar(45) NOT NULL DEFAULT '',
  `zip` varchar(15) NOT NULL DEFAULT '',
  `sp` int(11) NOT NULL DEFAULT '0',
  `fm` int(11) NOT NULL DEFAULT '0',
  `rel` varchar(5) NOT NULL DEFAULT '',
  `cde` varchar(5) NOT NULL DEFAULT ''
) ENGINE=MyISAM;




-- -----------------------------------------------------
-- Table `mcalendar`
-- -----------------------------------------------------
CREATE TABLE if not exists `mcalendar` (
  `idmcalendar` int(11) NOT NULL AUTO_INCREMENT,
  `E_Id` varchar(100) NOT NULL DEFAULT '',
  `idName` int(11) NOT NULL DEFAULT '0',
  `idName2` int(11) NOT NULL DEFAULT '0',
  `E_Title` varchar(45) NOT NULL DEFAULT '',
  `E_Start` datetime NOT NULL,
  `E_End` datetime DEFAULT NULL,
  `E_URL` varchar(145) NOT NULL DEFAULT '',
  `E_ClassName` varchar(45) NOT NULL DEFAULT '',
  `E_Editable` bit(1) NOT NULL DEFAULT b'0',
  `E_Source` varchar(244) NOT NULL DEFAULT '',
  `E_Description` text,
  `E_AllDay` bit(1) NOT NULL DEFAULT b'0',
  `E_Vol_Category` varchar(45) NOT NULL DEFAULT '',
  `E_Vol_Code` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Updated_By` varchar(100) NOT NULL DEFAULT '',
  `E_Status` varchar(4) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `E_Take_Overable` bit(1) NOT NULL DEFAULT b'0',
  `E_Fixed_In_Time` bit(1) NOT NULL DEFAULT b'0',
  `E_Shell` bit(1) NOT NULL DEFAULT b'0',
  `E_Locked` bit(1) NOT NULL DEFAULT b'0',
  `E_Shell_Id` int(11) NOT NULL DEFAULT '0',
  `E_Rpt_Id` int(11) NOT NULL DEFAULT '0',
  `E_Show_All` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`idmcalendar`),
  KEY `idName_INDEX` (`idName`)
) ENGINE=InnoDB AUTO_INCREMENT=3;




-- -----------------------------------------------------
-- Table `member_history`
-- -----------------------------------------------------
CREATE TABLE if not exists `member_history` (
  `idName` int(11) NOT NULL,
  `Admin_Access_Date` datetime DEFAULT NULL,
  `Guest_Access_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`idName`)
) ENGINE=InnoDB;


-- -----------------------------------------------------
-- Table `money`
-- -----------------------------------------------------
CREATE TABLE if not exists `money` (
  `idMoney` int(11) NOT NULL AUTO_INCREMENT,
  `Amount` decimal(10,2) NOT NULL,
  `idPayment` int(11) NOT NULL DEFAULT '0',
  `idInvoice` int(11) NOT NULL DEFAULT '0',
  `idInvoiceLine` int(11) NOT NULL DEFAULT '0',
  `Invoice_Number` varchar(45) NOT NULL DEFAULT '',
  `Discount_Taken` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Order_Number` varchar(45) NOT NULL DEFAULT '',
  `OrderLine_Number` int(11) NOT NULL DEFAULT '0',
  `Transaction_Date` datetime DEFAULT NULL,
  `Transaction_Type` varchar(5) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idMoney`)
) ENGINE=InnoDB AUTO_INCREMENT=3;




-- -----------------------------------------------------
-- Table `name`
-- -----------------------------------------------------
CREATE TABLE if not exists `name` (
  `idName` int(11) NOT NULL AUTO_INCREMENT,
  `Name_First` varchar(45) NOT NULL DEFAULT '',
  `Name_Last` varchar(45) NOT NULL DEFAULT '',
  `Name_Middle` varchar(45) NOT NULL DEFAULT '',
  `Name_Nickname` varchar(45) NOT NULL DEFAULT '',
  `Name_Full` varchar(170) NOT NULL DEFAULT '',
  `Name_Previous` varchar(45) NOT NULL DEFAULT '',
  `Web_Site` varchar(145) NOT NULL DEFAULT '',
  `Member_Since` datetime DEFAULT NULL,
  `Member_Type` varchar(15) NOT NULL DEFAULT '',
  `Member_Status` varchar(15) NOT NULL DEFAULT '',
  `Member_Category` varchar(45) NOT NULL DEFAULT '',
  `Preferred_Mail_Address` varchar(5) NOT NULL DEFAULT '',
  `Preferred_Email` varchar(5) NOT NULL DEFAULT '',
  `Preferred_Phone` varchar(5) NOT NULL DEFAULT '',
  `Organization_Code` varchar(15) NOT NULL DEFAULT '',
  `Company_Id` int(11) NOT NULL DEFAULT '0',
  `Title` varchar(75) NOT NULL DEFAULT '',
  `Company` varchar(80) NOT NULL DEFAULT '',
  `Company_CareOf` varchar(4) NOT NULL DEFAULT '',
  `Record_Member` bit(1) NOT NULL DEFAULT b'0',
  `Record_Company` bit(1) NOT NULL DEFAULT b'0',
  `Previous_Member_Type` varchar(15) NOT NULL DEFAULT '',
  `Prev_MT_Change_Date` datetime DEFAULT NULL,
  `Last_Updated` datetime DEFAULT NULL,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Exclude_Directory` bit(1) NOT NULL DEFAULT b'0',
  `Exclude_Mail` bit(1) NOT NULL DEFAULT b'0',
  `Exclude_Email` bit(1) NOT NULL DEFAULT b'0',
  `Exclude_Phone` bit(1) NOT NULL DEFAULT b'0',
  `Date_Added` datetime DEFAULT NULL,
  `Gender` varchar(2) NOT NULL DEFAULT '',
  `BirthDate` datetime DEFAULT NULL,
  `Member_Status_Date` datetime DEFAULT NULL,
  `Name_Suffix` varchar(10) NOT NULL DEFAULT '',
  `Name_Prefix` varchar(25) NOT NULL DEFAULT '',
  `Name_Last_First` varchar(90) NOT NULL DEFAULT '',
  `Birth_Month` int(11) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName`),
  UNIQUE KEY `idName_UNIQUE` (`idName`),
  KEY `iNameLastFirst` (`Name_Last_First`)
) ENGINE=InnoDB AUTO_INCREMENT=10;





-- -----------------------------------------------------
-- Table `name_address`
-- -----------------------------------------------------
CREATE TABLE if not exists `name_address` (
  `idName_Address` int(11) NOT NULL AUTO_INCREMENT,
  `idName` int(11) NOT NULL,
  `Purpose` varchar(25) NOT NULL DEFAULT '',
  `Company` varchar(80) NOT NULL DEFAULT '',
  `Address_1` varchar(200) NOT NULL DEFAULT '',
  `Address_2` varchar(45) NOT NULL DEFAULT '',
  `City` varchar(45) NOT NULL DEFAULT '',
  `State_Province` varchar(45) NOT NULL DEFAULT '',
  `Postal_Code` varchar(15) NOT NULL DEFAULT '',
  `Country` varchar(45) NOT NULL DEFAULT '',
  `Country_Code` varchar(10) NOT NULL DEFAULT '',
  `Address_Format` bit(1) NOT NULL DEFAULT b'0',
  `County` varchar(45) NOT NULL DEFAULT '',
  `Full_Address` varchar(255) NOT NULL DEFAULT '',
  `Mail_Code` varchar(5) NOT NULL DEFAULT '',
  `Phone` varchar(25) NOT NULL DEFAULT '',
  `Status` varchar(15) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Preferred_Mail` bit(1) NOT NULL DEFAULT b'0',
  `Last_Verified` datetime DEFAULT NULL,
  `Bad_Address` varchar(15) NOT NULL DEFAULT '',
  `Fax` varchar(25) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName_Address`),
  KEY `iNA_ID` (`idName`)
) ENGINE=InnoDB AUTO_INCREMENT=10;



-- -----------------------------------------------------
-- Table `name_crypto`
-- -----------------------------------------------------
CREATE TABLE if not exists `name_crypto` (
  `idname_crypto` int(11) NOT NULL AUTO_INCREMENT,
  `idName` varchar(45) NOT NULL,
  `Gen_Notes` text,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Contact_Date` date DEFAULT NULL,
  `Created_On` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idname_crypto`),
  KEY `name_Indx` (`idName`)
) ENGINE=InnoDB AUTO_INCREMENT=10;



-- -----------------------------------------------------
-- Table `name_customer`
-- -----------------------------------------------------
CREATE TABLE if not exists `name_customer` (
  `idName_customer` int(11) NOT NULL AUTO_INCREMENT,
  `idName` int(11) NOT NULL,
  `idGroup`  int(11) NOT NULL DEFAULT 0,
  `Auto_Payment_Type` int(11) NOT NULL DEFAULT 0,
  `Due_Date_Unit_Id` int(11) NOT NULL DEFAULT 0,
  `Due_Date_Value` int(11) NOT NULL DEFAULT 0,
  `Current_Order_Id` int(11) NOT NULL DEFAULT 0,
  `Balance_Type` int(11) NOT NULL DEFAULT 0,
  `Dynamic_Balance` decimal(12,6) NOT NULL DEFAULT '0.00',
  `Credit_Limit` decimal(12,6) NOT NULL DEFAULT '0.00',
  `Auto_Recharage` decimal(12,6) NOT NULL DEFAULT '0.00',
  `Notes` varchar(1000) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName_customer`)
) ENGINE=InnoDB;




-- -----------------------------------------------------
-- Table `name_demog`
-- -----------------------------------------------------
CREATE TABLE if not exists `name_demog` (
  `idName` int(11) NOT NULL,
  `Steering_Committee` tinyint(1) NOT NULL DEFAULT '0',
  `Newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `Key_Contact` tinyint(1) NOT NULL DEFAULT '0',
  `Legislative` tinyint(1) NOT NULL DEFAULT '0',
  `Gen_Notes` text,
  `Contact_Date` date DEFAULT NULL,
  `Orientation` tinyint(1) NOT NULL DEFAULT '0',
  `Orientation_Date` date DEFAULT NULL,
  `Media_Source` varchar(5) NOT NULL DEFAULT '',
  `Cert_1` tinyint(1) NOT NULL DEFAULT '0',
  `Cert_1_Title` varchar(45) NOT NULL DEFAULT '',
  `Cert_2` tinyint(1) NOT NULL DEFAULT '0',
  `Cert_2_Title` varchar(45) NOT NULL DEFAULT '',
  `Cert_3` tinyint(1) NOT NULL DEFAULT '0',
  `Cert_3_Title` varchar(45) NOT NULL DEFAULT '',
  `Cert_4` tinyint(1) NOT NULL DEFAULT '0',
  `Cert_4_Title` varchar(45) NOT NULL DEFAULT '',
  `Cert_5` tinyint(1) NOT NULL DEFAULT '0',
  `Cert_5_Title` varchar(45) NOT NULL DEFAULT '',
  `Cert_6` tinyint(1) NOT NULL DEFAULT '0',
  `Cert_6_Title` varchar(45) NOT NULL DEFAULT '',
  `Age_Bracket` varchar(5) NOT NULL DEFAULT '',
  `Race` varchar(45) NOT NULL DEFAULT '',
  `Ethnicity` varchar(45) NOT NULL DEFAULT '',
  `Income_Bracket` varchar(5) NOT NULL DEFAULT '',
  `Education_Level` varchar(5) NOT NULL DEFAULT '',
  `Special_Needs` varchar(5) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName`)
) ENGINE=InnoDB;




-- -----------------------------------------------------
-- Table `name_email`
-- -----------------------------------------------------
CREATE TABLE if not exists `name_email` (
  `idName` int(11) NOT NULL,
  `Purpose` varchar(25) NOT NULL DEFAULT '',
  `Email` varchar(140) NOT NULL DEFAULT '',
  `Bad_Address` varchar(15) NOT NULL DEFAULT '',
  `Last_Verified` date DEFAULT NULL,
  `Last_Updated` date DEFAULT NULL,
  `Updated_By` varchar(45) DEFAULT NULL,
  `Status` varchar(15) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName`,`Purpose`),
  KEY `idName_INDEX` (`idName`)
) ENGINE=InnoDB;





-- -----------------------------------------------------
-- Table `name_log`
-- -----------------------------------------------------
CREATE TABLE if not exists `name_log` (
  `Date_Time` datetime NOT NULL,
  `Log_Type` varchar(45) NOT NULL DEFAULT '',
  `Sub_Type` varchar(45) NOT NULL DEFAULT '',
  `WP_User_Id` varchar(45) NOT NULL DEFAULT '',
  `idName` varchar(15) NOT NULL DEFAULT '',
  `Log_Text` varchar(255) NOT NULL DEFAULT '',
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
);




-- -----------------------------------------------------
-- Table `name_phone`
-- -----------------------------------------------------
CREATE TABLE if not exists `name_phone` (
  `idName` int(11) NOT NULL,
  `Phone_Num` varchar(45) NOT NULL DEFAULT '',
  `Phone_Extension` varchar(15) NOT NULL DEFAULT '',
  `Phone_Code` varchar(5) NOT NULL,
  `Phone_Search` varchar(25) NOT NULL DEFAULT '',
  `Status` varchar(15) NOT NULL DEFAULT '',
  `is_Mobile` bit(1) NOT NULL DEFAULT b'0',
  `is_Toll_Free` bit(1) NOT NULL DEFAULT b'0',
  `is_International` bit(1) NOT NULL DEFAULT b'0',
  `is_Land_Line` bit(1) NOT NULL DEFAULT b'0',
  `is_Personal` bit(1) NOT NULL DEFAULT b'0',
  `is_Party_Line` bit(1) NOT NULL DEFAULT b'0',
  `is_SMS` bit(1) NOT NULL DEFAULT b'0',
  `Carrier` varchar(45) NOT NULL DEFAULT '',
  `Bad_Number` varchar(15) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Updated_By` varchar(45) DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName`,`Phone_Code`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `name_student`
-- -----------------------------------------------------
CREATE TABLE if not exists `name_student` (
  `idName` int(11) NOT NULL,
  `idSsg` int(11) NOT NULL,
  `Rel` varchar(4) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName`,`idSsg`)
) ENGINE=InnoDB;


-- -----------------------------------------------------
-- Table `name_volunteer2`
-- -----------------------------------------------------
CREATE TABLE if not exists `name_volunteer2` (
  `idName` int(11) NOT NULL,
  `Vol_Category` varchar(15) NOT NULL,
  `Vol_Code` varchar(5) NOT NULL,
  `Vol_Status` varchar(5) NOT NULL,
  `Vol_Availability` varchar(5) NOT NULL DEFAULT '',
  `Vol_Notes` text,
  `Vol_Begin` date DEFAULT NULL,
  `Vol_End` date DEFAULT NULL,
  `Vol_Check_Date` date DEFAULT NULL,
  `Dormant_Code` varchar(45) NOT NULL DEFAULT '',
  `Updated_By` varchar(25) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Vol_Rank` varchar(45) NOT NULL DEFAULT '',
  `Vol_Training_Date` date DEFAULT NULL,
  `Vol_License` varchar(25) NOT NULL DEFAULT '',
  `Vol_Trainer` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName`,`Vol_Category`,`Vol_Code`)
) ENGINE=InnoDB;



# -----------------------------------------------------------------------
# Table `order_line`
# -----------------------------------------------------------------------
CREATE TABLE if not exists `order_line`
(
    `idOrder_Line` INTEGER NOT NULL AUTO_INCREMENT,
    `Order_Id` INTEGER NOT NULL default 0,
    `Item_Id` INTEGER NOT NULL default 0,
    `Type_Id` INTEGER NOT NULL default 0,
    `Amount` DECIMAL(22,10) NOT NULL,
    `Quantity` DECIMAL(22,10) NOT NULL default 0,
    `Price` DECIMAL(22,10) NOT NULL default 0,
    `Item_Price` SMALLINT NOT NULL default 0,
    `Create_Datetime` TIMESTAMP NOT NULL,
    `Deleted` SMALLINT NOT NULL default 0,
    `Use_Item` SMALLINT NOT NULL default 0,
    `Description` VARCHAR(1000) NOT NULL default '',
    `Provisioning_Status` INTEGER NOT NULL default 0,
    `Provisioning_Request_Id` VARCHAR(50) NOT NULL default '',
    PRIMARY KEY(`idOrder_Line`),
    INDEX ix_order_line_order_id (`Order_Id`),
    INDEX ix_order_line_item_id (`Item_Id`)
) AUTO_INCREMENT=5;

# -----------------------------------------------------------------------
# Table `order_line_type`
# -----------------------------------------------------------------------
CREATE TABLE if not exists `order_line_type`
(
    `idOrder_Line_Type` INTEGER NOT NULL AUTO_INCREMENT,
    `Editable` SMALLINT NOT NULL,
    Description  VARCHAR(50) NOT NULL default '',
    PRIMARY KEY(`idOrder_Line_Type`)
) AUTO_INCREMENT=5;


# -----------------------------------------------------------------------
# Table `order_period`
# -----------------------------------------------------------------------
CREATE TABLE if not exists `order_period`
(
    `idOrder_Period` INTEGER NOT NULL,
    `Entity_Id` INTEGER NOT NULL default 0,
    `Value` INTEGER NOT NULL default 0,
    `Unit_Id` INTEGER NOT NULL default 0,
    Description  VARCHAR(50) NOT NULL default '',
    PRIMARY KEY(`idOrder_Period`)
) ;


# -----------------------------------------------------------------------
# Table `order_process`
# -----------------------------------------------------------------------
CREATE TABLE if not exists `order_process`
(
    idOrder_Process INTEGER NOT NULL AUTO_INCREMENT,
    Order_Id INTEGER NOT NULL default 0,
    Invoice_Id INTEGER NOT NULL default 0,
    Billing_Process_Id INTEGER NOT NULL default 0,
    Periods_Included INTEGER NOT NULL default 0,
    Period_Start DATETIME,
    Period_End DATETIME,
    Is_Review INTEGER NOT NULL,
    Origin INTEGER NOT NULL default 0,
    PRIMARY KEY(idOrder_Process),
    INDEX ix_uq_order_process_or_in (Order_Id, Invoice_Id),
    INDEX ix_uq_order_process_or_bp (Order_Id, Billing_Process_Id),
    INDEX ix_order_process_in (Invoice_Id)
) AUTO_INCREMENT=5;


-- -----------------------------------------------------
-- Table `page`
-- -----------------------------------------------------
CREATE TABLE if not exists `page` (
  `idPage` int(11) NOT NULL AUTO_INCREMENT,
  `File_Name` varchar(65) NOT NULL,
  `Login_Page_Id` int(11) NOT NULL DEFAULT '0',
  `Title` varchar(45) NOT NULL DEFAULT '',
  `Web_Site` varchar(5) NOT NULL DEFAULT '',
  `Menu_Parent` varchar(45) NOT NULL DEFAULT '',
  `Menu_Position` varchar(45) NOT NULL DEFAULT '',
  `Type` varchar(5) NOT NULL DEFAULT '',
  `Validity_Code` varchar(75) NOT NULL DEFAULT '',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPage`)
) ENGINE=InnoDB AUTO_INCREMENT=5;




-- -----------------------------------------------------
-- Table `page_securitygroup`
-- -----------------------------------------------------
CREATE TABLE if not exists `page_securitygroup` (
  `idPage` int(11) NOT NULL,
  `Group_Code` varchar(5) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPage`,`Group_Code`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `payment`
-- -----------------------------------------------------
CREATE TABLE if not exists `payment` (
  `idPayment` int(11) NOT NULL AUTO_INCREMENT,
  `Attempt` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Result` varchar(4) NOT NULL DEFAULT '',
  `Financial_Entity_Id` int(11) NOT NULL DEFAULT '0',
  `Payment_Date` datetime DEFAULT NULL,
  `idPayor` int(11) NOT NULL DEFAULT '0',
  `idPayment_Method` int(11) NOT NULL DEFAULT '0',
  `idTrans` int(11) NOT NULL DEFAULT '0',
  `idToken` int(11) NOT NULL DEFAULT '0',
  `Is_Refund` tinyint(4) NOT NULL DEFAULT '0',
  `Is_Preauth` tinyint(4) NOT NULL DEFAULT '0',
  `Status_Code` varchar(5) NOT NULL DEFAULT '',
  `Created_By` varchar(45) NOT NULL DEFAULT '',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPayment`)
) ENGINE=InnoDB AUTO_INCREMENT=7;



-- -----------------------------------------------------
-- Table `payment_auth`
-- -----------------------------------------------------
CREATE TABLE if not exists `payment_auth` (
  `idPayment_auth` int(11) NOT NULL AUTO_INCREMENT,
  `idPayment` int(11) NOT NULL DEFAULT '0',
  `idTrans` int(11) NOT NULL DEFAULT '0',
  `Processor` varchar(45) NOT NULL DEFAULT '',
  `Approved_Amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Approval_Code` varchar(20) NOT NULL DEFAULT '',
  `AVS` varchar(20) NOT NULL DEFAULT '',
  `Invoice_Number` varchar(45) NOT NULL DEFAULT '',
  `Acct_Number` VARCHAR(25) NOT NULL DEFAULT '',
  `Card_Type` VARCHAR(10) NOT NULL DEFAULT '',
  `Customer_Id` varchar(45) NOT NULL DEFAULT '',
  `Response_Message` varchar(200) NOT NULL DEFAULT '',
  `Reference_Num` varchar(45) NOT NULL DEFAULT '',
  `Code1` varchar(200) NOT NULL DEFAULT '',
  `Code2` varchar(200) NOT NULL DEFAULT '',
  `Code3` varchar(45) NOT NULL DEFAULT '',
  `Serialized_Details` varchar(1000) NOT NULL DEFAULT '',
  `Status_Code` varchar(5) NOT NULL DEFAULT '',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPayment_auth`)
) ENGINE=InnoDB AUTO_INCREMENT=7;



# -----------------------------------------------------------------------
# Table payment_invoice
# -----------------------------------------------------------------------
CREATE TABLE if not exists `payment_invoice`
(
    `idPayment_Invoice` INTEGER NOT NULL AUTO_INCREMENT,
    `Payment_Id` INTEGER NOT NULL DEFAULT 0,
    `Invoice_Id` INTEGER NOT NULL DEFAULT 0,
    `Amount` DECIMAL(22,10) NOT NULL DEFAULT 0.00,
    `Create_Datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`idPayment_Invoice`),
    INDEX ix_uq_payment_inv_map_pa_in (Payment_Id, Invoice_Id)
) AUTO_INCREMENT=4;



-- -----------------------------------------------------
-- Table `payment_method`
-- -----------------------------------------------------
CREATE TABLE if not exists `payment_method` (
  `idPayment_method` int(11) NOT NULL AUTO_INCREMENT,
  `Method_Name` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idPayment_method`)
) ENGINE=InnoDB AUTO_INCREMENT=4;





-- -----------------------------------------------------
-- Table `postal_codes`
-- -----------------------------------------------------
CREATE  TABLE if not exists `postal_codes` (
  `Zip_Code` VARCHAR(9) NOT NULL ,
  `City` VARCHAR(45) NOT NULL DEFAULT '' ,
  `State` VARCHAR(4) NOT NULL DEFAULT '' ,
  `Lat` VARCHAR(15) NOT NULL DEFAULT '' ,
  `Lng` VARCHAR(15) NOT NULL DEFAULT '' ,
  `Type` VARCHAR(15) NOT NULL DEFAULT '' ,
  `Acceptable_Cities` VARCHAR(511) NOT NULL DEFAULT '' ,
  PRIMARY KEY (`Zip_Code`) )
DEFAULT CHARACTER SET = utf8;





# -----------------------------------------------------------------------
# Table purchase_order
# -----------------------------------------------------------------------
CREATE TABLE if not exists `purchase_order` (
    `idPurchase_Order` INTEGER NOT NULL AUTO_INCREMENT,
    `User_Id` INTEGER NOT NULL default 0,
    `Group_Id` INTEGER NOT NULL default 0,
    `Period_Id` INTEGER NOT NULL default 0,
    `Billing_Type_Id` INTEGER NOT NULL,
    `Active_Since` DATETIME NULL,
    `Active_Until` DATETIME NULL,
    `Cycle_Start` DATETIME NULL,
    `Next_Billable_Day` DATETIME NULL,
    `Created_By` INTEGER NOT NULL default 0,
    `Status_Id` INTEGER NOT NULL,
    `Deleted` SMALLINT NOT NULL default 0,
    `Notify` SMALLINT NOT NULL default 0,
    `Last_Notified` DATETIME NULL,
    `Notification_Step` INTEGER NOT NULL default 0,
    `Anticipate_Periods` INTEGER NOT NULL default 0,
    `Notes` VARCHAR(200) NOT NULL DEFAULT '',
    `Notes_In_Invoice` SMALLINT NOT NULL default 0,
    `Is_Current` SMALLINT NOT NULL default 0,
    `Timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`idPurchase_Order`)
) ENGINE = InnoDB;




-- -----------------------------------------------------
-- Table `receipt`
-- -----------------------------------------------------
CREATE TABLE if not exists `receipt` (
  `idReceipt` int(11) NOT NULL AUTO_INCREMENT,
  `Receipt_Number` varchar(20) NOT NULL DEFAULT '',
  `Date_Issued` datetime DEFAULT NULL,
  `Issued_To_Id` int(11) NOT NULL DEFAULT '0',
  `Notes` varchar(250) NOT NULL DEFAULT '',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idReceipt`)
) ENGINE=InnoDB AUTO_INCREMENT=7;







-- -----------------------------------------------------
-- Table `relationship`
-- -----------------------------------------------------
CREATE TABLE if not exists `relationship` (
  `idRelationship` int(11) NOT NULL AUTO_INCREMENT,
  `idName` int(11) NOT NULL,
  `Target_Id` int(11) NOT NULL DEFAULT '0',
  `Relation_Type` varchar(5) NOT NULL DEFAULT '',
  `Status` varchar(45) NOT NULL DEFAULT '',
  `Principal` bit(1) NOT NULL DEFAULT b'0',
  `Effective_Date` date DEFAULT NULL,
  `Thru_date` date DEFAULT NULL,
  `Note` text,
  `Date_Added` datetime DEFAULT NULL,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Group_Code` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idRelationship`),
  UNIQUE KEY `idRelationship_UNIQUE` (`idRelationship`)
) ENGINE=InnoDB AUTO_INCREMENT=10;




-- -----------------------------------------------------
-- Table `scholarship`
-- -----------------------------------------------------
CREATE TABLE if not exists `scholarship` (
  `idscholarship` int(11) NOT NULL AUTO_INCREMENT,
  `idName` int(11) NOT NULL,
  `idDonation` int(11) NOT NULL,
  `Fund_Code` int(11) NOT NULL DEFAULT '0',
  `Original_Amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Is_Deleted` INT NOT NULL DEFAULT 0,
  `Last_Updated` datetime DEFAULT NULL,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idscholarship`)
) ENGINE=InnoDB AUTO_INCREMENT=10;



-- -----------------------------------------------------
-- Table `scholarship_log`
-- -----------------------------------------------------
CREATE TABLE if not exists `scholarship_log` (
  `Log_Date` datetime NOT NULL,
  `idscholarship` int(11) NOT NULL,
  `Orig_Bal` decimal(12,2) NOT NULL,
  `Target_Fund_Code` int(11) NOT NULL,
  `Amount_Taken` decimal(12,2) NOT NULL,
  `Updated By` varchar(45) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM;



-- -----------------------------------------------------
-- Table `secondary_unit_desig`
-- -----------------------------------------------------
CREATE TABLE if not exists `secondary_unit_desig` (
  `Common` varchar(45) NOT NULL,
  `Standard` varchar(6) NOT NULL,
  `Range_Required` bit(1) NOT NULL,
  `TitleCaps` varchar(6) NOT NULL
) ENGINE=InnoDB;




-- -----------------------------------------------------
-- Table `shell_events`
-- -----------------------------------------------------
CREATE TABLE if not exists `shell_events` (
  `idShell` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(45) NOT NULL DEFAULT '',
  `Description` varchar(145) NOT NULL DEFAULT '',
  `Vol_Cat` varchar(45) NOT NULL DEFAULT '',
  `Vol_Code` varchar(45) NOT NULL DEFAULT '',
  `Time_Start` time DEFAULT NULL,
  `Time_End` time DEFAULT NULL,
  `Date_Start` date DEFAULT NULL,
  `Duration_Code` varchar(4) NOT NULL DEFAULT '',
  `Sun` bit(1) NOT NULL DEFAULT b'0',
  `Mon` bit(1) NOT NULL DEFAULT b'0',
  `Tue` bit(1) NOT NULL DEFAULT b'0',
  `Wed` bit(1) NOT NULL DEFAULT b'0',
  `Thu` bit(1) NOT NULL DEFAULT b'0',
  `Fri` bit(1) NOT NULL DEFAULT b'0',
  `Sat` bit(1) NOT NULL DEFAULT b'0',
  `Skip_Holidays` bit(1) NOT NULL DEFAULT b'0',
  `AllDay` bit(1) NOT NULL DEFAULT b'0',
  `Class_Name` varchar(45) NOT NULL DEFAULT '',
  `URL` varchar(145) NOT NULL DEFAULT '',
  `Status` varchar(4) NOT NULL DEFAULT '',
  `Shell_Color` varchar(45) NOT NULL DEFAULT '',
  `Fixed_In_Time` bit(1) NOT NULL DEFAULT b'0',
  `Take_Overable` bit(1) NOT NULL DEFAULT b'0',
  `Locked` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`idShell`)
) ENGINE=InnoDB AUTO_INCREMENT=3;




-- -----------------------------------------------------
-- Table `ssg`
-- -----------------------------------------------------
CREATE TABLE if not exists `ssg` (
  `idSsg` int(11) NOT NULL AUTO_INCREMENT,
  `Fund_Code` int(11) NOT NULL DEFAULT '0',
  `Title` varchar(145) NOT NULL DEFAULT '',
  `idStudent` int(11) NOT NULL,
  `Start_Date` date DEFAULT NULL,
  `Graduation_Date` date DEFAULT NULL,
  `IsFunded` varchar(4) NOT NULL DEFAULT '',
  `Status` varchar(4) NOT NULL DEFAULT '',
  `Max_Amount` decimal(10,2) DEFAULT '0.00',
  `Current_Amount` decimal(10,2) DEFAULT '0.00',
  `Last_Updated` datetime DEFAULT NULL,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idSsg`)
) ENGINE=InnoDB AUTO_INCREMENT=6;




-- -----------------------------------------------------
-- Table `street_suffix`
-- -----------------------------------------------------
CREATE TABLE if not exists `street_suffix` (
  `Common` varchar(45) DEFAULT NULL,
  `Standard` varchar(5) DEFAULT NULL,
  `TitleCaps` varchar(5) DEFAULT NULL
) ENGINE=InnoDB;




-- -----------------------------------------------------
-- Table `sys_config`
-- -----------------------------------------------------
CREATE TABLE if not exists `sys_config` (
  `Key` varchar(25) NOT NULL,
  `Value` varchar(225) NOT NULL DEFAULT '',
  `Type` varchar(15) NOT NULL DEFAULT '',
  `Category` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`Key`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `trans`
-- -----------------------------------------------------
CREATE TABLE if not exists `trans` (
  `idTrans` int(11) NOT NULL AUTO_INCREMENT,
  `Trans_Type` varchar(5) NOT NULL DEFAULT '',
  `Trans_Method` varchar(5) NOT NULL DEFAULT '',
  `Trans_Date` datetime DEFAULT NULL,
  `idName` varchar(15) NOT NULL DEFAULT '',
  `Order_Number` varchar(45) NOT NULL DEFAULT '',
  `Invoice_Number` varchar(45) NOT NULL DEFAULT '',
  `Payment_Type` varchar(15) NOT NULL DEFAULT '',
  `Check_Number` varchar(15) NOT NULL DEFAULT '',
  `Check_Bank` varchar(45) NOT NULL DEFAULT '',
  `Card_Number` varchar(4) NOT NULL DEFAULT '',
  `Card_Expire` varchar(15) NOT NULL DEFAULT '',
  `Card_Authorize` varchar(15) NOT NULL DEFAULT '',
  `Card_Name` varchar(45) NOT NULL DEFAULT '',
  `Auth_Code` varchar(45) NOT NULL DEFAULT '',
  `RefNo` varchar(25) NOT NULL DEFAULT '',
  `Process_Code` varchar(15) NOT NULL DEFAULT '',
  `Gateway_Ref` varchar(45) NOT NULL DEFAULT '',
  `Payment_Status` varchar(15) NOT NULL DEFAULT '',
  `Amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `Date_Entered` datetime DEFAULT NULL,
  `Entered_By` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idTrans`),
  UNIQUE KEY `idTrans_UNIQUE` (`idTrans`)
) ENGINE=InnoDB AUTO_INCREMENT=7;



-- -----------------------------------------------------
-- Table `transaction_type`
-- -----------------------------------------------------
CREATE TABLE if not exists `transaction_type` (
  `idtransaction_type` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(45) NOT NULL,
  `Effect` varchar(45) NOT NULL DEFAULT '',
  `Code` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`idtransaction_type`)
) ENGINE=InnoDB AUTO_INCREMENT=1;




-- -----------------------------------------------------
-- Table `volunteer_hours`
-- -----------------------------------------------------
CREATE TABLE if not exists `volunteer_hours` (
  `idVolunteer_hours` int(11) NOT NULL AUTO_INCREMENT,
  `idmcalendar` int(11) NOT NULL DEFAULT '0',
  `idName` int(11) NOT NULL,
  `idCompany` int(11) NOT NULL DEFAULT '0',
  `Org` varchar(45) NOT NULL DEFAULT '',
  `Hours` decimal(10,3) NOT NULL DEFAULT '0.000',
  `E_Start` datetime DEFAULT NULL,
  `E_End` datetime DEFAULT NULL,
  `Logged_By` varchar(45) NOT NULL DEFAULT '',
  `Date_Logged` datetime DEFAULT NULL,
  `Verified_By` varchar(45) NOT NULL DEFAULT '',
  `Date_Verified` datetime DEFAULT NULL,
  `E_Vol_Category` varchar(45) NOT NULL DEFAULT '',
  `E_Vol_Code` varchar(45) NOT NULL DEFAULT '',
  `E_Status` varchar(5) NOT NULL DEFAULT '',
  `Type` varchar(45) NOT NULL DEFAULT '',
  `idHouse` int(11) NOT NULL DEFAULT '0',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idVolunteer_hours`)
) ENGINE=InnoDB AUTO_INCREMENT=10;



-- -----------------------------------------------------
-- Table `w_auth`
-- -----------------------------------------------------
CREATE TABLE if not exists `w_auth` (
  `idName` int(11) NOT NULL DEFAULT '0',
  `Role_Id` varchar(3) NOT NULL DEFAULT '',
  `Organization_Id` varchar(3) NOT NULL DEFAULT '',
  `Policy_id` int(11) NOT NULL DEFAULT '0',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `User_Name` varchar(245) NOT NULL DEFAULT '',
  `Status` varchar(2) NOT NULL DEFAULT '',
  `Ticket` char(128) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idName`)
) ENGINE=InnoDB;




-- -----------------------------------------------------
-- Table `w_groups`
-- -----------------------------------------------------
CREATE TABLE if not exists `w_groups` (
  `Group_Code` varchar(5) NOT NULL DEFAULT '',
  `Title` varchar(45) NOT NULL DEFAULT '',
  `Description` varchar(255) NOT NULL DEFAULT '',
  `Default_Access_Level` varchar(5) NOT NULL DEFAULT '',
  `Max_Level` varchar(5) NOT NULL DEFAULT '',
  `Min_Access_Level` varchar(5) NOT NULL DEFAULT '',
  `Cookie_Restricted` bit(1) NOT NULL DEFAULT b'0',
  `Password_Policy` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Group_Code`)
) ENGINE=InnoDB;



-- -----------------------------------------------------
-- Table `w_users`
-- -----------------------------------------------------
CREATE TABLE if not exists `w_users` (
  `idName` int(11) NOT NULL,
  `User_Name` varchar(100) NOT NULL DEFAULT '',
  `Enc_PW` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `Status` varchar(4) NOT NULL DEFAULT '',
  `Certificate` varchar(145) NOT NULL DEFAULT '',
  `Cookie` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `Session` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `Ip` varchar(15) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `Verify_Address` varchar(4) NOT NULL DEFAULT '',
  `Last_Login` datetime DEFAULT NULL,
  `Hash_PW` char(32) NOT NULL DEFAULT '',
  `Updated_By` varchar(45) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`User_Name`),
  UNIQUE KEY `User_Name_UNIQUE` (`User_Name`)
) ENGINE=InnoDB;




-- -----------------------------------------------------
-- Table `web_sites`
-- -----------------------------------------------------
CREATE TABLE if not exists `web_sites` (
  `idweb_sites` int(11) NOT NULL AUTO_INCREMENT,
  `Site_Code` varchar(5) NOT NULL,
  `Description` varchar(245) NOT NULL DEFAULT '',
  `Relative_Address` varchar(145) NOT NULL DEFAULT '',
  `Required_Group_Code` varchar(45) NOT NULL DEFAULT '',
  `Path_To_CSS` varchar(145) NOT NULL DEFAULT '',
  `Path_To_JS` varchar(145) NOT NULL DEFAULT '',
  `Last_Updated` datetime DEFAULT NULL,
  `Updated_By` varchar(45) NOT NULL,
  `Default_Page` varchar(105) NOT NULL DEFAULT '',
  `Index_Page` varchar(145) NOT NULL DEFAULT '',
  `HTTP_Host` varchar(245) NOT NULL DEFAULT '',
  PRIMARY KEY (`idweb_sites`)
) ENGINE=InnoDB AUTO_INCREMENT=5;


