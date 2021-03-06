<?php
/**
 * PaymentsRS.php
 *
 * @category  Site
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 *
 */


class PaymentRS extends TableRS {
    public $idPayment;   // int(11) NOT NULL,
    public $Attempt;   // int(11) DEFAULT NULL,
    public $Amount;   // decimal(10,2) NOT NULL DEFAULT '0.00',
    public $Balance;  // decimal(10,2) NOT NULL DEFAULT '0.00',
    public $Result;   // varchar(4) NOT NULL DEFAULT '',
    public $Payment_Date;   // datetime DEFAULT NULL,
    public $idPayor;   // int(11) NOT NULL DEFAULT '0',
    public $idPayment_Method;   // int(11) NOT NULL DEFAULT '0',
    public $idTrans;  // int(11) NOT NULL DEFAULT '0',
    public $idToken;  // int(11) NOT NULL DEFAULT '0',
    public $Is_Refund;   // tinyint(4) DEFAULT NULL,
    public $Is_Preauth;   // tinyint(4) DEFAULT NULL,
    public $Status_Code;   // varchar(5) NOT NULL DEFAULT '',
    public $Created_By;   // varchar(45) NOT NULL DEFAULT '',
    public $Updated_By;   // varchar(45) NOT NULL DEFAULT '',
    public $Last_Updated;   // datetime DEFAULT NULL,
    public $Timestamp;   // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    function __construct($TableName = "payment") {
        $this->idPayment = new DB_Field("idPayment", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Attempt = new DB_Field("Attempt", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Amount = new DB_Field('Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Balance = new DB_Field('Balance', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Result = new DB_Field("Result", "", new DbStrSanitizer(4), TRUE, TRUE);
        $this->Payment_Date = new DB_Field("Payment_Date", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->idPayor = new DB_Field("idPayor", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->idToken = new DB_Field("idToken", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->idPayment_Method = new DB_Field("idPayment_Method", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->idTrans = new DB_Field("idTrans", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Is_Refund = new DB_Field("Is_Refund", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Is_Preauth = new DB_Field("Is_Preauth", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Status_Code = new DB_Field("Status_Code", "", new DbStrSanitizer(5), TRUE, TRUE);
        $this->Created_By = new DB_Field("Created_By", '', new DbStrSanitizer(45), TRUE, True);

        $this->Updated_By = new DB_Field("Updated_By", '', new DbStrSanitizer(45), TRUE, True);
        $this->Last_Updated = new DB_Field("Last_Updated", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        $this->Timestamp = new DB_Field("Timestamp", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        parent::__construct($TableName);
    }

}

class PaymentInvoiceRS extends TableRS {

    public $idPayment_Invoice;   // INTEGER NOT NULL,
    public $Payment_Id;   // INTEGER,
    public $Invoice_Id;   // INTEGER,
    public $Amount;   // DECIMAL(22,10),
    public $Create_Datetime;   // TIMESTAMP NOT NULL,

    function __construct($TableName = "payment_invoice") {
        $this->idPayment_Invoice = new DB_Field("idPayment_Invoice", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Payment_Id = new DB_Field("Payment_Id", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Invoice_Id = new DB_Field("Invoice_Id", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Amount = new DB_Field('Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Create_Datetime = new DB_Field("Create_Datetime", NULL, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);

        parent::__construct($TableName);
    }

}


class Payment_AuthRS extends TableRS {

    public $idPayment_auth;   // int(11) NOT NULL AUTO_INCREMENT,
    public $idPayment;  // int(11) NOT NULL,
    public $Approved_Amount;   // decimal(10,2) NOT NULL DEFAULT '0.00',
    public $idTrans;   // int(11) NOT NULL DEFAULT '0',
    public $Processor;   // varchar(45) NOT NULL DEFAULT '',
    public $Approval_Code;   // varchar(20) NOT NULL DEFAULT '',
    public $AVS;   // varchar(20) NOT NULL DEFAULT '',
    public $Invoice_Number;   // varchar(45) NOT NULL DEFAULT '',
    public $Acct_Number;  // varchar(25) NOT NULL DEFAULT '',
    public $Card_Type;  // varchar(10) NOT NULL DEFAULT '',
    public $Customer_Id;   // varchar(45) NOT NULL DEFAULT '',
    public $Reference_Num;   // varchar(45) NOT NULL DEFAULT '',
    public $AcqRefData;   // varchar(200) NOT NULL DEFAULT '',
    public $ProcessData;   // varchar(200) NOT NULL DEFAULT '',
    public $Code3;   // varchar(45) NOT NULL DEFAULT '',
    public $Serialized_Details;   // varchar(1000) NOT NULL DEFAULT '',
    public $Status_Code;   // varchar(5) NOT NULL DEFAULT '',
    public $Updated_By;   // varchar(45) NOT NULL DEFAULT '',
    public $Last_Updated;   // datetime DEFAULT NULL,
    public $Timestamp;   // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    function __construct($TableName = "payment_auth") {
        $this->idPayment_auth = new DB_Field("idPayment_auth", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->idPayment = new DB_Field("idPayment", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Approved_Amount = new DB_Field('Approved_Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->idTrans = new DB_Field("idTrans", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Processor = new DB_Field("Processor", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Approval_Code = new DB_Field("Approval_Code", "", new DbStrSanitizer(20), TRUE, TRUE);
        $this->AVS = new DB_Field("AVS", "", new DbStrSanitizer(20), TRUE, TRUE);
        $this->Invoice_Number = new DB_Field("Invoice_Number", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Acct_Number = new DB_Field("Acct_Number", "", new DbStrSanitizer(25), TRUE, TRUE);
        $this->Card_Type = new DB_Field("Card_Type", "", new DbStrSanitizer(10), TRUE, TRUE);
        $this->Customer_Id = new DB_Field("Customer_Id", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Reference_Num = new DB_Field("Reference_Num", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->AcqRefData = new DB_Field("Code1", "", new DbStrSanitizer(200), TRUE, TRUE);
        $this->ProcessData = new DB_Field("Code2", "", new DbStrSanitizer(200), TRUE, TRUE);
        $this->Code3 = new DB_Field("Code3", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Serialized_Details = new DB_Field("Serialized_Details", "", new DbStrSanitizer(1000), TRUE, TRUE);
        $this->Status_Code = new DB_Field("Status_Code", "", new DbStrSanitizer(5), TRUE, TRUE);

        $this->Updated_By = new DB_Field("Updated_By", '', new DbStrSanitizer(45), TRUE, True);
        $this->Last_Updated = new DB_Field("Last_Updated", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        $this->Timestamp = new DB_Field("Timestamp", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        parent::__construct($TableName);
    }

}

class ReceiptRS extends TableRS {
    public $idReceipt;   // int(11) NOT NULL AUTO_INCREMENT,
    public $Receipt_Number;   // varchar(20) NOT NULL DEFAULT '',
    public $Date_Issued;   // datetime DEFAULT NULL,
    public $Issued_To_Id;   // int(11) NOT NULL DEFAULT '0',
    public $Notes;   // varchar(250) NOT NULL DEFAULT '',
    public $Updated_By;   // varchar(45) NOT NULL DEFAULT '',
    public $Last_Updated;   // datetime DEFAULT NULL,
    public $Timestamp;   // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    function __construct($TableName = "receipt") {
        $this->idReceipt = new DB_Field("idReceipt", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Receipt_Number = new DB_Field("Receipt_Number", "", new DbStrSanitizer(20), TRUE, TRUE);
        $this->Date_Issued = new DB_Field("Date_Issued", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Notes = new DB_Field("Notes", "", new DbStrSanitizer(250), TRUE, TRUE);
        $this->Issued_To_Id = new DB_Field("Issued_To_Id", 0, new DbIntSanitizer(), TRUE, TRUE);

        $this->Updated_By = new DB_Field("Updated_By", '', new DbStrSanitizer(45), TRUE, True);
        $this->Last_Updated = new DB_Field("Last_Updated", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        $this->Timestamp = new DB_Field("Timestamp", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        parent::__construct($TableName);
    }


}

class MoneyRS extends TableRS {

    public $idMoney;  // int(11) NOT NULL AUTO_INCREMENT,
    public $Amount;  // decimal(10,2) NOT NULL,
    public $idPayment;  // int(11) NOT NULL,
    public $idInvoice;  // int(11) NOT NULL DEFAULT '0',
    public $idInvoiceLine;  // int(11) NOT NULL DEFAULT '0',
    public $Invoice_Number;  // varchar(45) NOT NULL DEFAULT '',
    public $Discount_Taken;  // decimal(10,2) NOT NULL DEFAULT '0',
    public $Order_Number;  // varchar(45) NOT NULL DEFAULT '',
    public $OrderLine_Number;  // int(11) NOT NULL DEFAULT '0',
    public $Transaction_Date;  // datetime DEFAULT NULL,
    public $Transaction_Type;  // varchar(5) NOT NULL DEFAULT '',
    public $Timestamp;  // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,


    function __construct($TableName = "money") {
        $this->idMoney = new DB_Field("idMoney", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Amount = new DB_Field('Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->idPayment = new DB_Field("idPayment", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->idInvoice = new DB_Field("idInvoice", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->idInvoiceLine = new DB_Field("idInvoiceLine", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Invoice_Number = new DB_Field("Invoice_Number", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Discount_Taken = new DB_Field('Discount_Taken', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Order_Number = new DB_Field("Order_Number", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->OrderLine_Number = new DB_Field("OrderLine_Number", 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Transaction_Date = new DB_Field("Transaction_Date", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Transaction_Type = new DB_Field("Transaction_Type", "", new DbStrSanitizer(5), TRUE, TRUE);

        $this->Timestamp = new DB_Field("Timestamp", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        parent::__construct($TableName);
    }

}

class TransRs extends TableRS {

    public $idTrans;  // int(11) NOT NULL AUTO_INCREMENT,
    public $Trans_Type;  // varchar(5) NOT NULL DEFAULT '' COMMENT '	',
    public $Trans_Method;  // varchar(5) NOT NULL DEFAULT '',
    public $Trans_Date;  // datetime DEFAULT NULL,
    public $idName;  // varchar(15) NOT NULL DEFAULT '',
    public $Order_Number;  // varchar(45) NOT NULL DEFAULT '',
    public $Invoice_Number;  // varchar(45) NOT NULL DEFAULT '',
    public $Payment_Type;  // varchar(15) NOT NULL DEFAULT '',
    public $Check_Number;  // varchar(15) NOT NULL DEFAULT '',
    public $Check_Bank;  // varchar(45) NOT NULL DEFAULT '',
    public $Card_Number;  // varchar(4) NOT NULL DEFAULT '',
    public $Card_Expire;  // varchar(15) NOT NULL DEFAULT '',
    public $Card_Authorize;  // varchar(15) NOT NULL DEFAULT '',
    public $Card_Name;  // varchar(45) NOT NULL DEFAULT '',
    public $Auth_Code;  // varchar(45) NOT NULL DEFAULT '',
    public $RefNo;  // varchar(25) NOT NULL DEFAULT '',
    public $Process_Code;  // varchar(15) NOT NULL DEFAULT '',
    public $Gateway_Ref;  // varchar(45) NOT NULL DEFAULT '',
    public $Payment_Status;  // varchar(15) NOT NULL DEFAULT '',
    public $Amount;  // decimal(10,2) NOT NULL DEFAULT '0.00',
    public $Date_Entered;  // datetime DEFAULT NULL,
    public $Entered_By;  // varchar(45) NOT NULL DEFAULT '',
    public $Timestamp;  // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    function __construct($TableName = "trans") {
        $this->idTrans = new DB_Field("idTrans", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Trans_Type = new DB_Field("Trans_Type", "", new DbStrSanitizer(5), TRUE, TRUE);
        $this->Trans_Method = new DB_Field("Trans_Method", "", new DbStrSanitizer(5), TRUE, TRUE);
        $this->Trans_Date = new DB_Field("Trans_Date", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->idName = new DB_Field("idName", 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Order_Number = new DB_Field("Order_Number", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Invoice_Number = new DB_Field("Invoice_Number", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Payment_Type = new DB_Field("Payment_Type", "", new DbStrSanitizer(15), TRUE, TRUE);
        $this->Check_Number = new DB_Field("Check_Number", "", new DbStrSanitizer(15), TRUE, TRUE);
        $this->Check_Bank = new DB_Field("Check_Bank", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Card_Number = new DB_Field("Card_Number", "", new DbStrSanitizer(4), TRUE, TRUE);
        $this->Card_Expire = new DB_Field("Card_Expire", "", new DbStrSanitizer(15), TRUE, TRUE);
        $this->Card_Authorize = new DB_Field("Card_Authorize", "", new DbStrSanitizer(15), TRUE, TRUE);
        $this->Card_Name = new DB_Field("Card_Name", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Auth_Code = new DB_Field("Auth_Code", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->RefNo = new DB_Field("RefNo", "", new DbStrSanitizer(25), TRUE, TRUE);
        $this->Process_Code = new DB_Field("Process_Code", "", new DbStrSanitizer(15), TRUE, TRUE);
        $this->Gateway_Ref = new DB_Field("Gateway_Ref", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Payment_Status = new DB_Field("Payment_Status", "", new DbStrSanitizer(15), TRUE, TRUE);
        $this->Amount = new DB_Field('Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Date_Entered = new DB_Field("Date_Entered", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);

        $this->Entered_By = new DB_Field("Entered_By", '', new DbStrSanitizer(45), TRUE, True);
        $this->Timestamp = new DB_Field("Timestamp", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        parent::__construct($TableName);
    }

}

class InvoiceRs extends TableRS {

    public $idInvoice;  // int(11) NOT NULL AUTO_INCREMENT,
    public $Delegated_Invoice_Id;  // int(11) NOT NULL DEFAULT '0',
    public $Invoice_Number;  // varchar(45) DEFAULT NULL,
    public $Invoice_Type;  // varchar(4) DEFAULT NULL,
    public $Deleted;  // SMALLINT default 0 NOT NULL,
    public $Amount;  // decimal(10,2) NOT NULL DEFAULT '0.00',
    public $Sold_To_Id;  //;  // int(11) DEFAULT NULL,
    public $idGroup;  // int(11) DEFAULT NULL,
    public $Invoice_Date;  // datetime DEFAULT NULL,
    public $Payment_Attempts;  // int(11) NOT NULL DEFAULT '0',
    public $Status;  // varchar(5) NOT NULL DEFAULT '',
    public $Carried_Amount;  // decimal(10,2) NOT NULL DEFAULT '0.00',
    public $Balance;  // decimal(10,2) NOT NULL DEFAULT '0.00',
    public $Order_Number;  // varchar(45) DEFAULT NULL,
    public $First_Payment_Due;  // date DEFAULT NULL,
    public $Last_Reminder;  // DATETIME,
    public $Overdue_Step;  // INTEGER NOT NULL DEFAULT '0',
    public $Description;  // varchar(45) DEFAULT NULL,
    public $Notes;  // varchar(450) DEFAULT NULL,
    public $Updated_By;  // varchar(45) DEFAULT NULL,
    public $Last_Updated;  // datetime DEFAULT NULL,
    public $Timestamp;  // timestamp NULL DEFAULT NULL,

    function __construct($TableName = 'invoice') {
        $this->idInvoice = new DB_Field('idInvoice', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Delegated_Invoice_Id = new DB_Field('Delegated_Invoice_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Invoice_Number = new DB_Field('Invoice_Number', '', new DbStrSanitizer(45), TRUE, TRUE);
        $this->Billing_Process_Id = new DB_Field('Billing_Process_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Deleted = new DB_Field('Deleted', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Amount = new DB_Field('Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Sold_To_Id = new DB_Field('Sold_To_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->idGroup = new DB_Field('idGroup', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Invoice_Date = new DB_Field('Invoice_Date', NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Payment_Attempts = new DB_Field('Payment_Attempts', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Status = new DB_Field('Status', "", new DbStrSanitizer(5), TRUE, TRUE);
        $this->Carried_Amount = new DB_Field('Carried_Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Balance = new DB_Field('Balance', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Order_Number = new DB_Field('Order_Number', "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Due_Date = new DB_Field("Due_Date", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Description = new DB_Field("Description", "", new DbStrSanitizer(45), TRUE, TRUE);
        $this->Notes = new DB_Field("Notes", "", new DbStrSanitizer(450), TRUE, TRUE);

        $this->Updated_By = new DB_Field("Updated_By", '', new DbStrSanitizer(45), TRUE, True);
        $this->Last_Updated = new DB_Field("Last_Updated", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        $this->Timestamp = new DB_Field("Timestamp", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);
        parent::__construct($TableName);
    }

}

class InvoiceLineRS extends TableRS {

    public $idInvoice_Line;  // INTEGER NOT NULL,
    public $Invoice_Id;  // INTEGER,
    public $Type_Id;  //Integer NOT NULL DEFAULT '0',
    public $Amount;  // DECIMAL(22,10) NOT NULL,
    public $Quantity;  // DECIMAL(22,10),
    public $Price;  // DECIMAL(22,10),
    public $Deleted;  // SMALLINT default 0 NOT NULL,
    public $Item_Id;  // INTEGER,
    public $Description;  // VARCHAR(1000),
    public $Source_User_Id;  // INTEGER,
    public $Is_Percentage;  // SMALLINT default 0 NOT NULL,
    public $Timestamp;  // timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

    function __construct($TableName = 'invoice_line') {
        $this->idInvoice_Line = new DB_Field('idInvoice_Line', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Invoice_Id = new DB_Field('Invoice_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Type_Id = new DB_Field('Type_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Amount = new DB_Field('Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Quantity = new DB_Field('Quantity', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Price = new DB_Field('Price', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Deleted = new DB_Field('Deleted', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Item_Id = new DB_Field('Item_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Description = new DB_Field('Description', '', new DbStrSanitizer(1000), TRUE, TRUE);
        $this->Source_User_Id = new DB_Field('Source_User_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Is_Percentage = new DB_Field('Is_Percentage', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Timestamp = new DB_Field('Timestamp', null, new DbDateSanitizer('Y-m-d H:i:s'), FALSE);
        parent::__construct($TableName);
    }

}


class InvoiceLineTypeRS extends TableRS {

    public $idInvoice_Line_Type;  // INTEGER NOT NULL,
    public $Description;  // VARCHAR(50) NOT NULL,
    public $Order_Position;  // INTEGER NOT NULL,

    function __construct($TableName = 'invoice_line_type') {
        $this->idInvoice_Line_Type = new DB_Field('idInvoice_Line_Type', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Description = new DB_Field('Description', '', new DbStrSanitizer(50), TRUE, TRUE);
        $this->Order_Position = new DB_Field('Order_Position', 0, new DbIntSanitizer(), TRUE, TRUE);
        parent::__construct($TableName);
    }

}

