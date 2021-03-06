<?php

/**
 * PaymentSvcs.php
 *
 *
 * @category  House
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

class PaymentResult {

    protected $displayMessage = '';
    protected $status = '';
    protected $idName = 0;
    protected $idRegistration = 0;

    protected $receiptMarkup = '';
    protected $forwardHostedPayment;
    protected $idInvoice = 0;

    const ACCEPTED = 'a';
    const DENIED = 'd';
    const ERROR = 'e';
    const FORWARDED = 'f';

    function __construct($idInvoice, $idRegistration, $idName) {

        $this->idRegistration = $idRegistration;
        $this->idInvoice = $idInvoice;
        $this->idName = $idName;
        $this->forwardHostedPayment = array();

    }

    public function feePaymentAccepted(PDO $dbh, \Session $uS, PaymentResponse $payResp, \Invoice $invoice) {

        // set status
        $this->status = PaymentResult::ACCEPTED;

        // update fees
        Fees::updateFeesPaymentStatus($dbh, $payResp->getIdPayment(), $this->idInvoice, $payResp->getAmount(), $uS->username, FeesPaymentStatus::Cleared);

        $hsNames = '';

        // Find the hospital
        if ($invoice->getOrderNumber() > 0) {

            try {

                $stmt = $dbh->prepare("select
    ifnull(ha.Title, '') as `Assoc`,
    ifnull(hh.Title, '') as `Hospital`
from
    visit v
        left join
    hospital_stay hs ON v.idHospital_stay = hs.idHospital_stay
        left join
    hospital ha ON hs.idAssociation = ha.idHospital
        left join
    hospital hh ON hs.idHospital = hh.idHospital
where
    v.idVisit = :idv");

                $stmt->execute(array(':idv'=>$invoice->getOrderNumber()));

                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    if ($r['Assoc'] != '' && $r['Assoc'] != '(None)') {
                        $hsNames = $r['Assoc'] . '/' . $r['Hospital'];
                    } else {
                        $hsNames = $r['Hospital'];
                    }
                }
            } catch (PDOException $pex) {
                // do nothing.
            }
        }

        // Make out receipt
        $this->receiptMarkup = Receipt::createSaleMarkup($dbh, $uS->siteName, $uS->sId, $uS->resourceURL . 'images/receiptlogo.png', $payResp, $hsNames);

        // Email receipt
        try {
            $this->displayMessage .= $this->emailReceipt($dbh);
        } catch (Exception $ex) {
            $this->displayMessage .= "Email Failed, Error = " . $ex->getMessage();
        }

    }

    public function feePaymentRejected(PDO $dbh, \Session $uS, PaymentResponse $payResp) {


        if ($this->getStatus() == self::DENIED) {
            $feeStatus = FeesPaymentStatus::Denied;
        } else {
            $feeStatus = FeesPaymentStatus::Error;
        }

        // update fees
        Fees::updateFeesPaymentStatus($dbh, $payResp->getIdPayment(), $this->idInvoice, 0, $uS->username, $feeStatus);

    }

    public function feePaymentError(PDO $dbh, \Session $uS) {

        // update fees
        Fees::updateFeesPaymentStatus($dbh, 0, $this->idInvoice, 0, $uS->username, FeesPaymentStatus::Error);

    }

    public function emailReceipt(\PDO $dbh) {

        $query = "Select ne.Email, r.Email_Receipt from `registration` r, `name` n left join `name_email` ne on n.idName = ne.idName and n.Preferred_Email = ne.Purpose"
                . " where r.idregistration = :idreg and n.idName = :id";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array(':idreg'=>$this->idRegistration, ':id'=>$this->idName));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {

            $emAddr = $rows[0]['Email'];
            $emFlag = $rows[0]['Email_Receipt'];

            if ($emFlag == 1 && $emAddr != '') {

                $config = new Config_Lite(ciCFG_FILE);

                $mail = prepareEmail($config);

                $mail->From = $config->getString('guest_email', 'FromAddress', '');
                $mail->addReplyTo($config->getString('guest_email', 'FromAddress', ''));
                $mail->FromName = $config->getString('site', 'Site_Name', '');
                $mail->addAddress($emAddr);     // Add a recipient

                $bccEntry = $config->getString('guest_email', 'BccAddress', '');
                $bccs = explode(',', $bccEntry);

                foreach ($bccs as $b) {
                    $bcc = filter_var($b, FILTER_SANITIZE_EMAIL);
                    if ($bcc !== FALSE && $bcc != '') {
                        $mail->addBCC($bcc);
                    }
                }

                $mail->isHTML(true);

                $mail->Subject = $config->getString('site', 'Site_Name', '') . ' Payment Receipt';
                $mail->msgHTML($this->receiptMarkup);


                if($mail->send()) {
                    $this->displayMessage .= "Email sent.  ";
                } else {
                    $this->displayMessage .= "Send Email failed!  " . $mail->ErrorInfo;
                }
            }
        } else {
            $this->displayMessage .= "Email Receipt is not checked.  ";
        }
    }

    public function wasError() {
        if ($this->getStatus() == self::ERROR) {
            return TRUE;
        }
        return FALSE;
    }

    public function getReceiptMarkup() {
        return $this->receiptMarkup;
    }

    public function getDisplayMessage() {
        return $this->displayMessage;
    }

    public function getIdInvoice() {
        return $this->idInvoice;
    }

    public function setDisplayMessage($displayMessage) {
        $this->displayMessage = $displayMessage . $this->displayMessage;
        return $this;
    }

    public function getForwardHostedPayment() {
        return $this->forwardHostedPayment;
    }

    public function setForwardHostedPayment(array $fwdHostedPayment) {
        $this->setStatus(PaymentResult::FORWARDED);
        $this->forwardHostedPayment = $fwdHostedPayment;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($s) {
        $this->status = $s;
        return $this;
    }

    public function getIdName() {
        return $this->idName;
    }

    public function getIdRegistration() {
        return $this->idRegistration;
    }

}

class cofResult extends PaymentResult {

    function __construct($displayMessage, $status, $idName, $idRegistration) {

        parent::__construct(0, $idRegistration, $idName);

        $this->displayMessage = $displayMessage;
        $this->status = $status;

    }

}



/**
 * Description of PaymentSvcs
 *
 * @author Eric
 */
