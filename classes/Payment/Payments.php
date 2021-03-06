<?php
/**
 * Payments.php
 *
 *
 * @category  house
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link
 */


abstract class PaymentResponse {

    public $idPayor = 0;
    public $idVisit;
    public $idReservation;
    public $idRegistration;
    public $idTrans = 0;
    public $idToken = '';
    public $expDate = '';
    public $cardNum = '';
    public $cardName = '';
    public $paymentType;
    public $checkNumber = '';

    /**
     *
     * @var \PaymentRS
     */
    public $paymentRs;

    /**
     *
     * @var \Payment_AuthRS
     */
    public $paymentAuthRs;

    /**
     *
     * @var \MoneyRS
     */
    public $moneyRs;

    /**
     *
     * @var \MercResponse
     */
    public $response;

    public function getAmount() {
        return $this->response->getAuthorizeAmount();
    }

    public function getInvoice() {
        return $this->response->getInvoice();
    }

    public function getIdPayment() {

        if (is_null($this->paymentRs) === FALSE) {
            return $this->paymentRs->idPayment->getStoredVal();
        }

        return 0;
    }
}



/**
 * Description of Payments
 *
 * @author Eric
 */
abstract class Payments {

    public static function processReply(PDO $dbh, \PaymentResponse $pr, $userName, $idPayment = 0) {
        $vr = $pr->response;

        // Transaction status
        switch ($vr->getStatus()) {

            case MpStatusValues::Approved:
                $pr = static::caseApproved($dbh, $pr, $userName, $idPayment);
                break;

            case MpStatusValues::Declined:
                $pr = static::caseDeclined($dbh, $pr, $userName, $idPayment);
                break;

//            case MpStatusValues::Invalid:
//                // Indicates that the user entered invalid card data too many times and was therefore redirected back to the Merchants eCommerce site.
//                //throw new Hk_Exception_Payment("Repeated invalid account number entries.  " . $vr->getDisplayMessage());
//                break;
//
//            case MpStatusValues::Error:
//                // A transaction processing error occurred.
//                //throw new Hk_Exception_Payment("Transaction processing error.  Try again later.  " . $vr->getDisplayMessage());
//                break;
//
//            case MpStatusValues::AuthFail:
//                // Authentication failed for MerchantID/password.
//                //throw new Hk_Exception_Payment("Bad Merchant Id or password. ");
//                break;
//
//            case MpStatusValues::MercInternalFail:
//                // An error occurred internal to Mercury.
//                //throw new Hk_Exception_Payment("Mercury Internal Error.  Try again later. ");
//                break;
//
//            case MpStatusValues::ValidateFail:
//                // Validation of the request failed. See Message for validation errors.
//                //throw new Hk_Exception_Payment('Validation Fail: ' . $vr->getDisplayMessage());
//                break;

            default:
                static::caseOther($dbh, $pr, $userName, $idPayment);

        }

        return $pr;
    }

    protected static function caseApproved(PDO $dbh, \PaymentResponse $pr, $userName, $idPayment = 0) {}
    protected static function caseDeclined(PDO $dbh, \PaymentResponse $pr, $userName, $idPayment = 0) {
         return $pr;
    }
    protected static function caseOther(PDO $dbh, \PaymentResponse $pr, $userName, $idPayment = 0) {
         return $pr;
    }

}

class SaleReply extends Payments {


