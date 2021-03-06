<?php
/**
 * TokenTX.php
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
 * Description of TokenTX
 *
 * @author Eric
 */
class TokenTX {

    public static function CreditSaleToken(\PDO $dbh, $idGuest, $gwName, \CreditSaleTokenRequest $cstReq) {

        $uS = Session::getInstance();

        if (strtolower($gwName) == 'test') {
            $cstReq->setAddress('4')->setZip('30329')->setOperatorID('test');
        }

        $gway = Gateway::getGateway($dbh, $gwName);

        // Call to web service
        $creditResponse = $cstReq->submit($gway);

        $vr = new TokenResponse($creditResponse, $idGuest, $cstReq->getTokenId());


        // Save raw transaction in the db.
        Gateway::saveGwTx($dbh, $vr->response->getStatus(), json_encode($cstReq->getFieldsArray()), json_encode($vr->response->getResultArray()), 'CreditSaleToken');


        // New Token?
        if ($vr->response->getToken() != '') {
            $guestTokenRs = CreditToken::updateToken($dbh, $vr);
            $vr->cardNum = str_ireplace('x', '', $guestTokenRs->MaskedAccount->getStoredVal());
            $vr->cardName = $guestTokenRs->CardHolderName->getStoredVal();
            $vr->expDate = $guestTokenRs->ExpDate->getStoredVal();
            $vr->idToken = $guestTokenRs->idGuest_token->getStoredVal();
        }

        // Record transaction
        $transRs = Transaction::recordTransaction($dbh, $vr, $gwName, TransType::Sale, TransMethod::Token);
        $vr->idTrans = $transRs->idTrans->getStoredVal();


        // Record Payment
        return SaleReply::processReply($dbh, $vr, $uS->username);


    }


    public static function creditVoidSaleToken(\PDO $dbh, $idGuest, $gwName, \CreditVoidSaleTokenRequest $voidSale, $idPayment) {

        if ($idPayment == 0) {
            throw new Hk_Exception_Payment('DB Payment Id not given.  ');
        }

        $uS = Session::getInstance();

        if (strtolower($gwName) == 'test') {
            $voidSale->setOperatorID('test');
        }

        $gway = Gateway::getGateway($dbh, $gwName);

        // Call to web service
        $creditResponse = $voidSale->submit($gway);
        $vr = new TokenResponse($creditResponse, $idGuest, $voidSale->getTokenId());

        // Save raw transaction in the db.
        Gateway::saveGwTx($dbh, $vr->response->getStatus(), json_encode($voidSale->getFieldsArray()), json_encode($vr->response->getResultArray()), 'CreditVoidSaleToken');


        // New Token?
        if ($vr->response->getToken() != '') {
            $guestTokenRs = CreditToken::updateToken($dbh, $vr);
            $vr->cardNum = str_ireplace('x', '', $guestTokenRs->MaskedAccount->getStoredVal());
            $vr->cardName = $guestTokenRs->CardHolderName->getStoredVal();
            $vr->expDate = $guestTokenRs->ExpDate->getStoredVal();
            $vr->idToken = $guestTokenRs->idGuest_token->getStoredVal();
        }

        // Record transaction
        $transRs = Transaction::recordTransaction($dbh, $vr, $gwName, TransType::Void, TransMethod::Token);
        $vr->idTrans = $transRs->idTrans->getStoredVal();

        // Record payment
        return VoidReply::processReply($dbh, $vr, $uS->username, $idPayment);

    }


    public static function creditReturnToken(\PDO $dbh, $idGuest, $gwName, \CreditReturnTokenRequest $returnSale, $idPayment) {

        if ($idPayment == 0) {
            throw new Hk_Exception_Payment('DB Payment Id not given.  ');
        }

        $uS = Session::getInstance();

        if (strtolower($gwName) == 'test') {
            $returnSale->setOperatorID('test');
        }

        $gway = Gateway::getGateway($dbh, $gwName);

        // Call to web service
        $creditResponse = $returnSale->submit($gway);
        $vr = new TokenResponse($creditResponse, $idGuest, $returnSale->getTokenId());


        // Save raw transaction in the db.
        Gateway::saveGwTx($dbh, $vr->response->getStatus(), json_encode($returnSale->getFieldsArray()), json_encode($vr->response->getResultArray()), 'CreditReturnToken');


        // New Token?
        if ($vr->response->getToken() != '') {
            $guestTokenRs = CreditToken::updateToken($dbh, $vr);
            $vr->cardNum = str_ireplace('x', '', $guestTokenRs->MaskedAccount->getStoredVal());
            $vr->cardName = $guestTokenRs->CardHolderName->getStoredVal();
            $vr->expDate = $guestTokenRs->ExpDate->getStoredVal();
            $vr->idToken = $guestTokenRs->idGuest_token->getStoredVal();
        }


        // Record transaction
        $transRs = Transaction::recordTransaction($dbh, $vr, $gwName, TransType::Retrn, TransMethod::Token);
        $vr->idTrans = $transRs->idTrans->getStoredVal();

        // Record payment
        return ReturnReply::processReply($dbh, $vr, $uS->username, $idPayment);

    }

    public static function creditVoidReturnToken (\PDO $dbh, $idGuest, $gwName, \CreditVoidReturnTokenRequest $returnVoid, $idPayment) {

        if ($idPayment == 0) {
            throw new Hk_Exception_Payment('DB Payment Id not given.  ');
        }

        $uS = Session::getInstance();

        if (strtolower($gwName) == 'test') {
            $returnVoid->setOperatorID('test');
        }

        $gway = Gateway::getGateway($dbh, $gwName);

        // Call to web service
        $creditResponse = $returnVoid->submit($gway);
        $vr = new TokenResponse($creditResponse, $idGuest, $returnVoid->getTokenId());


        // Save raw transaction in the db.
        Gateway::saveGwTx($dbh, $vr->response->getStatus(), json_encode($returnVoid->getFieldsArray()), json_encode($vr->response->getResultArray()), 'CreditVoidReturnToken');

        // New Token?
        if ($vr->response->getToken() != '') {
            $guestTokenRs = CreditToken::updateToken($dbh, $vr);
            $vr->cardNum = str_ireplace('x', '', $guestTokenRs->MaskedAccount->getStoredVal());
            $vr->cardName = $guestTokenRs->CardHolderName->getStoredVal();
            $vr->expDate = $guestTokenRs->ExpDate->getStoredVal();
            $vr->idToken = $guestTokenRs->idGuest_token->getStoredVal();
        }


        // Record transaction
        $vr->transRs = Transaction::recordTransaction($dbh, $vr, $gwName, TransType::VoidReturn, TransMethod::Token);
        //$vr->idTrans = $transRs->idTrans->getStoredVal();

        // Record payment
        return VoidReturnReply::processReply($dbh, $vr, $uS->username, $idPayment);

    }
}



class TokenResponse extends PaymentResponse {

    public $invoiceNumber;

    function __construct(\CreditTokenResponse $creditTokenResponse, $idPayor, $idToken) {
        $this->response = $creditTokenResponse;
        $this->paymentType = PayType::Charge;
        $this->idPayor = $idPayor;
        $this->idToken = $idToken;
        $this->invoiceNumber = $creditTokenResponse->getInvoice();
    }


}
