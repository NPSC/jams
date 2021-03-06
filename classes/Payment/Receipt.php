<?php
/**
 * Receipt.php
 *
 *
 * @category  house
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link
 */

Define('NEWLINE', "\n");


/**
 * Description of Receipt
 *
 * @author Eric
 */
class Receipt {

    public $guestObj;
    public $guestName;
    public $guestEmail;
    public $emailFlag;


    public static function createSaleMarkup(\PDO $dbh, $siteName, $siteId, $logoUrl, \PaymentResponse $payResp, $hospitalName = '') {

        // Assemble the statement
        $rec = self::getHouseIconMarkup($logoUrl, $siteName);

        $rec .= HTMLContainer::generateMarkup('div', self::getAddressTable($dbh, $siteId), array('style'=>'float:left;margin-bottom:10px;margin-left:20px;'));

        $tbl = new HTMLTable();
        $tbl->addBodyTr(HTMLTable::makeTh($siteName . " Receipt", array('colspan'=>'2')));

        $guest = Member::GetDesignatedMember($dbh, $payResp->idPayor, MemBasis::Indivual);

        if ($guest->getMemberName() != '') {
            $tbl->addBodyTr(HTMLTable::makeTd("Guest: ", array('class'=>'tdlabel')) . HTMLTable::makeTd($guest->getMemberName()));
        }

        if ($hospitalName != '') {
            $tbl->addBodyTr(HTMLTable::makeTd("Hospital: ", array('class'=>'tdlabel')) . HTMLTable::makeTd($hospitalName));
        }

        $tbl->addBodyTr(HTMLTable::makeTd("Date: ", array('class'=>'tdlabel')) . HTMLTable::makeTd(date('D M jS, Y g:ia')));
        $tbl->addBodyTr(HTMLTable::makeTd("Item: ", array('class'=>'tdlabel')) . HTMLTable::makeTd("Room Fees" ));
        $tbl->addBodyTr(HTMLTable::makeTd("Total:", array('class'=>'tdlabel')) . HTMLTable::makeTd(number_format($payResp->getAmount(), 2)));

        if ($payResp->paymentType == PayType::Charge) {

            $tbl->addBodyTr(HTMLTable::makeTd("Credit Card:", array('class'=>'tdlabel')) . HTMLTable::makeTd(number_format($payResp->getAmount(), 2)));
            $tbl->addBodyTr(HTMLTable::makeTd($payResp->response->getCardType() . ':', array('class'=>'tdlabel')) . HTMLTable::makeTd("xxxxxxxxxxx". $payResp->cardNum));

        } else if ($payResp->paymentType == PayType::Cash) {

            $tbl->addBodyTr(HTMLTable::makeTd("Cash Tendered:", array('class'=>'tdlabel')) . HTMLTable::makeTd(number_format($payResp->getAmount(), 2)));

        } else if ($payResp->paymentType == PayType::Check) {

            $tbl->addBodyTr(HTMLTable::makeTd("Check:", array('class'=>'tdlabel')) . HTMLTable::makeTd(number_format($payResp->getAmount(), 2)));

        }
        $tbl->addBodyTr(HTMLTable::makeTd("Invoice:", array('class'=>'tdlabel')) . HTMLTable::makeTd($payResp->getInvoice()));

        $rec .= HTMLContainer::generateMarkup('div', $tbl->generateMarkup(), array('style'=>'margin-bottom:10px;clear:both;'));


        return $rec;
    }

    public static function createVoidMarkup(\PDO $dbh, \PaymentResponse $verifyResp, $logoUrl, $siteName, $siteId) {

        $rec = self::getHouseIconMarkup($logoUrl, $siteName);

        $rec .= HTMLContainer::generateMarkup('div', self::getAddressTable($dbh, $siteId), array('style'=>'float:left;margin-bottom:10px;margin-left:20px;'));

        $tbl = new HTMLTable();
        $tbl->addBodyTr(HTMLTable::makeTh($siteName . " Void Sale Receipt", array('colspan'=>'2')));

        $tbl->addBodyTr(HTMLTable::makeTd("Date: ", array('class'=>'tdlabel'))
                . HTMLTable::makeTd(date('D M jS, Y g:ia')));

        $tbl->addBodyTr(HTMLTable::makeTd("Total Voided:", array('class'=>'tdlabel')) . HTMLTable::makeTd(number_format($verifyResp->getAmount(), 2)));

        if ($verifyResp->paymentType == PayType::Charge) {

            $tbl->addBodyTr(HTMLTable::makeTd($verifyResp->response->getCardType() . ':', array('class'=>'tdlabel')) . HTMLTable::makeTd("xxxxxxxxxxx". $verifyResp->cardNum));
        }

        $tbl->addBodyTr(HTMLTable::makeTd("Invoice:", array('class'=>'tdlabel')) . HTMLTable::makeTd($verifyResp->getInvoice()));

        $rec .= HTMLContainer::generateMarkup('div', $tbl->generateMarkup(), array('style'=>'margin-bottom:10px;clear:both;'));

//        if ($verifyResp->idVisit > 0) {
//            $rec .= VisitView::createCurrentFees($dbh, $verifyResp->idVisit);
//        }

        return $rec;
    }