    protected static function caseApproved(PDO $dbh, \PaymentResponse $pr, $username, $idPayment = 0) {
        $vr = $pr->response;

        // Record Payment
        $payRs = new PaymentRS();
        $payRs->Amount->setNewVal($vr->getAuthorizeAmount());
        $payRs->Payment_Date->setNewVal(date("Y-m-d H:i:s"));
        $payRs->idPayor->setNewVal($pr->idPayor);
        $payRs->idTrans->setNewVal($pr->idTrans);
        $payRs->idToken->setNewVal($pr->idToken);
        $payRs->idPayment_Method->setNewVal(2);
        $payRs->Result->setNewVal(MpStatusValues::Approved);
        //$payRs->Attempt->setNewVal(1);
        $payRs->Status_Code->setNewVal(PaymentStatusCode::Paid);
        $payRs->Created_By->setNewVal($username);

        $idPayment = EditRS::insert($dbh, $payRs);
        $payRs->idPayment->setNewVal($idPayment);
        EditRS::updateStoredVals($payRs);
        $pr->paymentRs = $payRs;

        if ($idPayment > 0) {
            //Payment Detail
            $pDetailRS = new Payment_AuthRS();
            $pDetailRS->idPayment->setNewVal($idPayment);
            $pDetailRS->Approved_Amount->setNewVal($vr->getAuthorizeAmount());
            $pDetailRS->Approval_Code->setNewVal($vr->getAuthCode());
            $pDetailRS->Reference_Num->setNewVal($vr->getRefNo());
            $pDetailRS->Acct_Number->setNewVal($pr->cardNum);
            $pDetailRS->Card_Type->setNewVal($vr->getCardType());
            $pDetailRS->AVS->setNewVal($vr->getAVSResult());
            $pDetailRS->Invoice_Number->setNewVal($vr->getInvoice());
            $pDetailRS->idTrans->setNewVal($pr->idTrans);
            $pDetailRS->AcqRefData->setNewVal($vr->getAcqRefData());
            $pDetailRS->ProcessData->setNewVal($vr->getProcessData());
            $pDetailRS->Code3->setNewVal($vr->getCvvResult());

            $pDetailRS->Updated_By->setNewVal($username);
            $pDetailRS->Last_Updated->setNewVal(date("Y-m-d H:i:s"));
            $pDetailRS->Status_Code->setNewVal(PaymentStatusCode::Paid);

            $idPaymentAuth = EditRS::insert($dbh, $pDetailRS);
            $pDetailRS->idPayment_auth->setNewVal($idPaymentAuth);
            EditRS::updateStoredVals($pDetailRS);
            $pr->paymentAuthRs = $pDetailRS;

        }

        // Money table
        $moneyRs = new MoneyRS();
        $moneyRs->idPayment->setNewVal($idPayment);
        $moneyRs->Invoice_Number->setNewVal($vr->getInvoice());
        $moneyRs->Amount->setNewVal($vr->getAuthorizeAmount());
        $moneyRs->Transaction_Type->setNewVal(TransType::Sale);
        $moneyRs->Transaction_Date->setNewVal(date("Y-m-d H:i:s"));

        $monId = EditRS::insert($dbh, $moneyRs);
        $moneyRs->idMoney->setNewVal($monId);
        EditRS::updateStoredVals($moneyRs);

        $pr->moneyRs = $moneyRs;

        return $pr;
    }

    protected static function caseDeclined(PDO $dbh, PaymentResponse $pr, $username, $idPayment = 0) {

        $payRs = new PaymentRS();
        $payRs->Payment_Date->setNewVal(date("Y-m-d H:i:s"));
        $payRs->idPayor->setNewVal($pr->idPayor);
        $payRs->idToken->setNewVal($pr->idToken);
        $payRs->idTrans->setNewVal($pr->idTrans);
        $payRs->idPayment_Method->setNewVal(2);
        $payRs->Result->setNewVal(MpStatusValues::Declined);
        $payRs->Created_By->setNewVal($username);
        //$payRs->Attempt->setNewVal(1);

        $idPayment = EditRS::insert($dbh, $payRs);
        $payRs->idPayment->setNewVal($idPayment);
        EditRS::updateStoredVals($payRs);
        $pr->paymentRs = $payRs;

        return $pr;
    }

}


class VoidReply extends Payments {