class PaymentSvcs {

    public static function initCardOnFile(PDO $dbh, $gw, $pageTitle, $idGuest, $idGroup, $cardHolderName, $postBackPage) {

        $uS = Session::getInstance();

        $config = new Config_Lite(ciCFG_FILE);
        $houseUrl = $config->getString('site', 'House_URL', '');
        $siteUrl = $config->getString('site', 'Site_URL', '');
        $logo = $uS->PaymentLogoUrl;

        if ($houseUrl == '' || $siteUrl == '') {
            throw new Hk_Exception_Runtime("The site/house URL is missing.  ");
        }

        if ($idGuest < 1 || $idGroup < 1) {
            throw new Hk_Exception_Runtime("Card Holder information is missing.  ");
        }


        $initCi = new InitCiRequest($pageTitle, 'Custom');

        $initCi->setCardHolderName($cardHolderName)
                ->setFrequency(MpFrequencyValues::OneTime)
                ->setCompleteURL($houseUrl . $postBackPage)
                ->setReturnURL($houseUrl . $postBackPage)
                ->setLogoUrl($siteUrl . $logo);


        return CardInfo::sendToPortal($dbh, $gw, $idGuest, $idGroup, $initCi);
    }

    protected static function initHostedPayment(PDO $dbh, $gw, $pageTitle, \Invoice $invoice, $cardHolderName, $address, $zipCode, $postPage) {

        $uS = Session::getInstance();

        // Do a hosted payment.
        $config = new Config_Lite(ciCFG_FILE);
        $houseUrl = $config->getString('site', 'House_URL', '');
        $siteUrl = $config->getString('site', 'Site_URL', '');
        $logo = $uS->PaymentLogoUrl;

        if ($houseUrl == '' || $siteUrl == '') {
            throw new Hk_Exception_Runtime("The site/house URL is missing.  ");
        }

        if ($invoice->getSoldToId() < 1 || $invoice->getIdGroup() < 1) {
            throw new Hk_Exception_Runtime("Card Holder information is missing.  ");
        }

        $pay = new InitCkOutRequest($pageTitle, 'Custom');

        $pay    ->setAVSZip($zipCode)
                ->setAVSAddress($address)
                ->setCardHolderName($cardHolderName)
                ->setFrequency(MpFrequencyValues::OneTime)
                ->setInvoice($invoice->getInvoiceNumber())
                ->setMemo(MpVersion::PosVersion)
                ->setTaxAmount(0)
                ->setTotalAmount($invoice->getBalance())
                ->setCompleteURL($houseUrl . $postPage)
                ->setReturnURL($houseUrl . $postPage)
                ->setTranType(MpTranType::Sale)
                ->setLogoUrl($siteUrl . $logo)
                ->setCVV('on')
                ->setAVSFields('both');

        $CreditCheckOut = HostedCheckout::sendToPortal($dbh, $gw, $invoice->getSoldToId(), $invoice->getIdGroup(), $invoice->getInvoiceNumber(), $pay);

        return $CreditCheckOut;

    }