    public static function createReturnMarkup(\PDO $dbh, \PaymentResponse $verifyResp, $logoUrl, $siteName, $siteId) {

        $rec = self::getHouseIconMarkup($logoUrl, $siteName);

        $rec .= HTMLContainer::generateMarkup('div', self::getAddressTable($dbh, $siteId), array('style'=>'float:left;margin-bottom:10px;margin-left:20px;'));

        $tbl = new HTMLTable();
        $tbl->addBodyTr(HTMLTable::makeTh($siteName . " Return Receipt", array('colspan'=>'2')));

        $tbl->addBodyTr(HTMLTable::makeTd("Date: ", array('class'=>'tdlabel'))
                . HTMLTable::makeTd(date('D M jS, Y g:ia')));

        $tbl->addBodyTr(HTMLTable::makeTd("Total Returned:", array('class'=>'tdlabel')) . HTMLTable::makeTd(number_format($verifyResp->getAmount(), 2)));

        if ($verifyResp->paymentType == PayType::Charge) {
            $tbl->addBodyTr(HTMLTable::makeTd($verifyResp->response->getCardType() . ':', array('class'=>'tdlabel')) . HTMLTable::makeTd("xxxxxxxxxxx". $verifyResp->cardNum));
        }

        $tbl->addBodyTr(HTMLTable::makeTd("Invoice:", array('class'=>'tdlabel')) . HTMLTable::makeTd($verifyResp->getInvoice()));

        $rec .= HTMLContainer::generateMarkup('div', $tbl->generateMarkup(), array('style'=>'margin-bottom:10px;clear:both;'));

//        if ($verifyResp->idVisit > 0) {
//            $rec .= VisitView::createCurrentFees($dbh, $verifyResp->idVisit);
//        }

        return $rec;
    }


    public static function getHouseIconMarkup($logoUrl, $siteName) {

        return HTMLContainer::generateMarkup('div',
                HTMLContainer::generateMarkup('img', '', array('src'=>$logoUrl, 'id'=>'hhkrcpt', 'alt'=>$siteName, 'width'=>'120')),
                array('style'=>'margin-bottom:10px;float:left;'));

    }

    protected static function getAddressTable(PDO $dbh, $idName) {

        $mkup = '';

        if ($idName > 0) {

            $stmt = $dbh->query("select n.Company, a.Address_1, a.Address_2, a.City, a.State_Province, a.Postal_Code, p.Phone_Num, n.Web_Site
    from name n left join name_address a on n.idName = a.idName and n.Preferred_Mail_Address = a.Purpose left join name_phone p on n.idName = p.idName and n.Preferred_Phone = p.Phone_Code where n.idName = $idName");

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($rows) == 1) {

                $street = $rows[0]['Address_1'];

                if ($rows[0]['Address_2'] != '') {
                    $street .= ', ' . $rows[0]['Address_2'];
                }
                $adrTbl = new HTMLTable();

                $adrTbl->addBodyTr(HTMLTable::makeTd($rows[0]['Company']));
                $adrTbl->addBodyTr(HTMLTable::makeTd($street));
                $adrTbl->addBodyTr(HTMLTable::makeTd($rows[0]['City'] . ', ' . $rows[0]['State_Province'] . ' ' . $rows[0]['Postal_Code']));
                if ($rows[0]['Phone_Num'] != '') {
                    $adrTbl->addBodyTr(HTMLTable::makeTd('Phone: ' . $rows[0]['Phone_Num']));
                }
                $adrTbl->addBodyTr(HTMLTable::makeTd($rows[0]['Web_Site']));

                $mkup = $adrTbl->generateMarkup();
            }
        }

        return $mkup;
    }

