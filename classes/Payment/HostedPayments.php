<?php
/**
 * HostedPayments.php
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
 * CardInfo - Create markup for  the Hosted CC portal.
 *
 * @author Eric
 */
class CardInfo {

    public static function sendToPortal(PDO $dbh, $gw, $idPayor, $idGroup, InitCiRequest $initCi) {

        $dataArray = array();

        if (strtolower($gw) == 'test') {
            $initCi->setOperatorID('test');
        }

        $ciResponse = $initCi->submit(Gateway::getGateway($dbh, $gw));

        // Save raw transaction in the db.
        try {
            Gateway::saveGwTx($dbh, $ciResponse->getResponseCode(), json_encode($initCi->getFieldsArray()), json_encode($ciResponse->getResultArray()), 'CardInfoInit');
        } catch(Exception $ex) {
            // Do Nothing
        }

        if ($ciResponse->getResponseCode() == 0) {

            // Save the CardID in the database indexed by the guest id.
            $ciq = "replace into `card_id` (`idName`, `idGroup`, `Transaction`, `CardID`, `Init_Date`, `Frequency`, `ResponseCode`)"
                . " values ($idPayor, $idGroup, 'cof', '" . $ciResponse->getCardId() . "', now(), 'OneTime', '" . $ciResponse->getResponseCode() . "')";

            $dbh->exec($ciq);

            $dataArray = array('xfer' => $ciResponse->getCardInfoUrl(), 'cardId' => $ciResponse->getCardId());

        } else {

            // The initialization failed.
            throw new Hk_Exception_Payment("Card-On-File Gateway Error: " . $ciResponse->getResponseText());

        }

        return $dataArray;
    }


    public static function portalReply(PDO $dbh, $gw, $cardId, $post) {

        $cidInfo = PaymentSvcs::getInfoFromCardId($dbh, $cardId);

        $verify = new VerifyCIRequest();
        $verify->setCardId($cardId);

        // Verify request
        $verifyResponse = $verify->submit(Gateway::getGateway($dbh, $gw));
        $vr = new CardInfoResponse($verifyResponse, $cidInfo['idName'], $cidInfo['idGroup']);

        // Save raw transaction in the db.
        try {
            Gateway::saveGwTx($dbh, $vr->response->getStatus(), json_encode($verify->getFieldsArray()), json_encode($vr->response->getResultArray()), 'CardInfoVerify');
        } catch(Exception $ex) {
            // Do Nothing
        }


        if ($vr->response->getResponseCode() == 0 && $vr->response->getStatus() == MpStatusValues::Approved) {

            if ($vr->response->getToken() != '') {

                try {
                    $vr->idToken = CreditToken::storeToken($dbh, $vr->idRegistration, $vr->idPayor, $vr->response);
                } catch(Exception $ex) {
                    $vr->idToken = 0;
                }

            } else {
                $vr->idToken = 0;
            }
        }

        return $vr;

    }
}

class CardInfoResponse extends PaymentResponse {


    function __construct(\VerifyCiResponse $verifyCiResponse, $idPayor, $idGroup) {
        $this->response = $verifyCiResponse;
        $this->idPayor = $idPayor;
        $this->idRegistration = $idGroup;
        $this->expDate = $verifyCiResponse->getExpDate();
        $this->cardNum = str_ireplace('x', '', $verifyCiResponse->getMaskedAccount());
    }


}


class HostedCheckout {

    public static function sendToPortal(PDO $dbh, $gw, $idPayor, $idGroup, $invoiceNumber, InitCkOutRequest $initCoRequest) {

        $dataArray = array();

        if (strtolower($gw) == 'test') {
            $initCoRequest->setAVSAddress('4')->setAVSZip('30329');
            $initCoRequest->setOperatorID('test');
        }


        $ciResponse = $initCoRequest->submit(Gateway::getGateway($dbh, $gw));

        // Save raw transaction in the db.
        try {
            Gateway::saveGwTx($dbh, $ciResponse->getResponseCode(), json_encode($initCoRequest->getFieldsArray()), json_encode($ciResponse->getResultArray()), 'HostedCoInit');
        } catch(Exception $ex) {
            // Do Nothing
        }


        if ($ciResponse->getResponseCode() == 0) {

            // Save payment ID
            $ciq = "replace into card_id (idName, `idGroup`, `Transaction`, InvoiceNumber, CardID, Init_Date, Frequency, ResponseCode)"
                . " values ($idPayor, $idGroup, 'hco', '$invoiceNumber', '" . $ciResponse->getPaymentId() . "', now(), 'OneTime', '" . $ciResponse->getResponseCode() . "')";

            $dbh->exec($ciq);

            $dataArray = array('xfer' => $ciResponse->getCheckoutUrl(), 'paymentId' => $ciResponse->getPaymentId());

        } else {

            // The initialization failed.
            throw new Hk_Exception_Payment("Credit Payment Gateway Error: " . $ciResponse->getResponseText());

        }


        return $dataArray;
    }

    public static function portalReply(PDO $dbh, $gw, $paymentId) {

        $uS = Session::getInstance();

        // Check paymentId
        $cidInfo = PaymentSvcs::getInfoFromCardId($dbh, $paymentId);

        // setup the verify request
        $verify = new VerifyCkOutRequest();
        $verify->setPaymentId($paymentId);

        // Verify request
        $verifyResponse = $verify->submit(Gateway::getGateway($dbh, $gw));
        $vr = new CheckOutResponse($verifyResponse, $cidInfo['idName'], $cidInfo['idGroup'], $cidInfo['InvoiceNumber']);


        // Save raw transaction in the db.
        try {
            Gateway::saveGwTx($dbh, $vr->response->getStatus(), json_encode($verify->getFieldsArray()), json_encode($vr->response->getResultArray()), 'HostedCoVerify');
        } catch(Exception $ex) {
            // Do Nothing
        }

        // Record transaction
        try {

            $transRs = Transaction::recordTransaction($dbh, $vr, $gw, TransType::Sale, TransMethod::HostedPayment);
            $vr->idTrans = $transRs->idTrans->getStoredVal();

        } catch(Exception $ex) {
            $vr->idTrans = 0;
        }

        // Save token
        if ($vr->response->getToken() != '') {

            try {
                $vr->idToken = CreditToken::storeToken($dbh, $vr->idRegistration, $vr->idPayor, $vr->response);
            } catch(Exception $ex) {
                $vr->idToken = 0;
            }

        } else {
            $vr->idToken = 0;
        }

        // record payment
        return SaleReply::processReply($dbh, $vr, $uS->username);

    }

}


class CheckOutResponse extends PaymentResponse {

    public $invoiceNumber;

    function __construct(\VerifyCkOutResponse $verifyCkOutResponse, $idPayor, $idGroup, $invoiceNumber) {
        $this->response = $verifyCkOutResponse;
        $this->paymentType = PayType::Charge;
        $this->idPayor = $idPayor;
        $this->idRegistration = $idGroup;
        $this->invoiceNumber = $invoiceNumber;
        $this->expDate = $verifyCkOutResponse->getExpDate();
        $this->cardNum = str_ireplace('x', '', $verifyCkOutResponse->getMaskedAccount());
    }

}


