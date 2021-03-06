<?php
/**
 * Invoice.php
 *
 *
 * @category  house
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link
 */

/**
 * Description of Invoice
 *
 * @author Eric
 */
class Invoice {

    protected $invRs;
    protected $invoiceNum;



    function __construct(PDO $dbh, $invoiceNumber = 0) {

        $this->invoiceNum = $invoiceNumber;
        $this->invRs = new InvoiceRs();

        if ($invoiceNumber > 0) {
            $this->invRs->Invoice_Number->setStoredVal($invoiceNumber);
            $rows = EditRS::select($dbh, $this->invRs, array($this->invRs->Invoice_Number));

            if (count($rows) == 1) {
                EditRS::loadRow($rows[0], $this->invRs);
            } else {
                throw new Hk_Exception_Runtime('Invoice number not found: ' . $invoiceNumber);
            }
        }
    }

    public function loadInvoice(PDO $dbh, $idInvoice) {

        $this->invoiceNum = 0;
        $this->invRs = new InvoiceRs();

        if ($idInvoice > 0) {

            $this->invRs->idInvoice->setStoredVal($idInvoice);
            $rows = EditRS::select($dbh, $this->invRs, array($this->invRs->idInvoice));

            if (count($rows) == 1) {

                EditRS::loadRow($rows[0], $this->invRs);
                $this->invoiceNum = $this->invRs->Invoice_Number->getStoredVal();

            } else {
                throw new Hk_Exception_Runtime('Invoice Id not found: ' . $idInvoice);
            }
        }

    }

    public function newInvoice(PDO $dbh, $amount, $invoiceType, $soldToId, $idGroup, $orderNumber, $description, $invoiceDate, $username) {

        $invRs = new InvoiceRs();
        $invRs->Amount->setNewVal($amount);
        $invRs->Balance->setNewVal($amount);
        $invRs->Invoice_Number->setNewVal(self::createNewInvoiceNumber($dbh));
        //$invRs->Invoice_Type->setNewVal($invoiceType);
        $invRs->Sold_To_Id->setNewVal($soldToId);
        $invRs->idGroup->setNewVal($idGroup);
        $invRs->Order_Number->setNewVal($orderNumber);
        $invRs->Description->setNewVal($description);
        $invRs->Invoice_Date->setNewVal($invoiceDate);
        $invRs->Status->setNewVal(InvoiceStatus::Unpaid);

        $invRs->Updated_By->setNewVal($username);
        $invRs->Last_Updated->setNewVal(date('Y-m-d H:i:s'));

        $idInvoice = EditRS::insert($dbh, $invRs);
        $invRs->idInvoice->setNewVal($idInvoice);
        EditRS::updateStoredVals($invRs);

        $this->invRs = $invRs;
        $this->invoiceNum = $invRs->Invoice_Number->getStoredVal();

        return $idInvoice;
    }

    public function updateInvoice(PDO $dbh, $paymentAmount, $user) {

        if ($this->invoiceNum > 0) {

            $invAmt = $this->invRs->Amount->getStoredVal();
            $bal = $invAmt - $paymentAmount;
            $this->invRs->Balance->setNewVal($bal);

            $attempts = $this->invRs->Payment_Attempts->getStoredVal();
            $this->invRs->Payment_Attempts->setNewVal(++$attempts);

            if ($bal == 0) {
                $this->invRs->Status->setNewVal(InvoiceStatus::Paid);
            } else {
                $this->invRs->Status->setNewVal(InvoiceStatus::Unpaid);
            }

            $this->invRs->Last_Updated->setNewVal(date('Y-m-d H:i:s'));
            $this->invRs->Updated_By->setNewVal($user);

            EditRS::update($dbh, $this->invRs, array($this->invRs->Invoice_Number));
            EditRS::updateStoredVals($this->invRs);

        } else {
            throw new Hk_Exception_Payment('Cannot update a blank invoice record.  ');

        }

    }

    private function createNewInvoiceNumber(PDO $dbh) {
        return incCounter($dbh, 'invoice');
    }

    public static function getIdFromInvNum(PDO $dbh, $invNum) {

        $idInvoice = 0;

        if ($invNum < 1) {
            return $idInvoice;
        }

        $invRs = new InvoiceRs();
        $invRs->Invoice_Number->setStoredVal($invNum);
        $rows = EditRS::select($dbh, $invRs, array($invRs->Invoice_Number));

        if (count($rows) == 1) {
            EditRS::loadRow($rows[0], $invRs);
            $idInvoice = $invRs->idInvoice->getStoredVal();
        }

        return $idInvoice;

    }

    public function getIdInvoice() {
        return $this->invRs->idInvoice->getStoredVal();
    }

    public function getInvoiceNumber() {
        return $this->invRs->Invoice_Number->getStoredVal();
    }

    public function getBalance() {
        return $this->invRs->Balance->getStoredVal();
    }

    public function getStatus() {
        return $this->invRs->Status->getStoredVal();
    }

    public function getPayAttemtps() {
        return $this->invRs->Payment_Attempts->getStoredVal();
    }

    public function getSoldToId() {
        return $this->invRs->Sold_To_Id->getStoredVal();
    }

    public function getIdGroup() {
        return $this->invRs->idGroup->getStoredVal();
    }

    public function getOrderNumber() {
        return $this->invRs->Order_Number->getStoredVal();
    }

}