    protected static function processRatesRooms(array $spans, array &$rooms, array &$rates) {

        $idResc = 0;
        $rateCat = '';
        $pledgedAmt = 0;
        $rateCounter = 0;
        $roomCounter = 0;
        $idVisit = 0;

        foreach ($spans as $v) {

            if ($idVisit != $v['idVisit']) {
                $idResc = 0;
                $rateCat = '';
                $pledgedAmt = 0;

            }

            // Set expected departure to now if earlier than "today"
            $expDepDT = new DateTime($v['Expected_Departure']);
            $now = new DateTime();

            if ($expDepDT < $now) {
                $expDepStr = $now->format('Y-m-d');
            } else {
                $expDepStr = $expDepDT->format('Y-m-d');
            }



            // rooms
            if ($idResc != $v['idResource']) {

                $idResc = $v['idResource'];
                $roomCounter++;
                $rooms[$roomCounter] = array('vid'=>$v['idVisit'], 'span'=>$v['Span'], 'title'=>$v['Title'], 'start'=>$v['Span_Start'], 'end'=>$v['Span_End'], 'expEnd'=>$expDepStr);

            } else {
                $rooms[$roomCounter]['end'] = $v['Span_End'];
                $rooms[$roomCounter]['expEnd'] = $expDepStr;
            }


            // rates
            if ($rateCat != $v['Rate_Category'] || ($rateCat == RoomRateCategorys::Fixed_Rate_Category && $pledgedAmt != $v['Pledged_Rate'])) {

                $rateCat = $v['Rate_Category'];
                $pledgedAmt = $v['Pledged_Rate'];
                $rateCounter++;
                $rates[$rateCounter] = array('vid'=>$v['idVisit'], 'span'=>$v['Span'], 'title'=>$v['Title'], 'cat'=>$rateCat, 'amt'=>$v['Pledged_Rate'], 'adj'=>$v['Expected_Rate'], 'start'=>$v['Span_Start'], 'end'=>$v['Span_End'], 'expEnd'=>$expDepStr);

            } else {

                $rates[$rateCounter]['end'] = $v['Span_End'];
                $rates[$rateCounter]['expEnd'] = $expDepStr;

            }
        }

    }

    protected static function makeOrdersRatesTable(\PDO $dbh, $rates, &$totalAmt) {

        $numberNites = 0;
        $tbl = new HTMLTable();
        $tbl->addHeaderTr(HTMLTable::makeTh('Visit Id').HTMLTable::makeTh('Room').HTMLTable::makeTh('Start').HTMLTable::makeTh('End').HTMLTable::makeTh('Nights').HTMLTable::makeTh('Rate').HTMLTable::makeTh('Charge'));

        // orders and rates
        foreach ($rates as $r) {

            $startDT = new DateTime($r['start']);
            $startDT->setTime(0,0,0);
            $endDT = ($r['end'] == '' ? new DateTime($r['expEnd']) : new DateTime($r['end']));
            $endDT->setTime(0,0,0);
            $days = $startDT->diff($endDT, TRUE)->days;

            $numberNites += $days;

            $tiers = FinAssistance::tiersCalculation($dbh, $days, $r['cat'], $r['amt'], $r['adj']);


            foreach ($tiers as $t) {

                $totalAmt += $t['amt'];

                $tbl->addBodyTr(
                     HTMLTable::makeTd($r['vid'] . '-' . $r['span'])
                    .HTMLTable::makeTd($r['title'])
                    .HTMLTable::makeTd($startDT->format('n/d/Y'))
                    .HTMLTable::makeTd($startDT->add(new DateInterval('P' . $t['days'] . 'D'))->format('n/d/Y'))
                    .HTMLTable::makeTd($t['days'], array('style'=>'text-align:center;'))
                    .HTMLTable::makeTd(number_format($t['rate'], 2), array('style'=>'text-align:right;'))
                    .HTMLTable::makeTd('$' . number_format($t['amt'], 2), array('style'=>'text-align:right;'))
                        );
            }

        }


        // Orders totals
        $tbl->addBodyTr(HTMLTable::makeTd('Totals', array('colspan'=>'4', 'class'=>'tdlabel hhk-tdTotals'))
            .HTMLTable::makeTd($numberNites, array('class'=>'hhk-tdTotals', 'style'=>'text-align:center;'))
            .HTMLTable::makeTd('', array('class'=>'hhk-tdTotals'))
            .HTMLTable::makeTd('$'. number_format($totalAmt, 2), array('class'=>'hhk-tdTotals', 'style'=>'text-align:right;')));

        return $tbl;
    }