    public static function payAmount(\PDO $dbh, \Invoice $invoice, $payType, $idToken, $postPage) {

        $uS = Session::getInstance();

        $amount = $invoice->getBalance();

        if ($amount <= 0) {
            throw new Hk_Exception_Payment('The payment amount is 0 or less.');
        }



        if ($payType == PayType::Charge) {

            if ($uS->ccgw == '') {
                throw new Hk_Exception_Payment('Payment Gateway is not defined.');
            }

            $guest = new Guest($dbh, '', $invoice->getSoldToId());
            $addr = $guest->getAddrObj()->get_data($guest->getAddrObj()->get_preferredCode());

            $tokenRS = CreditToken::getTokenRsFromId($dbh, $idToken);

            // Do we have a token?
            if (CreditToken::hasToken($tokenRS)) {

                $cpay = new CreditSaleTokenRequest();

                $cpay->setPurchaseAmount($amount)
                    ->setTaxAmount(0)
                    ->setCustomerCode($invoice->getSoldToId())
                    ->setAddress($addr["Address_1"])
                    ->setZip($addr["Postal_Code"])
                    ->setToken($tokenRS->Token->getStoredVal())
                    ->setPartialAuth(FALSE)
                    ->setCardHolderName($tokenRS->CardHolderName->getStoredVal())
                    ->setFrequency(MpFrequencyValues::OneTime)
                    ->setInvoice($invoice->getInvoiceNumber())
                    ->setTokenId($tokenRS->idGuest_token->getStoredVal())
                    ->setMemo(MpVersion::PosVersion);

                // Run the token transaction
                $csResp = TokenTX::CreditSaleToken($dbh, $invoice->getSoldToId(), $uS->ccgw, $cpay);

                // Analyze the result
                $payResult = self::AnalyzeCredSaleResult($dbh, $csResp, $invoice);


            } else {

                // Initialiaze hosted payment
                $fwrder = self::initHostedPayment($dbh, $uS->ccgw, $uS->siteName, $invoice, '', $addr['Address_1'], $addr["Postal_Code"], $postPage);

                $payResult = new PaymentResult($invoice->getIdInvoice(), $invoice->getIdGroup(), $invoice->getSoldToId());
                $payResult->setForwardHostedPayment($fwrder);
                $payResult->setDisplayMessage('Forward to Payment Page. ');

            }

        } else if ($payType == PayType::Cash) {

            $cashResp = new CashResponse($amount, $invoice->getSoldToId(), $invoice->getInvoiceNumber());

            $cr = CashTX::cashSale($dbh, $cashResp, $uS->username);

            // Update invoice
            $invoice->updateInvoice($dbh, $cr->getAmount(), $uS->username);

            $payResult = new PaymentResult($invoice->getIdInvoice(), $invoice->getIdGroup(), $invoice->getSoldToId());
            $payResult->feePaymentAccepted($dbh, $uS, $cr, $invoice);
            $payResult->setDisplayMessage('Cash Fees Paid.  ');


        } else if ($payType == PayType::Check) {

            $ckResp = new CheckResponse($amount, $invoice->getSoldToId(), $invoice->getInvoiceNumber());

            $cr = CheckTX::checkSale($dbh, $ckResp, $uS->username);

            // Update invoice
            $invoice->updateInvoice($dbh, $cr->getAmount(), $uS->username);

            $payResult = new PaymentResult($invoice->getIdInvoice(), $invoice->getIdGroup(), $invoice->getSoldToId());
            $payResult->feePaymentAccepted($dbh, $uS, $cr, $invoice);
            $payResult->setDisplayMessage('Fees Paid by Check.  ');

        }

        return $payResult;
    }

