<?php
/**
 * CheckTX.php
 *
 *
 * @category  house
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

/**
 * Description of CheckTX
 *
 * @author Eric
 */
class CheckTX {

    public static function checkSale(\PDO $dbh, \CheckResponse $pr, $userName) {

        // Record transaction
        $transRs = Transaction::recordTransaction($dbh, $pr, '', TransType::Sale, TransMethod::Check);
        $pr->idTrans = $transRs->idTrans->getStoredVal();


        // Record Payment
        $payRs = new PaymentRS();
        $payRs->Amount->setNewVal($pr->getAmount());
        $payRs->Payment_Date->setNewVal(date("Y-m-d H:i:s"));
        $payRs->idPayor->setNewVal($pr->idPayor);
        $payRs->idTrans->setNewVal($pr->idTrans);
        $payRs->idToken->setNewVal($pr->idToken);
        $payRs->idPayment_Method->setNewVal(3);
        $payRs->Attempt->setNewVal(1);
        $payRs->Status_Code->setNewVal(PaymentStatusCode::Paid);
        $payRs->Created_By->setNewVal($userName);

        $idPayment = EditRS::insert($dbh, $payRs);
        $payRs->idPayment->setNewVal($idPayment);
        EditRS::updateStoredVals($payRs);
        $pr->paymentRs = $payRs;

        // Money table
        $moneyRs = new MoneyRS();
        $moneyRs->idPayment->setNewVal($idPayment);
        $moneyRs->Invoice_Number->setNewVal($pr->getInvoice());
        $moneyRs->Amount->setNewVal($pr->getAmount());
        $moneyRs->Transaction_Type->setNewVal(TransType::Sale);
        $moneyRs->Transaction_Date->setNewVal(date("Y-m-d H:i:s"));

        $monId = EditRS::insert($dbh, $moneyRs);
        $moneyRs->idMoney->setNewVal($monId);
        EditRS::updateStoredVals($moneyRs);

        $pr->moneyRs = $moneyRs;

        return $pr;

    }

    public static function checkVoid(\PDO $dbh, \CheckResponse $pr, $username, $idPayment) {

        // Record transaction
        $transRs = Transaction::recordTransaction($dbh, $pr, '', TransType::Void, TransMethod::Check);
        $pr->idTrans = $transRs->idTrans->getStoredVal();

        if ($idPayment == 0) {
            throw new Hk_Exception_Payment('Payment Id not given.  ');
        }


        // Should be a payment
        $payRs = new PaymentRS();
        $payRs->idPayment->setStoredVal($idPayment);
        $rows = EditRS::select($dbh, $payRs, array($payRs->idPayment));

        if (count($rows) == 1) {

            // Payment record
            $payRs->Status_Code->setNewVal(PaymentStatusCode::VoidSale);
            $payRs->Balance->setNewVal($payRs->Amount->getStoredVal());
            $payRs->Updated_By->setNewVal($username);
            $payRs->Last_Updated->setNewVal(date('Y-m-d H:i:s'));

            EditRS::update($dbh, $payRs, array($payRs->idPayment));
            EditRS::updateStoredVals($payRs);
            $pr->paymentRs = $payRs;
        }

        return $pr;
    }

}

class CheckResponse extends PaymentResponse {

    private $amount;
    private $invoiceNumber;


    function __construct($amount, $idPayor, $invoiceNumber, $checkNumber = '') {
        $this->response = NULL;
        $this->paymentType = PayType::Check;
        $this->idPayor = $idPayor;
        $this->amount = $amount;
        $this->invoiceNumber = $invoiceNumber;
        $this->checkNumber = $checkNumber;

    }

    public function getAmount() {
        return $this->amount;
    }

    public function getInvoice() {
        return $this->invoiceNumber;
    }


}