    protected static function makePaymentsTable($pments, $payTypes, $feesItemTypes, $totalAmt) {

        $ptbl = new HTMLTable();
        $totalPment = 0.00;

        foreach ($pments as $p) {

            $totalPment += $p['Authorized_Amount'];

            // look for key deposit refunds, set by Visit->returnKeyDeposit()
            if ($feesItemTypes[$p['Type']][0] === FeesType::RoomFee && $p['Fund_Code'] == 'keyRfnd') {
                $type = 'Key Deposit Refund';
            } else {
                $type = $payTypes[$p['Pay_Type']][1];
            }

            $ptbl->addBodyTr(
                     HTMLTable::makeTd($p['idVisit'] . '-' . $p['Visit_Span'])
                    .HTMLTable::makeTd(date('n/d/Y', strtotime($p['Date_Entered'])))
                    .HTMLTable::makeTd($feesItemTypes[$p['Type']][1])
                    .HTMLTable::makeTd($p['Invoice_Number'])
                    .HTMLTable::makeTd($type)
                    .HTMLTable::makeTd($p['CardType'] . ' ' . $p['MaskedAccount'])
                    .HTMLTable::makeTd('$'. number_format($p['Authorized_Amount'], 2)), array('style'=>'text-align:right;'));

        }

        if ($totalPment > 0) {
            $ptbl->addHeaderTr(HTMLTable::makeTh('Visit Id').HTMLTable::makeTh('Date').HTMLTable::makeTh('Item').HTMLTable::makeTh('Invoice').HTMLTable::makeTh('Type').HTMLTable::makeTh('').HTMLTable::makeTh('Payment'));
            $ptbl->addBodyTr(HTMLTable::makeTd('Payment Total (Thank You!)', array('colspan'=>'6', 'class'=>'tdlabel hhk-tdTotals'))
                .HTMLTable::makeTd('$'. number_format($totalPment, 2), array('class'=>'hhk-tdTotals', 'style'=>'text-align:right;')));
        } else if (count($pments) == 0) {
            $ptbl->addBodyTr(HTMLTable::makeTd('No Payments Recorded', array('colspan'=>'7', 'style'=>'font-style:italic;')));
        }

        $bal = $totalAmt - $totalPment;

        // Set up balance prompt ..
        if ($bal > 0) {
            $finalWord = 'Current Balance Due';
        } else if ($bal == 0) {
            $finalWord = 'Current Balance';
        } else {
            $finalWord = 'Guest Credit';
            $bal = abs($bal);
        }

        if ($totalAmt > 0) {
            $ptbl->addBodyTr(HTMLTable::makeTd('Total Charges', array('colspan'=>'6', 'class'=>'tdlabel'))
                .HTMLTable::makeTd('$'. number_format($totalAmt, 2), array('style'=>'text-align:right;')));
        }

        $ptbl->addBodyTr(HTMLTable::makeTd($finalWord, array('colspan'=>'6', 'class'=>'tdlabel hhk-tdTotals'))
                .HTMLTable::makeTd('$'. number_format($bal, 2), array('class'=>'hhk-tdTotals', 'style'=>'text-align:right;')));

        return $ptbl;
    }

    public static function createComprehensiveStatements(\PDO $dbh, $spans, $idRegistration, $logoUrl, $guestName) {

        $uS = Session::getInstance();

        if (count($spans) == 0) {
            return 'Visits Not Found.  ';
        }

        $idPsg = $spans[0]['idPsg'];

        // Collect rates and rooms
        $rooms = array();
        $rates = array();

        self::processRatesRooms($spans, $rooms, $rates);

        $totalAmt = 0.00;

        // Visits and Rates
        $tbl = self::makeOrdersRatesTable($dbh, $rates, $totalAmt);

        // Payments
        $query = "select * from vlist_payments where idRegistration = :idreg and `Status` = :stat and `Type` = 'r' order by `idVisit`, `Visit_Span`, `Date_Entered`";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array(':idreg'=>$idRegistration, ':stat'=>  FeesPaymentStatus::Cleared));
        $pments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ptbl = self::makePaymentsTable($pments, $uS->nameLookups[GL_TableNames::PayType], $uS->guestLookups['Fees_Item_Type'], $totalAmt);