    public static function voidFees(\PDO $dbh, $idFees, $bid) {

        $uS = Session::getInstance();
        $dataArray = array('bid' => $bid);
        $reply = '';

        $feeRs = new FeesRS();
        $feeRs->idFees->setStoredVal($idFees);
        $fees = EditRS::select($dbh, $feeRs, array($feeRs->idFees));

        if (count($fees) != 1) {
            throw new Hk_Exception_Payment('Fees record not found.  ');
        }

        // Load data into the record source record.
        EditRS::loadRow($fees[0], $feeRs);

        if ($feeRs->idPayment->getStoredVal() == 0) {
            throw new Hk_Exception_Payment('Payment record not defined.  ');
        }

        $payRs = new PaymentRS();
        $payRs->idPayment->setStoredVal($feeRs->idPayment->getStoredVal());
        $pments = EditRS::select($dbh, $payRs, array($payRs->idPayment));

        if (count($pments) != 1) {
            throw new Hk_Exception_Payment('Payment record not found.  ');
        }

        EditRS::loadRow($pments[0], $payRs);

        // Already voided, or otherwise ineligible
        if ($payRs->Status_Code->getStoredVal() != PaymentStatusCode::Paid) {
            return array('warning' => 'Payment is ineligable for void.  ', 'bid' => $bid);
        }

        // Get the invoice record
        $invoice = new Invoice($dbh);
        $invoice->loadInvoice($dbh, $feeRs->idInvoice->getStoredVal());

        switch ($feeRs->Pay_Type->getStoredVal()) {

            case PayType::Charge:

                // find the token record
                if ($payRs->idToken->getStoredVal() > 0) {
                    $tknRs = CreditToken::getTokenRsFromId($dbh, $payRs->idToken->getStoredVal());
                } else {
                    return array('warning' => 'Payment Token Id not found.  Unable to Void this purchase.  ', 'bid' => $bid);
                }

                if (CreditToken::hasToken($tknRs) === FALSE) {
                    return array('warning' => 'Payment Token not found.  Unable to Void this purchase.  ', 'bid' => $bid);
                }

                // Find hte detail record.
                $pAuthRs = new Payment_AuthRS();
                $pAuthRs->idPayment->setStoredVal($payRs->idPayment->getStoredVal());
                $arows = EditRS::select($dbh, $pAuthRs, array($pAuthRs->idPayment));

                if (count($arows) != 1) {
                    throw new Hk_Exception_Payment('Payment Detail record not found.  Unable to Void this purchase. ');
                }

                EditRS::loadRow($arows[0], $pAuthRs);


                // Set up request
                $voidRequest = new CreditVoidSaleTokenRequest();
                $voidRequest->setAuthCode($pAuthRs->Approval_Code->getStoredVal());
                $voidRequest->setCardHolderName($tknRs->CardHolderName->getStoredVal());
                $voidRequest->setFrequency(MpFrequencyValues::OneTime)->setMemo(MpVersion::PosVersion);
                $voidRequest->setInvoice($invoice->getInvoiceNumber());
                $voidRequest->setPurchaseAmount($pAuthRs->Approved_Amount->getStoredVal());
                $voidRequest->setRefNo($pAuthRs->Reference_Num->getStoredVal());
                $voidRequest->setToken($tknRs->Token->getStoredVal());
                $voidRequest->setTokenId($tknRs->idGuest_token->getStoredVal());

                try {

                    $csResp = TokenTX::creditVoidSaleToken($dbh, $payRs->idPayor->getstoredVal(), $uS->ccgw, $voidRequest, $payRs->idPayment->getStoredVal());

                    switch ($csResp->response->getStatus()) {

                        case MpStatusValues::Approved:

                            // Update invoice
                            $invoice->updateInvoice($dbh, 0, $uS->username);

                            // update fees
                            Fees::updateFeesPaymentStatus($dbh, $csResp->getIdPayment(), $invoice->getIdInvoice(), $csResp->response->getAuthorizeAmount(), $uS->username, FeesPaymentStatus::Void);
                            $reply .= 'Payment is void.  ';
                            $csResp->idVisit = $feeRs->idVisit->getStoredVal();
                            $dataArray['receipt'] = HTMLContainer::generateMarkup('div', nl2br(Receipt::createVoidMarkup($dbh, $csResp, $uS->resourceURL . 'images/receiptlogo.png', $uS->siteName, $uS->sId)));

                            break;

                        case MpStatusValues::Declined:

                            if ($csResp->response->getMessage() == 'ITEM VOIDED') {
                                $feeStatus = FeesPaymentStatus::Void;
                                Fees::updateFeesPaymentStatus($dbh, $csResp->getIdPayment(), $invoice->getIdInvoice(), 0, $uS->username, $feeStatus);
                            }
                            return array('warning' => $csResp->response->getMessage(), 'bid' => $bid);

                            break;

                        default:

                            return array('warning' => '** Void Invalid or Error. **  ' . 'Message: ' . $csResp->response->getMessage(), 'bid' => $bid);
                    }
                } catch (Hk_Exception_Payment $exPay) {

                    return array('warning' => "Payment Error = " . $exPay->getMessage(), 'bid' => $bid);
                }
                break;

            case PayType::Cash:

                $cashResp = new CashResponse($payRs->Amount->getStoredVal(), $payRs->idPayor->getStoredVal(), $invoice->getInvoiceNumber());

                $cr = CashTX::cashVoid($dbh, $cashResp, $uS->username, $payRs->idPayment->getStoredVal());

                // Update invoice
                $invoice->updateInvoice($dbh, 0, $uS->username);

                // update fees
                Fees::updateFeesPaymentStatus($dbh, $cr->getIdPayment(), $invoice->getIdInvoice(), $cr->getAmount(), $uS->username, FeesPaymentStatus::Void);
                $reply .= 'Payment is void.  ';

                $cr->idVisit = $feeRs->idVisit->getStoredVal();
                $dataArray['receipt'] = HTMLContainer::generateMarkup('div', nl2br(Receipt::createVoidMarkup($dbh, $cr, $uS->resourceURL . 'images/receiptlogo.png', $uS->siteName, $uS->sId)));

                break;

            case PayType::Check:

                $cashResp = new CheckResponse($payRs->Amount->getStoredVal(), $payRs->idPayor->getStoredVal(), $invoice->getInvoiceNumber());

                $cr = CheckTX::checkVoid($dbh, $cashResp, $uS->username, $payRs->idPayment->getStoredVal());

                // Update invoice
                $invoice->updateInvoice($dbh, 0, $uS->username);

                // update fees
                Fees::updateFeesPaymentStatus($dbh, $cr->getIdPayment(), $invoice->getIdInvoice(), $cr->getAmount(), $uS->username, FeesPaymentStatus::Void);
                $reply .= 'Payment is void.  ';

                $cr->idVisit = $feeRs->idVisit->getStoredVal();
                $dataArray['receipt'] = HTMLContainer::generateMarkup('div', nl2br(Receipt::createVoidMarkup($dbh, $cr, $uS->resourceURL . 'images/receiptlogo.png', $uS->siteName, $uS->sId)));

                break;

            default:
                throw new Hk_Exception_Payment('Unknown pay type.  ');
        }

        $dataArray['success'] = $reply;
        return $dataArray;
    }

