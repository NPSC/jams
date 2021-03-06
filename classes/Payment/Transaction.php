<?php
/**
 * Transaction.php
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
 * Description of Transaction
 *
 * @author Eric
 */
class Transaction {

    public static function recordTransaction(\PDO $dbh, \PaymentResponse $vr, $gwName, $transType, $transMethod) {

        // Record transaction
        $transRs = new TransRs();

        $transRs->Amount->setNewVal($vr->getAmount());
        $transRs->Card_Number->setNewVal($vr->cardNum);
        $transRs->Card_Expire->setNewVal($vr->expDate);
        $transRs->Card_Name->setNewVal($vr->cardName);
        $transRs->Invoice_Number->setNewVal($vr->getInvoice());
        $transRs->Date_Entered->setNewVal(date("Y-m-d H:i:s"));
        $transRs->Payment_Type->setNewVal($vr->paymentType);
        $transRs->idName->setNewVal($vr->idPayor);
        $transRs->Trans_Date->setNewVal(date("Y-m-d H:i:s"));
        $transRs->Gateway_Ref->setNewVal($gwName);
        $transRs->Trans_Type->setNewVal($transType);
        $transRs->Trans_Method->setNewVal($transMethod);
        $transRs->Check_Number->setNewVal($vr->checkNumber);

        if (is_null($vr->response) === FALSE) {
            $transRs->Payment_Status->setNewVal($vr->response->getStatus());
            $transRs->Card_Authorize->setNewVal($vr->response->getAuthCode());
            $transRs->RefNo->setNewVal($vr->response->getRefNo());
            $transRs->Process_Code->setNewVal($vr->response->getProcessData());
        }

        $idTrans = EditRS::insert($dbh, $transRs);
        $transRs->idTrans->setNewVal($idTrans);
        EditRS::updateStoredVals($transRs);

        return $transRs;

    }


}