        // Key deposits
        $keyMkup = '';
        if ($uS->KeyDeposit) {

            $query = "select * from vlist_payments where idRegistration = :idreg and `Status` = :stat and `Type` = 'k' order by `idVisit`, `Visit_Span`, `Date_Entered`";
            $stmt = $dbh->prepare($query);
            $stmt->execute(array(':idreg'=>$idRegistration, ':stat'=>  FeesPaymentStatus::Cleared));
            $pments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $keyTbl = self::makePaymentsTable($pments, $uS->nameLookups[GL_TableNames::PayType], $uS->guestLookups['Fees_Item_Type'], 0);

            $keyMkup = HTMLContainer::generateMarkup('h4', 'Key Deposits', array('style'=>'margin-top:15px;'))
                    . HTMLContainer::generateMarkup('div', $keyTbl->generateMarkup(), array('style'=>'margin-bottom:10px;', 'class'=>'hhk-tdbox'));
        }


        // Find patient name
        $patientName = '';
        $pstmt = $dbh->query("select n.Name_First, n.Name_Last from name n left join hospital_stay hs on n.idName = hs.idPatient where hs.idPsg = $idPsg");
        $rows = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) > 0) {
            $patientName = $rows[0]['Name_First'] . ' ' . $rows[0]['Name_Last'];
        }