    public static function returnFees(\PDO $dbh, $idFees, $bid) {

        $uS = Session::getInstance();
        $dataArray = array('bid' => $bid);
        $reply = '';

        // Fees record
        $feeRs = new FeesRS();
        $feeRs->idFees->setStoredVal($idFees);
        $fees = EditRS::select($dbh, $feeRs, array($feeRs->idFees));

        if (count($fees) != 1) {
            throw new Hk_Exception_Payment('Fees record not found.  ');
        }

        // Load data into the record source record.
        EditRS::loadRow($fees[0], $feeRs);

        if ($feeRs->idPayment->getStoredVal() == 0) {
            throw new Hk_Exception_Payment('Payment record not defined.  ');
        }

        $payRs = new PaymentRS();
        $payRs->idPayment->setStoredVal($feeRs->idPayment->getStoredVal());
        $pments = EditRS::select($dbh, $payRs, array($payRs->idPayment));

        if (count($pments) != 1) {
            throw new Hk_Exception_Payment('Payment record not found.  ');
        }

        EditRS::loadRow($pments[0], $payRs);

        // Already voided, or otherwise ineligible
        if ($payRs->Status_Code->getStoredVal() != PaymentStatusCode::Paid) {
            return array('warning' => 'Payment is ineligable for return.  ', 'bid' => $bid);
        }

        // Get the invoice record
        $invoice = new Invoice($dbh);
        $invoice->loadInvoice($dbh, $feeRs->idInvoice->getStoredVal());



        switch ($feeRs->Pay_Type->getStoredVal()) {

            case PayType::Charge:

                // find the token
                if ($payRs->idToken->getStoredVal() > 0) {
                    $tknRs = CreditToken::getTokenRsFromId($dbh, $payRs->idToken->getStoredVal());
                } else {
                    return array('warning' => 'Payment Token not found.  ', 'bid' => $bid);
                }


                // Find hte detail record.
                $pAuthRs = new Payment_AuthRS();
                $pAuthRs->idPayment->setStoredVal($payRs->idPayment->getStoredVal());
                $arows = EditRS::select($dbh, $pAuthRs, array($pAuthRs->idPayment));

                if (count($arows) != 1) {
                    throw new Hk_Exception_Payment('Payment Detail record not found. ');
                }

                EditRS::loadRow($arows[0], $pAuthRs);


                // Set up request
                $returnRequest = new CreditReturnTokenRequest();
                $returnRequest->setCardHolderName($tknRs->CardHolderName->getStoredVal());
                $returnRequest->setFrequency(MpFrequencyValues::OneTime)->setMemo(MpVersion::PosVersion);
                $returnRequest->setInvoice($invoice->getInvoiceNumber());
                $returnRequest->setPurchaseAmount($pAuthRs->Approved_Amount->getStoredVal());
                $returnRequest->setToken($tknRs->Token->getStoredVal());
                $returnRequest->setTokenId($tknRs->idGuest_token->getStoredVal());

                try {

                    $csResp = TokenTX::creditReturnToken($dbh, $payRs->idPayor->getstoredVal(), $uS->ccgw, $returnRequest, $payRs->idPayment->getStoredVal());

                    switch ($csResp->response->getStatus()) {

                        case MpStatusValues::Approved:


                            // Update invoice
                            $invoice->updateInvoice($dbh, 0, $uS->username);

                            // update fees
                            Fees::updateFeesPaymentStatus($dbh, $csResp->getIdPayment(), $invoice->getIdInvoice(), $csResp->response->getAuthorizeAmount(), $uS->username, FeesPaymentStatus::Returned);
                            $reply .= 'Payment is Returned.  ';
                            $csResp->idVisit = $feeRs->idVisit->getStoredVal();
                            $dataArray['receipt'] = HTMLContainer::generateMarkup('div', nl2br(Receipt::createReturnMarkup($dbh, $csResp, $uS->resourceURL . 'images/receiptlogo.png', $uS->siteName, $uS->sId)));

                            break;

                        case MpStatusValues::Declined:

                            return array('warning' => $csResp->response->getMessage(), 'bid' => $bid);

                            break;

                        default:

                            return array('warning' => '** Void Invalid or Error. **  ', 'bid' => $bid);
                    }
                } catch (Hk_Exception_Payment $exPay) {

                    return array('warning' => "Payment Error = " . $exPay->getMessage(), 'bid' => $bid);
                }
                break;

            case PayType::Cash:

                $cashResp = new CashResponse($payRs->Amount->getStoredVal(), $payRs->idPayor->getStoredVal(), $invoice->getInvoiceNumber());

                $cr = CashTX::cashVoid($dbh, $cashResp, $uS->username, $payRs->idPayment->getStoredVal());

                // Update invoice
                $invoice->updateInvoice($dbh, 0, $uS->username);

                // update fees
                Fees::updateFeesPaymentStatus($dbh, $cr->getIdPayment(), $invoice->getIdInvoice(), $cr->getAmount(), $uS->username, FeesPaymentStatus::Returned);
                $reply .= 'Payment is Returned.  ';

                $cr->idVisit = $feeRs->idVisit->getStoredVal();
                $dataArray['receipt'] = HTMLContainer::generateMarkup('div', nl2br(Receipt::createReturnMarkup($dbh, $cr, $uS->resourceURL . 'images/receiptlogo.png', $uS->siteName, $uS->sId)));

                break;

            case PayType::Check:

                $cashResp = new CheckResponse($payRs->Amount->getStoredVal(), $payRs->idPayor->getStoredVal(), $invoice->getInvoiceNumber());

                $cr = CashTX::checkVoid($dbh, $cashResp, $uS->username, $payRs->idPayment->getStoredVal());

                // Update invoice
                $invoice->updateInvoice($dbh, 0, $uS->username);

                // update fees
                Fees::updateFeesPaymentStatus($dbh, $cr->getIdPayment(), $invoice->getIdInvoice(), $cr->getAmount(), $uS->username, FeesPaymentStatus::Returned);
                $reply .= 'Payment is Returned.  ';

                $cr->idVisit = $feeRs->idVisit->getStoredVal();
                $dataArray['receipt'] = HTMLContainer::generateMarkup('div', nl2br(Receipt::createReturnMarkup($dbh, $cr, $uS->resourceURL . 'images/receiptlogo.png', $uS->siteName, $uS->sId)));
                break;

            default:
                throw new Hk_Exception_Payment('Unknown pay type.  ');
        }

        $dataArray['success'] = $reply;
        return $dataArray;
    }

