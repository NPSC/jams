<?php
/**
 * CreditToken.php
 *
 *
 * @category  Payment
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

/**
 * Description of CreditToken
 *
 * @author Eric
 */
class CreditToken {

    public static function storeToken(PDO $dbh, $idRegistration, $idPayor, MercResponse $vr) {

        $gtRs = self::findTokenRS($dbh, $idPayor, $vr->getCardHolderName(), $vr->getCardType(), $vr->getMaskedAccount());

        // Load values
        $gtRs->idGuest->setNewVal($idPayor);
        $gtRs->idRegistration->setNewVal($idRegistration);

        $gtRs->CardHolderName->setNewVal($vr->getCardHolderName());
        $gtRs->CardType->setNewVal($vr->getCardType());
        $gtRs->CardUsage->setNewVal($vr->getCardUsage());
        $gtRs->ExpDate->setNewVal($vr->getExpDate());
        $gtRs->Frequency->setNewVal('OneTime');
        $gtRs->Granted_Date->setNewVal(date('Y-m-d H:i:s'));
        $gtRs->LifetimeDays->setNewVal(MpTokenLifetimeDays::OneTime);
        $gtRs->MaskedAccount->setNewVal($vr->getMaskedAccount());
        $gtRs->OperatorID->setNewVal($vr->getOperatorID());
        $gtRs->Response_Code->setNewVal($vr->getResponseCode());
        $gtRs->Status->setNewVal($vr->getStatus());
        $gtRs->StatusMessage->setNewVal($vr->getStatusMessage());
        $gtRs->Tran_Type->setNewVal($vr->getTranType());
        $gtRs->Token->setNewVal($vr->getToken());

        // Write
        if ($gtRs->idGuest_token->getStoredVal() > 0) {
            // Update
            $num = EditRS::update($dbh, $gtRs, array($gtRs->idGuest_token));
            $idToken = $gtRs->idGuest_token->getStoredVal();
        } else {
            //Insert
            $idToken = EditRS::insert($dbh, $gtRs);
        }

        return $idToken;
    }


    public static function updateToken(PDO $dbh, PaymentResponse $vr) {

        $gtRs = new Guest_TokenRS();
        $gtRs->idGuest_token->setStoredVal($vr->idToken);
        $rows = EditRS::select($dbh, $gtRs, array($gtRs->idGuest_token));

        if (count($rows) == 1) {

            EditRS::loadRow($rows[0], $gtRs);

            // Load new values
            $gtRs->Token->setNewVal($vr->response->getToken());
            $gtRs->Response_Code->setNewVal($vr->response->getResponseCode());
            $gtRs->Status->setNewVal($vr->response->getStatus());
            $gtRs->StatusMessage->setNewVal($vr->response->getMessage());

            EditRS::update($dbh, $gtRs, array($gtRs->idGuest_token));

        }
        return $gtRs;
    }


    public static function getRegTokenRSs(PDO $dbh, $idRegistration, $idGuest = 0) {

        $rsRows = array();

        // Get registration tokens
        if ($idRegistration > 0) {
            $gtRs = new Guest_TokenRS();
            $gtRs->idRegistration->setStoredVal($idRegistration);
            $rows = EditRS::select($dbh, $gtRs, array($gtRs->idRegistration));


            foreach ($rows as $r) {
                $gtRs = new Guest_TokenRS();
                EditRS::loadRow($r, $gtRs);
                    $rsRows[$gtRs->idGuest_token->getStoredVal()] = $gtRs;
                }
            }

        if ($idGuest > 0) {
            $gtRs = new Guest_TokenRS();
            $gtRs->idGuest->setStoredVal($idGuest);
            $rows = EditRS::select($dbh, $gtRs, array($gtRs->idGuest));

            foreach ($rows as $r) {
                $gtRs = new Guest_TokenRS();
                EditRS::loadRow($r, $gtRs);

                if (isset($rsRows[$gtRs->idGuest_token->getStoredVal()]) === FALSE) {
                    $rsRows[] = $gtRs;
                }
            }
        }
        return $rsRows;

    }

    public static function findTokenRS(PDO $dbh, $gid, $cardHolderName, $cardType, $maskedAccount) {

        $gtRs = new Guest_TokenRS();
        $gtRs->idGuest->setStoredVal($gid);
        $gtRs->CardHolderName->setStoredVal($cardHolderName);
        $gtRs->CardType->setStoredVal($cardType);
        $gtRs->MaskedAccount->setStoredVal($maskedAccount);
        $rows = EditRS::select($dbh, $gtRs, array($gtRs->idGuest, $gtRs->CardHolderName, $gtRs->CardType, $gtRs->MaskedAccount));

        if (count($rows) == 1) {

            EditRS::loadRow($rows[0], $gtRs);

        } else if (count($rows) == 0) {

            $gtRs = New Guest_TokenRS();

        } else {

            throw new Hk_Exception_Runtime('Multiple Payment Tokens for guest Id: '.$gid);
        }

        return $gtRs;
    }

    public static function getTokenRsFromId(PDO $dbh, $idToken) {

        $gtRs = new Guest_TokenRS();

        if ($idToken > 0) {

            $gtRs->idGuest_token->setStoredVal($idToken);
            $rows = EditRS::select($dbh, $gtRs, array($gtRs->idGuest_token));

            if (count($rows) > 0) {
                EditRS::loadRow($rows[0], $gtRs);
            } else {
                $gtRs = New Guest_TokenRS();
            }
        }

        return $gtRs;
    }

    public static function hasToken(Guest_TokenRS $tokenRs) {

        if ($tokenRs->idGuest_token->getStoredVal() > 0 && $tokenRs->Token->getStoredVal() != '') {

            $now = new DateTime();

            // Card expired?
            $expDate = $tokenRs->ExpDate->getStoredVal();

            if (strlen($expDate) == 4) {

                $expMonth = $expDate[0] . $expDate[1];
                $expYear = $expDate[2] . $expDate[3];
                $expDT = new DateTime($expYear . '-' . $expMonth . '-01');

                if ($now > $expDT) {
                    return FALSE;
                }
            }

            // Token Expired?
            $grantedDT = new DateTime($tokenRs->Granted_Date->getStoredVal());
            $p1d = new DateInterval('P' . $tokenRs->LifetimeDays->getStoredVal() . 'D');
            $grantedDT->add($p1d);

            if ($grantedDT > $now) {
                return TRUE;
            }
        }

        return FALSE;
    }


}