    protected static function caseApproved(PDO $dbh, PaymentResponse $pr, $username, $idPayment = 0){

        if ($idPayment == 0) {
            throw new Hk_Exception_Payment('Payment Id not given.  ');
        }

        $vr = $pr->response;

        // Should be a payment
        $payRs = new PaymentRS();
        $payRs->idPayment->setStoredVal($idPayment);
        $rows = EditRS::select($dbh, $payRs, array($payRs->idPayment));

        if (count($rows) == 1) {

            // Payment record
            $payRs->Status_Code->setNewVal(PaymentStatusCode::VoidSale);
            $bal = $payRs->Balance->getStoredVal() + $vr->getAuthorizeAmount();
            $payRs->Balance->setNewVal($bal);
            $payRs->Updated_By->setNewVal($username);
            $payRs->Last_Updated->setNewVal(date('Y-m-d H:i:s'));

            EditRS::update($dbh, $payRs, array($payRs->idPayment));
            EditRS::updateStoredVals($payRs);
            $pr->paymentRs = $payRs;

            // Payment Detail
            $pDetailRS = new Payment_AuthRS();
            $pDetailRS->idPayment->setNewVal($idPayment);
            $pDetailRS->Approved_Amount->setNewVal($vr->getAuthorizeAmount());
            $pDetailRS->Approval_Code->setNewVal($vr->getAuthCode());
            $pDetailRS->Reference_Num->setNewVal($vr->getRefNo());
            $pDetailRS->AVS->setNewVal($vr->getAVSResult());
            $pDetailRS->Acct_Number->setNewVal($pr->cardNum);
            $pDetailRS->Card_Type->setNewVal($vr->getCardType());
            $pDetailRS->Invoice_Number->setNewVal($vr->getInvoice());
            $pDetailRS->idTrans->setNewVal($pr->idTrans);
            $pDetailRS->AcqRefData->setNewVal($vr->getAcqRefData());
            $pDetailRS->ProcessData->setNewVal($vr->getProcessData());
            $pDetailRS->Code3->setNewVal($vr->getCvvResult());

            $pDetailRS->Updated_By->setNewVal($username);
            $pDetailRS->Last_Updated->setNewVal(date("Y-m-d H:i:s"));
            $pDetailRS->Status_Code->setNewVal(PaymentStatusCode::VoidSale);

            $idPaymentAuth = EditRS::insert($dbh, $pDetailRS);
            $pDetailRS->idPayment_auth->setNewVal($idPaymentAuth);
            EditRS::updateStoredVals($pDetailRS);
            $pr->paymentAuthRs = $pDetailRS;


            // Money table
            $moneyRs = new MoneyRS();
            $moneyRs->idPayment->setNewVal($idPayment);
            $moneyRs->Invoice_Number->setNewVal($vr->getInvoice());
            $moneyRs->Amount->setNewVal($vr->getAuthorizeAmount());
            $moneyRs->Transaction_Type->setNewVal(TransType::Void);
            $moneyRs->Transaction_Date->setNewVal(date("Y-m-d H:i:s"));

            $monId = EditRS::insert($dbh, $moneyRs);
            $moneyRs->idMoney->setNewVal($monId);
            EditRS::updateStoredVals($moneyRs);

            $pr->moneyRs = $moneyRs;


        } else {
            throw new Hk_Exception_Payment('Payment Id not found. (' . $idPayment . ')');
        }
        return $pr;

    }

}

class ReturnReply extends Payments {

    protected static function caseApproved(PDO $dbh, PaymentResponse $pr, $username, $idPayment = 0){
        $vr = $pr->response;

        if ($idPayment == 0) {
            throw new Hk_Exception_Payment('Payment Id not given.  ');
        }

        // Should be a payment
        $payRs = new PaymentRS();
        $payRs->idPayment->setStoredVal($idPayment);
        $rows = EditRS::select($dbh, $payRs, array($payRs->idPayment));

        if (count($rows) == 1) {

            // Payment record
            $payRs->Status_Code->setNewVal(PaymentStatusCode::Retrn);
            $bal = $payRs->Balance->getStoredVal();
            $payRs->Balance->setNewVal($bal + $vr->getAuthorizeAmount());
            $payRs->Updated_By->setNewVal($username);
            $payRs->Last_Updated->setNewVal(date('Y-m-d H:i:s'));

            EditRS::update($dbh, $payRs, array($payRs->idPayment));
            EditRS::updateStoredVals($payRs);
            $pr->paymentRs = $payRs;

            if ($idPayment > 0) {
                //Payment Detail
                $pDetailRS = new Payment_AuthRS();
                $pDetailRS->idPayment->setNewVal($idPayment);
                //$pDetailRS->Processor->setNewVal($gw);
                $pDetailRS->Approved_Amount->setNewVal($vr->getAuthorizeAmount());
                $pDetailRS->Approval_Code->setNewVal($vr->getAuthCode());
                $pDetailRS->Reference_Num->setNewVal($vr->getRefNo());
                $pDetailRS->AVS->setNewVal($vr->getAVSResult());
                $pDetailRS->Acct_Number->setNewVal($pr->cardNum);
                $pDetailRS->Card_Type->setNewVal($vr->getCardType());
                $pDetailRS->Invoice_Number->setNewVal($vr->getInvoice());
                $pDetailRS->idTrans->setNewVal($pr->idTrans);
                $pDetailRS->AcqRefData->setNewVal($vr->getAcqRefData());
                $pDetailRS->ProcessData->setNewVal($vr->getProcessData());
                $pDetailRS->Code3->setNewVal($vr->getCvvResult());

                $pDetailRS->Updated_By->setNewVal($username);
                $pDetailRS->Last_Updated->setNewVal(date("Y-m-d H:i:s"));
                $pDetailRS->Status_Code->setNewVal(PaymentStatusCode::Retrn);

                $idPaymentAuth = EditRS::insert($dbh, $pDetailRS);
                $pDetailRS->idPayment_auth->setNewVal($idPaymentAuth);
                EditRS::updateStoredVals($pDetailRS);
                $pr->paymentAuthRs = $pDetailRS;

            }

            // Money table
            $moneyRs = new MoneyRS();
            $moneyRs->idPayment->setNewVal($idPayment);
            $moneyRs->Invoice_Number->setNewVal($vr->getInvoice());
            $moneyRs->Amount->setNewVal($vr->getAuthorizeAmount());
            $moneyRs->Transaction_Type->setNewVal(TransType::Retrn);
            $moneyRs->Transaction_Date->setNewVal(date("Y-m-d H:i:s"));

            $monId = EditRS::insert($dbh, $moneyRs);
            $moneyRs->idMoney->setNewVal($monId);
            EditRS::updateStoredVals($moneyRs);

            $pr->moneyRs = $moneyRs;
        } else {
            throw new Hk_Exception_Payment('Payment Id not found. (' . $idPayment . ')');
        }
        return $pr;

    }

}