    public static function voidReturnFees(\PDO $dbh, $idFees, $bid) {

        // Turned off for now....
        throw new Hk_Exception_Payment('Void Returns are not supported.  ');

        $uS = Session::getInstance();
        $dataArray = array('bid' => $bid);
        $reply = '';

        $feeRs = new FeesRS();
        $feeRs->idFees->setStoredVal($idFees);
        $fees = EditRS::select($dbh, $feeRs, array($feeRs->idFees));

        if (count($fees) != 1) {
            throw new Hk_Exception_Payment('Fees record not found.  ');
        }

        // Load data into the record source record.
        EditRS::loadRow($fees[0], $feeRs);


        switch ($feeRs->Pay_Type->getStoredVal()) {

            case PayType::Charge:

                if ($feeRs->idPayment->getStoredVal() > 0) {

                    $payRs = new PaymentRS();
                    $payRs->idPayment->setStoredVal($feeRs->idPayment->getStoredVal());
                    $pments = EditRS::select($dbh, $payRs, array($payRs->idPayment));

                    if (count($pments) != 1) {
                        throw new Hk_Exception_Payment('Payment record not found.  ');
                    }

                    EditRS::loadRow($pments[0], $payRs);

                    // Already voided, or otherwise ineligible
                    if ($payRs->Status_Code->getStoredVal() != PaymentStatusCode::Retrn) {
                        return array('warning' => 'Payment is ineligable for void return.  ', 'bid' => $bid);
                    }

                    // find the token
                    if ($payRs->idToken->getStoredVal() > 0) {
                        $tknRs = CreditToken::getTokenRsFromId($dbh, $payRs->idToken->getStoredVal());
                    } else {
                        return array('warning' => 'Payment Token not found.  ', 'bid' => $bid);
                    }

                    // Get the invoice record
                    $invoice = new Invoice($dbh);
                    $invoice->loadInvoice($dbh, $feeRs->idInvoice->getStoredVal());


                    // Find hte detail record.
                    $pAuthRs = new Payment_AuthRS();
                    $pAuthRs->idPayment->setStoredVal($payRs->idPayment->getStoredVal());
                    $arows = EditRS::select($dbh, $pAuthRs, array($pAuthRs->idPayment));

                    if (count($arows) != 1) {
                        throw new Hk_Exception_Payment('Payment Detail record not found. ');
                    }

                    EditRS::loadRow($arows[0], $pAuthRs);


                    // Set up request
                    $voidRetRequest = new CreditVoidReturnTokenRequest();
                    $voidRetRequest->setAuthCode($pAuthRs->Approval_Code->getStoredVal());
                    $voidRetRequest->setCardHolderName($tknRs->CardHolderName->getStoredVal());
                    $voidRetRequest->setFrequency(MpFrequencyValues::OneTime)->setMemo(MpVersion::PosVersion);
                    $voidRetRequest->setInvoice($invoice->getInvoiceNumber());
                    $voidRetRequest->setPurchaseAmount($pAuthRs->Approved_Amount->getStoredVal());
                    $voidRetRequest->setRefNo($pAuthRs->Reference_Num->getStoredVal());
                    $voidRetRequest->setToken($tknRs->Token->getStoredVal());
                    $voidRetRequest->setTokenId($tknRs->idGuest_token->getStoredVal());

                    try {

                        $csResp = TokenTX::creditVoidReturnToken($dbh, $payRs->idPayor->getstoredVal(), $uS->ccgw, $voidRetRequest, $payRs->idPayment->getStoredVal());

                        switch ($csResp->response->getStatus()) {

                            case MpStatusValues::Approved:


                                // Update invoice
                                $invoice->updateInvoice($dbh, $csResp->response->getAuthorizeAmount(), $uS->username);

                                // update fees
                                Fees::updateFeesPaymentStatus($dbh, $csResp->getIdPayment(), $invoice->getIdInvoice(), $csResp->response->getAuthorizeAmount(), $uS->username, FeesPaymentStatus::Cleared);
                                $reply .= 'Return is voided.  ';
                                //$csResp->idVisit = $feeRs->idVisit->getStoredVal();
                                //$dataArray['receipt'] = HTMLContainer::generateMarkup('div', nl2br(Receipt::createVoidReturnMarkup($dbh, $csResp)));

                                break;

                            case MpStatusValues::Declined:

//                                if ($csResp->response->getMessage() == 'ITEM VOIDED') {
//                                    $feeStatus = FeesPaymentStatus::Void;
//                                    Fees::updateFeesPaymentStatus($dbh, $csResp->getIdPayment(), $invoice->getIdInvoice(), 0, $uS->username, $feeStatus);
//                                }
                                return array('warning' => $csResp->response->getMessage(), 'bid' => $bid);



                            default:

                                return array('warning' => '** Void Return Invalid or Error. **  ', 'bid' => $bid);
                        }
                    } catch (Hk_Exception_Payment $exPay) {

                        return array('warning' => "Void Return Error = " . $exPay->getMessage(), 'bid' => $bid);
                    }
                } else {
                    throw new Hk_Exception_Payment('Payment record not defined.  ');
                }
                break;

            case PayType::Cash:

                throw new Hk_Exception_Payment("Cannot void the return of Cash.  Use a new payment instead.");
                break;

            case PayType::Check:

                throw new Hk_Exception_Payment("Cannot void the return of a Check.  Use a new payment instead.");
                break;

            default:
                throw new Hk_Exception_Payment('Unknown pay type.  ');
        }

        $dataArray['success'] = $reply;
        return $dataArray;
    }