//        $attr = array(
//            'style' => "background-image: url($logoUrl); height: 162px; width: 300px; background-repeat: no-repeat;margin-bottom:10px;float:left;",);

        // Assemble the statement
        $rec = HTMLContainer::generateMarkup('div',
                HTMLContainer::generateMarkup('img', '', array('src'=>$logoUrl, 'id'=>'hhkrcpt', 'alt'=>'House Icon', 'width'=>'120')),
                array('style'=>'margin-bottom:10px;float:left;'));

        $rec .= HTMLContainer::generateMarkup('div', self::getAddressTable($dbh, $uS->sId), array('style'=>'float:left;margin-bottom:10px;margin-left:20px;'));

        $rec .= HTMLContainer::generateMarkup('h2', 'Comprehensive Statement of Account', array('style'=>'clear:both;'));
        $rec .= HTMLContainer::generateMarkup('h4', 'Prepared '.date('M jS, Y'), array('style'=>'margin-bottom:10px;'));
        $rec .= HTMLContainer::generateMarkup('h4', 'Guest: ' . $guestName, array('style'=>'margin-bottom:10px;'));
        $rec .= HTMLContainer::generateMarkup('h4', 'Patient: ' . $patientName);
        $rec .= HTMLContainer::generateMarkup('p', 'Includes all visits for this Patient Support Group (' . $idPsg .')', array('style'=>'margin-bottom:10px;'));
        if ($spans[0]['Association'] != '') {
            $hospTitle = 'Hospital/Association: ' . $spans[0]['Hospital'] . '/' . $spans[0]['Association'];
        } else {
            $hospTitle = 'Hospital: ' . $spans[0]['Hospital'];
        }
        $rec .= HTMLContainer::generateMarkup('h4', $hospTitle);

        $rec .= HTMLContainer::generateMarkup('h4', 'Visit Dates & Room Charges', array('style'=>'margin-top:25px;'));
        $rec .= HTMLContainer::generateMarkup('div', $tbl->generateMarkup(), array('class'=>'hhk-tdbox'));

        $rec .= HTMLContainer::generateMarkup('h4', 'Payments', array('style'=>'margin-top:15px;'));
        $rec .= HTMLContainer::generateMarkup('div', $ptbl->generateMarkup(), array('style'=>'margin-bottom:10px;', 'class'=>'hhk-tdbox'));

        $rec .= $keyMkup;

        return $rec;

    }

    public static function createStatementMarkup(\PDO $dbh, $idVisit, $logoUrl, $guestName) {

        $uS = Session::getInstance();


        if ($idVisit > 0) {

            $stmt = $dbh->prepare("select * from vvisit_stmt where idVisit = :idvisit order by `Span`");
            $stmt->execute(array(':idvisit'=>$idVisit));
            $spans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else {
            return 'Missing Input pararmeters.  ';
        }


        if (count($spans) == 0) {
            return 'Visit Not Found.  ';
        }

        $idPsg = $spans[0]['idPsg'];


        // Collect rates and rooms
        $rooms = array();
        $rates = array();

        self::processRatesRooms($spans, $rooms, $rates);

        $totalAmt = 0.00;

        $tbl = self::makeOrdersRatesTable($dbh, $rates, $totalAmt);

        // Rooms
        if (count($rooms) > 1) {
            $roomstbl = new HTMLTable();
            $roomstbl->addHeaderTr(HTMLTable::makeTh('Room'). HTMLTable::makeTh('Start') . HTMLTable::makeTh('End'));

            foreach ($rooms as $r) {
                $roomstbl->addBodyTr(
                        HTMLTable::makeTd($r['title'])
                        . HTMLTable::makeTd(date('n/d/Y', strtotime($r['start'])))
                        . HTMLTable::makeTd($r['end'] == '' ? date('n/d/Y', strtotime($r['expEnd'])) : date('n/d/Y', strtotime($r['end']))));
            }

            $roomsMarkup = $roomstbl->generateMarkup();

        } else {
            $roomsMarkup = HTMLContainer::generateMarkup('p', $rooms[1]['title']);
        }


        // Payments
        $query = "select * from vlist_payments where idVisit = :idvisit and `Status` = :stat and `Type` = 'r' order by `Visit_Span`, `Date_Entered`";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array(':idvisit'=>$idVisit, ':stat'=>  FeesPaymentStatus::Cleared));
        $pments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ptbl = self::makePaymentsTable($pments, $uS->nameLookups[GL_TableNames::PayType], $uS->guestLookups['Fees_Item_Type'], $totalAmt);

        // Find patient name
        $patientName = '';
        $pstmt = $dbh->query("select n.Name_First, n.Name_Last from name n left join hospital_stay hs on n.idName = hs.idPatient where hs.idPsg = $idPsg");
        $rows = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) > 0) {
            $patientName = $rows[0]['Name_First'] . ' ' . $rows[0]['Name_Last'];
        }

        $rec = HTMLContainer::generateMarkup('div',
                HTMLContainer::generateMarkup('img', '', array('src'=>$logoUrl, 'id'=>'hhkrcpt', 'alt'=>'House Icon', 'width'=>'120')),
                array('style'=>'margin-bottom:10px;float:left;'));

        $rec .= HTMLContainer::generateMarkup('div', self::getAddressTable($dbh, $uS->sId), array('style'=>'float:left;margin-bottom:10px;margin-left:20px;'));

        $rec .= HTMLContainer::generateMarkup('h2', 'Statement of Account', array('style'=>'clear:both;'));
        $rec .= HTMLContainer::generateMarkup('h4', 'Prepared '.date('M jS, Y'), array('style'=>'margin-bottom:10px;'));
        $rec .= HTMLContainer::generateMarkup('h4', 'Guest: ' . $guestName, array('style'=>'margin-bottom:10px;'));
        $rec .= HTMLContainer::generateMarkup('h4', 'Patient: ' . $patientName);
        $rec .= HTMLContainer::generateMarkup('p', 'Patient Support Group Id: ' . $idPsg, array('style'=>'margin-bottom:10px;'));

        if ($spans[0]['Association'] != '') {
            $hospTitle = 'Hospital/Association: ' . $spans[0]['Hospital'] . '/' . $spans[0]['Association'];
        } else {
            $hospTitle = 'Hospital: ' . $spans[0]['Hospital'];
        }
        $rec .= HTMLContainer::generateMarkup('h4', $hospTitle);

        $rec .= HTMLContainer::generateMarkup('h4', 'Room Assignment', array('style'=>'margin-top:15px;'));
        $rec .= HTMLContainer::generateMarkup('div', $roomsMarkup, array('class'=>'hhk-tdbox'));

        $rec .= HTMLContainer::generateMarkup('h4', 'Visit Dates & Room Charges', array('style'=>'margin-top:25px;'));
        $rec .= HTMLContainer::generateMarkup('div', $tbl->generateMarkup(), array('class'=>'hhk-tdbox'));

        $rec .= HTMLContainer::generateMarkup('h4', 'Payments', array('style'=>'margin-top:15px;'));
        $rec .= HTMLContainer::generateMarkup('div', $ptbl->generateMarkup(), array('style'=>'margin-bottom:10px;', 'class'=>'hhk-tdbox'));

        return $rec;


    }

    public function getGuestInfo(PDO $dbh, $idGuest) {

        $guest = new Guest($dbh, '', $idGuest);

        $name = $guest->getNameObj();

        //$addr = $guest->getAddrObj()->get_data($guest->getAddrObj()->get_preferredCode());
        $email = $guest->getEmailsObj()->get_data($guest->getEmailsObj()->get_preferredCode());
        $this->guestEmail = $email["Email"];
        $this->guestName = $name->get_fullName();


    }
}