class VoidReturnReply extends Payments {

    protected static function caseApproved(PDO $dbh, PaymentResponse $pr, $username, $idPayment = 0){
        $vr = $pr->response;

        if ($idPayment == 0) {
            throw new Hk_Exception_Payment('Payment Id is undefined (0).  ');
        }


        // Should be a payment
        $payRs = new PaymentRS();
        $payRs->idPayment->setStoredVal($idPayment);
        $rows = EditRS::select($dbh, $payRs, array($payRs->idPayment));

        if (count($rows) == 1) {

            // Payment Detail
            $pDetailRS = new Payment_AuthRS();
            $pDetailRS->idPayment->setNewVal($idPayment);
            $pDetailRS->Approved_Amount->setNewVal($vr->getAuthorizeAmount());
            $pDetailRS->Approval_Code->setNewVal($vr->getAuthCode());
            $pDetailRS->Reference_Num->setNewVal($vr->getRefNo());
            $pDetailRS->AVS->setNewVal($vr->getAVSResult());
            $pDetailRS->Acct_Number->setNewVal($pr->cardNum);
            $pDetailRS->Card_Type->setNewVal($vr->getCardType());
            $pDetailRS->Invoice_Number->setNewVal($vr->getInvoice());
            $pDetailRS->idTrans->setNewVal($pr->idTrans);
            $pDetailRS->AcqRefData->setNewVal($vr->getAcqRefData());
            $pDetailRS->ProcessData->setNewVal($vr->getProcessData());
            $pDetailRS->Code3->setNewVal($vr->getCvvResult());

            $pDetailRS->Updated_By->setNewVal($username);
            $pDetailRS->Last_Updated->setNewVal(date("Y-m-d H:i:s"));
            $pDetailRS->Status_Code->setNewVal(PaymentStatusCode::VoidReturn);

            $idPaymentAuth = EditRS::insert($dbh, $pDetailRS);
            $pDetailRS->idPayment_auth->setNewVal($idPaymentAuth);
            EditRS::updateStoredVals($pDetailRS);
            $pr->paymentAuthRs = $pDetailRS;

            // Payment record
            $payRs->Status_Code->setNewVal(PaymentStatusCode::VoidReturn);
            $bal = $payRs->Balance->getStoredVal() - $vr->getAuthorizeAmount();
            if ($bal < 0) {
                throw new Hk_Exception_Payment('Void Return auth amount is greater than recorded payment balance.  ');
            }
            $payRs->Balance->setNewVal($bal);
            $payRs->Updated_By->setNewVal($username);
            $payRs->Last_Updated->setNewVal(date('Y-m-d H:i:s'));

            EditRS::update($dbh, $payRs, array($payRs->idPayment));
            EditRS::updateStoredVals($payRs);
            $pr->paymentRs = $payRs;

            // Money table
            $moneyRs = new MoneyRS();
            $moneyRs->idPayment->setNewVal($idPayment);
            $moneyRs->Invoice_Number->setNewVal($vr->getInvoice());
            $moneyRs->Amount->setNewVal($vr->getAuthorizeAmount());
            $moneyRs->Transaction_Type->setNewVal(TransType::VoidReturn);
            $moneyRs->Transaction_Date->setNewVal(date("Y-m-d H:i:s"));

            $monId = EditRS::insert($dbh, $moneyRs);
            $moneyRs->idMoney->setNewVal($monId);
            EditRS::updateStoredVals($moneyRs);

            $pr->moneyRs = $moneyRs;
        } else {
            throw new Hk_Exception_Payment('Payment Id not found.  ');
        }

        return $pr;

    }

}