    protected static function AnalyzeCredSaleResult(\PDO $dbh, \PaymentResponse $payResp, \Invoice $invoice) {

        $uS = Session::getInstance();

        // Update invoice
        $invoice->updateInvoice($dbh, $payResp->getAmount(), $uS->username);

        $payResult = new PaymentResult($invoice->getIdInvoice(), $invoice->getIdGroup(), $invoice->getSoldToId());


        switch ($payResp->response->getStatus()) {

            case MpStatusValues::Approved:

                $payResult->feePaymentAccepted($dbh, $uS, $payResp, $invoice);
                $payResult->setDisplayMessage('Fees Paid by Credit Card.  ');

                $avsResult = new AVSResult($payResp->response->getAVSResult());
                $cvvResult = new CVVResult($payResp->response->getCvvResult());

                if ($avsResult->isZipMatch() === FALSE) {
                    $payResult->setDisplayMessage($avsResult->getResultMessage() . '.  ');
                }

                if ($cvvResult->isCvvMatch() === FALSE) {
                    $payResult->setDisplayMessage($cvvResult->getResultMessage() . '.  ');
                }

                break;

            case MpStatusValues::Declined:

                $payResult->setStatus(PaymentResult::DENIED);
                $payResult->feePaymentRejected($dbh, $uS, $payResp);
                $payResult->setDisplayMessage('** The Payment is Declined. **  Message: ' . $payResp->response->getDisplayMessage());

                break;

            default:

                $payResult->setStatus(PaymentResult::ERROR);
                $payResult->feePaymentError($dbh, $uS);
                $payResult->setDisplayMessage('** Payment Invalid or Error **  Message: ' . $payResp->response->getDisplayMessage());
        }

        return $payResult;
    }

