<?php
/**
 * OrdersRS.php
 *
 * @category  Site
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 *
 */

class ItemRS extends TableRS {

    public $idItem;  // INTEGER NOT NULL,
    public $Internal_Number;  // VARCHAR(50) NOT NULL default '',
    public $Entity_Id;  // INTEGER NOT NULL DEFAULT 0,
    public $Percentage;  // DECIMAL(22,10) NOT NULL DEFAULT '0.00',
    public $Deleted;  // SMALLINT default 0 NOT NULL DEFAULT '0',
    public $Has_Decimals;  // SMALLINT default 0 NOT NULL DEFAULT '0',
    public $Gl_Code;  // VARCHAR(50) NOT NULL default '',
    public $Description;  // VARCHAR(1000), NOT NULL default ''.

    function __construct($TableName = 'item') {
        $this->idItem = new DB_Field('idItem', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Internal_Number = new DB_Field('Internal_Number', '', new DbStrSanitizer(50), TRUE, TRUE);
        $this->Entity_Id = new DB_Field('Entity_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Percentage = new DB_Field('Percentage', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Deleted = new DB_Field('Deleted', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Has_Decimals = new DB_Field('Has_Decimals', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Gl_Code = new DB_Field('Gl_Code', '', new DbStrSanitizer(50), TRUE, TRUE);
        $this->Description = new DB_Field('Description', '', new DbStrSanitizer(1000), TRUE, TRUE);
        parent::__construct($TableName);
    }

}

class ItemTypeRS extends TableRS {

    public $idItem_Type;  // INTEGER NOT NULL,
    public $Category_Type;  // INTEGER NOT NULL,
    public $Description;  // VARCHAR(100),
    public $Order_Line_Type_Id;  // INTEGER NOT NULL,

    function __construct($TableName = 'item_type') {
        $this->idItem_Type = new DB_Field('idItem_Type', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Category_Type = new DB_Field('Category_Type', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Description = new DB_Field('Description', '', new DbStrSanitizer(100), TRUE, TRUE);
        $this->Order_Line_Type_Id = new DB_Field('Order_Line_Type_Id', 0, new DbIntSanitizer(), TRUE, TRUE);

        parent::__construct($TableName);
    }

}


class ItemTypeMapRS extends TableRS {

    public $Item_Id;  // INTEGER,
    public $Type_Id;  // INTEGER);

    function __construct($TableName = 'item_type_map') {
        $this->Item_Id = new DB_Field('Item_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Type_Id = new DB_Field('Type_Id', 0, new DbIntSanitizer(), TRUE, TRUE);

        parent::__construct($TableName);
    }

}


class OrderLineRS Extends TableRS {

    public $idOrder_Line;  // INTEGER NOT NULL,
    public $Order_Id;  // INTEGER,
    public $Item_Id;  // INTEGER,
    public $Type_Id;  // INTEGER,
    public $Amount;  // DECIMAL(22,10) NOT NULL,
    public $Quantity;  // DECIMAL(22,10),
    public $Price;  // DECIMAL(22,10),
    public $Item_Price;  // SMALLINT,
    public $Create_Datetime;  // TIMESTAMP NOT NULL,
    public $Deleted;  // SMALLINT default 0 NOT NULL,
    public $Use_Item;  // SMALLINT NOT NULL,
    public $Description;  // VARCHAR(1000),
    public $Provisioning_Status;  // INTEGER,
    public $Provisioning_Request_Id;  // VARCHAR(50),

    function __construct($TableName = 'order_line') {
        $this->idOrder_Line = new DB_Field('idOrder_Line', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Order_Id = new DB_Field('Order_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Item_Id = new DB_Field('Item_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Type_Id = new DB_Field('Type_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Amount = new DB_Field('Amount', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Quantity = new DB_Field('Quantity', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Price = new DB_Field('Price', 0, new DbDecimalSanitizer(), TRUE, TRUE);
        $this->Item_Price = new DB_Field('Item_Price', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Create_Datetime = new DB_Field("Create_Datetime", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Deleted = new DB_Field('Deleted', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Use_Item = new DB_Field('Use_Item', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Description = new DB_Field('Description', '', new DbStrSanitizer(1000), TRUE, TRUE);
        $this->Provisioning_Status = new DB_Field('Provisioning_Status', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Provisioning_Request_Id = new DB_Field('Provisioning_Request_Id', '', new DbStrSanitizer(50), TRUE, TRUE);
        parent::__construct($TableName);
    }

}

class OrderLineTypeRS extends TableRS {

    public $idOrder_Line_Type;  // INTEGER NOT NULL,
    public $Editable;  // INTEGER NOT NULL,
    public $Description;  //  VARCHAR(50) NOT NULL default '',

    function __construct($TableName = 'order_line_type') {
        $this->idOrder_Line_Type = new DB_Field('idOrder_Line_Type', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Editable = new DB_Field('Editable', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Description = new DB_Field('Description', '', new DbStrSanitizer(50), TRUE, TRUE);

        parent::__construct($TableName);
    }

}

class OrderPeriodRS extends TableRS {

    public $idOrder_Period;  // INTEGER NOT NULL AUTO_INCREMENT,
    public $Entity_Id;  // INTEGER NOT NULL default 0,
    public $Value;  // INTEGER NOT NULL default 0,
    public $Unit_Id;  // INTEGER NOT NULL default 0,
    public $Description;  //  VARCHAR(50) NOT NULL default '',

    function __construct($TableName = 'order_period') {
        $this->idOrder_Period = new DB_Field('idOrder_Period', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Entity_Id = new DB_Field('Entity_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Value = new DB_Field('Value', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Unit_Id = new DB_Field('Unit_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Description = new DB_Field('Description', '', new DbStrSanitizer(50), TRUE, TRUE);

        parent::__construct($TableName);
    }
}

class OrderProcessRS extends TableRS {

    public $idOrder_Process;  // INTEGER NOT NULL,
    public $Order_Id;  // INTEGER NOT NULL,
    public $Invoice_Id;  // INTEGER NOT NULL default 0,
    public $Billing_Process_Id;  // INTEGER NOT NULL default 0,
    public $Periods_Included;  // INTEGER NOT NULL default 0,
    public $Period_Start;  // DATETIME,
    public $Period_End;  // DATETIME,
    public $Is_Review;  // INTEGER NOT NULL,
    public $Origin;  // INTEGER NOT NULL default 0,

    function __construct($TableName = 'order_process') {
        $this->idOrder_Process = new DB_Field('idOrder_Process', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Order_Id = new DB_Field('Order_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Invoice_Id = new DB_Field('Invoice_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Billing_Process_Id = new DB_Field('Billing_Process_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Periods_Included = new DB_Field('Periods_Included', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Create_Datetime = new DB_Field("Create_Datetime", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Is_Review = new DB_Field('Is_Review', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Origin = new DB_Field('Origin', 0, new DbIntSanitizer(), TRUE, TRUE);

        parent::__construct($TableName);
    }

}

class PurchaseOrderRS extends TableRS {

    public $idPurchase_Order;  // INTEGER NOT NULL AUTO_INCREMENT,
    public $User_Id;  // INTEGER NOT NULL default 0,
    public $Group_Id;  // INTEGER NOT NULL default 0,
    public $Period_Id;  // INTEGER NOT NULL default 0,
    public $Billing_Type_Id;  // INTEGER NOT NULL,
    public $Active_Since;  // DATETIME,
    public $Active_Until;  // DATETIME,
    public $Cycle_Start;  // DATETIME,
    public $Next_Billable_Day;  // DATETIME,
    public $Created_By;  // INTEGER NOT NULL default 0,
    public $Status_Id;  // INTEGER NOT NULL,
    public $Deleted;  // SMALLINT NOT NULL default 0,
    public $Notify;  // SMALLINT NOT NULL default 0,
    public $Last_Notified;  // TIMESTAMP,
    public $Notification_Step;  // INTEGER NOT NULL default 0,
    public $Anticipate_Periods;  // INTEGER NOT NULL default 0,
    public $Notes;  // VARCHAR(200) NOT NULL DEFAULT '',
    public $Notes_In_Invoice;  // SMALLINT NOT NULL default 0,
    public $Is_Current;  // SMALLINT NOT NULL default 0,
    public $Timestamp;  // TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    function __construct($TableName = 'order_process') {
        $this->idPurchase_Order = new DB_Field('idPurchase_Order', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->User_Id = new DB_Field('User_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Group_Id = new DB_Field('Group_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Period_Id = new DB_Field('Period_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Billing_Type_Id = new DB_Field('Billing_Type_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Active_Since = new DB_Field("Active_Since", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Active_Until = new DB_Field("Active_Until", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Cycle_Start = new DB_Field("Cycle_Start", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Next_Billable_Day = new DB_Field("Next_Billable_Day", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Created_By = new DB_Field('Created_By', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Status_Id = new DB_Field('Status_Id', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Deleted = new DB_Field('Deleted', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Notify = new DB_Field('Notify', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Last_Notified = new DB_Field("Last_Notified", NULL, new DbDateSanitizer("Y-m-d H:i:s"), TRUE, TRUE);
        $this->Notification_Step = new DB_Field('Notification_Step', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Anticipate_Periods = new DB_Field('Anticipate_Periods', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Notes = new DB_Field('Notes', '', new DbStrSanitizer(200), TRUE, TRUE);
        $this->Notes_In_Invoice = new DB_Field('Notes_In_Invoice', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Is_Current = new DB_Field('Is_Current', 0, new DbIntSanitizer(), TRUE, TRUE);
        $this->Timestamp = new DB_Field("Timestamp", null, new DbDateSanitizer("Y-m-d H:i:s"), FALSE);

        parent::__construct($TableName);
    }
}