    public static function getInfoFromCardId(PDO $dbh, $cardId) {

        $infoArray = array();

        $query = "select idName, idGroup, InvoiceNumber from card_id where CardID = :cid";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array(':cid'=>$cardId));

        $rows = $stmt->fetchAll(PDO::FETCH_NUM);

        if (count($rows) > 0) {

            $infoArray['idName'] = $rows[0][0];
            $infoArray['idGroup'] = $rows[0][1];
            $infoArray['InvoiceNumber'] = $rows[0][2];

            // Delete to discourge replays.
            $stmt = $dbh->prepare("delete from card_id where CardID = :cid");
            $stmt->execute(array(':cid'=>$cardId));

        } else {
            throw new Hk_Exception_Payment('CardID/PaymentID not found.  ');
        }

        return $infoArray;
    }

    public static function processSiteReturn(\PDO $dbh, $gw, $post) {

        $rtnCode = '';
        $rtnMessage = '';
        $payResult = null;

        if (isset($post['ReturnCode'])) {
            $rtnCode = intval(filter_var($post['ReturnCode'], FILTER_SANITIZE_NUMBER_INT), 10);
        }

        if (isset($post['ReturnMessage'])) {
            $rtnMessage = filter_var($post['ReturnMessage'], FILTER_SANITIZE_STRING) . "  ";
        }

        if (isset($post['CardID'])) {

            $cardId = filter_var($post['CardID'], FILTER_SANITIZE_STRING);

            // Save postback in the db.
            try {
                Gateway::saveGwTx($dbh, $rtnCode, '', json_encode($post), 'CardInfoPostBack');
            } catch (Exception $ex) {
                // Do nothing
            }

            if ($rtnCode != 0) {

                 $payResult = new cofResult($rtnMessage, PaymentResult::ERROR, 0, 0);

            } else {

                try {

                    $vr = CardInfo::portalReply($dbh, $gw, $cardId, $post);

                    $payResult = new CofResult($vr->response->getDisplayMessage(), $vr->response->getStatus(), $vr->idPayor, $vr->idRegistration);

                } catch (Hk_Exception_Payment $hex) {
                    $payResult = new cofResult($hex->getMessage(), PaymentResult::ERROR, 0, 0);
                }

            }

        } else if (isset($post['PaymentID'])) {

            $paymentId = filter_var($post['PaymentID'], FILTER_SANITIZE_STRING);

            try {
                Gateway::saveGwTx($dbh, $rtnCode, '', json_encode($post), 'HostedCoPostBack');
            } catch (Exception $ex) {
                // Do nothing
            }

            try {

                $csResp = HostedCheckout::portalReply($dbh, $gw, $paymentId, $post);

            } catch (Hk_Exception_Payment $hex) {

                $payResult = new PaymentResult('', 0, 0);
                $payResult->setStatus(PaymentResult::ERROR);
                $payResult->setDisplayMessage($hex->getMessage());

                //$payResult = new cofResult($hex->getMessage(), PaymentResult::ERROR, 0, 0);
                return $payResult;
            }


            if ($csResp->invoiceNumber != '') {

                $invoice = new Invoice($dbh, $csResp->invoiceNumber);

                // Analyze the result
                $payResult = self::AnalyzeCredSaleResult($dbh, $csResp, $invoice);

            } else {

                $payResult = new PaymentResult('', 0, 0);
                $payResult->setStatus(PaymentResult::ERROR);
                $payResult->setDisplayMessage('Invoice Not Found!  ');
            }

        }

        return $payResult;
    }

}


