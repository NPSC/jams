<?php
/**
 * IndivMember.php
 *
 *
 *
 * @category  member
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

/**
 * IndivMember
 * @author Eric
 */
class IndivMember extends Member {


    protected function getDefaultMemBasis() {
        return MemBasis::Indivual;
    }

    /**
     *
     * @return MemDesignation
     */
    public function getMemberDesignation(){
        return MemDesignation::Individual;
    }

    public function getMemberName() {
        return ($this->get_nickName() != '' ? $this->get_nickName() : $this->get_firstName()) . " " . $this->get_lastName();
    }

    public function getMemberFrmlName() {
        return $this->get_firstName() . " " . $this->get_lastName();
    }

    /**
     *
     * @param PDO $dbh
     * @param string $statusClass HTML Class attribute for status control
     * @param string $basisClass HTML class attribute for basis control
     * @param string $idPrefix
     * @return string HTML table markup
     */
    public function createMarkupTable() {

        $uS = Session::getInstance();
        $idPrefix = $this->getIdPrefix();

        $table = new HTMLTable();
        $table->addHeaderTr(
                HTMLContainer::generateMarkup('th', 'Id')
                . HTMLContainer::generateMarkup('th', 'Prefix')
                . HTMLContainer::generateMarkup('th', 'First Name')
                . HTMLContainer::generateMarkup('th', 'Middle')
                . HTMLContainer::generateMarkup('th', 'Last Name')
                . HTMLContainer::generateMarkup('th', 'Suffix')
                . HTMLContainer::generateMarkup('th', 'Nickname')
                . HTMLContainer::generateMarkup('th', 'Status')
                . HTMLContainer::generateMarkup('th', 'Basis')
                );

        // Id
        $tr = HTMLContainer::generateMarkup('td',
                HTMLInput::generateMarkup(($this->nameRS->idName->getStoredVal() == 0 ? '' : $this->nameRS->idName->getStoredVal())
                        , array('name'=>$idPrefix.'idName', 'readonly'=>'readonly', 'size'=>'5', 'style'=>'border:none;background-color:transparent;'))
                );

        // Prefix
        $tr .= HTMLContainer::generateMarkup('td', HTMLSelector::generateMarkup(
                HTMLSelector::doOptionsMkup($uS->nameLookups[GL_TableNames::NamePrefix],
                        $this->nameRS->Name_Prefix->getstoredVal(), TRUE), array('name'=>$idPrefix.'selPrefix')));

        // First Name
        $tr .= HTMLContainer::generateMarkup('td',
                HTMLInput::generateMarkup($this->nameRS->Name_First, array('name'=>$idPrefix.'txtFirstName', 'class'=>'hhk-firstname')));

        // Middle Name
        $tr .= HTMLContainer::generateMarkup('td', HTMLInput::generateMarkup($this->nameRS->Name_Middle, array('name'=>$idPrefix.'txtMiddleName', 'size'=>'5')));

        // Last Name
        $tr .= HTMLContainer::generateMarkup('td', HTMLInput::generateMarkup($this->nameRS->Name_Last, array('name'=>$idPrefix.'txtLastName', 'class'=>'hhk-lastname')));

        // Suffix
        $tr .= HTMLContainer::generateMarkup('td', HTMLSelector::generateMarkup(
                HTMLSelector::doOptionsMkup($uS->nameLookups[GL_TableNames::NameSuffix],
                        $this->nameRS->Name_Suffix, TRUE), array('name'=>$idPrefix.'selSuffix')));

        // Nick Name
        $tr .= HTMLContainer::generateMarkup('td', HTMLInput::generateMarkup($this->nameRS->Name_Nickname, array('name'=>$idPrefix.'txtNickname', 'size'=>'10')));

        // Status
        $tr .= HTMLContainer::generateMarkup('td', HTMLSelector::generateMarkup(
                HTMLSelector::doOptionsMkup(removeOptionGroups($uS->nameLookups[GL_TableNames::MemberStatus]),
                        $this->nameRS->Member_Status, FALSE), array('name'=>$idPrefix.'selStatus')));

        // Basis
        $basis = array();
        foreach ($uS->nameLookups[GL_TableNames::MemberBasis] as $b) {
            if ($b[Member::SUBT] == $this->getMemberDesignation()) {
                $basis[$b[Member::CODE]] = $b;
            }
        }
        $tr .= HTMLContainer::generateMarkup(
                'td',
                HTMLSelector::generateMarkup(
                        HTMLSelector::doOptionsMkup(
                                removeOptionGroups($basis),
                                $this->nameRS->Member_Type, FALSE), array('name'=>$idPrefix.'selMbrType')
                        )
                );

        $table->addBodyTr($tr);
        return $table->generateMarkup();
    }


    /**
     *
     * @param PDO $dbh
     * @param string $inputClass HTML class attribute for each control
     * @param bool $showOrientDate
     * @return string HTML UL with following DIV tab panels
     */
    public function createMiscTabsMarkup() {

        $panels = "";
        $tabs = "";
        $attrs = array('id'=>'adminTab', 'class'=>'ui-tabs-hide');

        $panels .= HTMLContainer::generateMarkup(
                'div',
                $this->createAdminPanel(),
                $attrs);

        $excl = $this->createExcludesPanel();
        $attrs['id'] = 'excludesTab';
        $panels .= HTMLContainer::generateMarkup(
                'div',
                $excl['markup'],
                $attrs);

        $attrs['id'] = 'miscTab';
        $panels .= HTMLContainer::generateMarkup(
                'div',
                $this->createDemographicsPanel(),
                $attrs);

        $tabs .= HTMLContainer::generateMarkup('li',
                HTMLContainer::generateMarkup('a', 'Admin', array('href'=>'#adminTab', 'title'=>'Administrative Details'))
                );

        $tabs .= HTMLContainer::generateMarkup('li',
                HTMLContainer::generateMarkup('a', $excl['tabIcon'] . 'Exclude', array('href'=>'#excludesTab', 'title'=>'Exclude Addresses'))
                );

        $tabs .= HTMLContainer::generateMarkup('li',
                HTMLContainer::generateMarkup('a', 'Demographics', array('href'=>'#miscTab', 'title'=>'Miscellaneous demographics'))
                );

                // wrap tabs in a UL
        $ul = HTMLContainer::generateMarkup('ul', $tabs);

        return $ul . $panels;

    }

    public function createAdminPanel() {

        $table = new HTMLTable();

        $table->addBodyTr(
                HTMLTable::makeTd('Last Updated:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd(HTMLContainer::generateMarkup('span', $this->get_lastUpdated()), array('style'=>'display:table-cell;'))
                );

        $table->addBodyTr(
                HTMLTable::makeTd('Updated By:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd(HTMLContainer::generateMarkup('span', $this->get_updatedBy()), array('style'=>'display:table-cell;'))
                );

        $table->addBodyTr(
                HTMLTable::makeTd('Date Added:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd(HTMLContainer::generateMarkup('span', $this->get_dateAdded()), array('style'=>'display:table-cell;'))
                );

        $table->addBodyTr(
                HTMLTable::makeTd('Orientation:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd(HTMLInput::generateMarkup(($this->get_orientationDate() != "" ? date('M j, Y', strtotime($this->get_orientationDate())) : ""), array('name'=>'txtOrienDate', 'class'=>'ckdate')), array('style'=>'display:table-cell;'))
                );
        return $table->generateMarkup();
    }
    /**
     *
      * @return string HTML table structure
     */
    public function createDemographicsPanel() {

        $uS = Session::getInstance();

        $table = new HTMLTable();
        $table->addBodyTr(
                HTMLTable::makeTd('Birth Month:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd($this->prepBirthMonthMarkup($this->get_bmonth()), array('style'=>'display:table-cell;'))
                );

        $table->addBodyTr(
                HTMLTable::makeTd('Gender:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd(
                        HTMLSelector::generateMarkup(
                                HTMLSelector::doOptionsMkup($uS->nameLookups[GL_TableNames::Gender], $this->nameRS->Gender->getStoredVal()),
                                array('name'=>'selGender')
                                )
                        , array('style'=>'display:table-cell;')
                        )
                );

        $table->addBodyTr(
                HTMLTable::makeTd('Age Range:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd(
                        HTMLSelector::generateMarkup(
                                HTMLSelector::doOptionsMkup($uS->nameLookups[GL_TableNames::AgeBracket], $this->demogRS->Age_Bracket->getStoredVal()),
                                array('name'=>'selAgeRange')
                                )
                        , array('style'=>'display:table-cell;')
                        )
                );

        $table->addBodyTr(
                HTMLTable::makeTd('Ethnicity:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd(
                        HTMLSelector::generateMarkup(
                                HTMLSelector::doOptionsMkup($uS->nameLookups[GL_TableNames::Ethnicity], $this->demogRS->Ethnicity->getStoredVal()),
                                array('name'=>'selEthnicity')
                                )
                        , array('style'=>'display:table-cell;')
                        )
                );

        $table->addBodyTr(
                HTMLTable::makeTd('Special Needs:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd(
                        HTMLSelector::generateMarkup(
                                HTMLSelector::doOptionsMkup($uS->nameLookups[GL_TableNames::SpecialNeeds], $this->demogRS->Special_Needs->getStoredVal()),
                                array('name'=>'selNeeds')
                                )
                        , array('style'=>'display:table-cell;')
                        )
                );

        $table->addBodyTr(
                HTMLTable::makeTd('Previous Name:', array('class'=>'tdlabel'))
                . HTMLTable::makeTd(
                        HTMLInput::generateMarkup(
                                $this->nameRS->Name_Previous,
                                array('name'=>'txtPreviousName', 'size'=>'9')
                                )
                        , array('style'=>'display:table-cell;')
                        )
                );

        $table->addBodyTr(
                HTMLTable::makeTd('Media Source:', array('class'=>'tdlabel', 'title'=>'How did you hear of us?'))
                . HTMLTable::makeTd(
                        HTMLSelector::generateMarkup(
                                HTMLSelector::doOptionsMkup($uS->nameLookups['Media_Source'], $this->demogRS->Media_Source->getStoredVal()),
                                array('name'=>'selMedia', 'title'=>'How did you hear of us?')
                                )
                        , array('style'=>'display:table-cell;')
                        )
                );

        // Newsletter
        $nlAttr = array('type'=>'checkbox', 'name'=>'cbnewsltr', 'title'=>'Receive our newsletter?');
        if ($this->demogRS->Newsletter->getStoredVal() > 0) {
            $nlAttr['checked'] = 'checked';
        }
        $table->addBodyTr(
                HTMLTable::makeTd('Newsletter:', array('class'=>'tdlabel', 'title'=>'Receive our newsletter?'))
                . HTMLTable::makeTd(
                        HTMLInput::generateMarkup('', $nlAttr)
                        , array('style'=>'display:table-cell;')
                        )
                );

        return $table->generateMarkup();

    }

    /**
     *
     * @param array $rel Array of relationship types
     * @param string $page Link to page for related members
     * @return string HTML markup
     */
    public function createRelationsTabs(array $rel, $page = "NameEdit.php", \iStudent $student = null) {

        $stuDiv = '';
        if (is_null($student) === FALSE && $this->get_idName() > 0) {
            $stuTab = HTMLContainer::generateMarkup('div',$student->createMarkup($page), array('style'=>'float:left; margin-left:20px;'));
            $stuDiv = HTMLContainer::generateMarkup('div', $stuTab, array('id'=>'stu', 'class'=>'ui-tabs-hide'));
        }

        $relTab = HTMLContainer::generateMarkup('div', $rel[RelLinkType::Spouse]->createMarkup($page), array('style'=>'float:left; margin-left:20px;'))
                .HTMLContainer::generateMarkup('div',$rel[RelLinkType::Sibling]->createMarkup($page), array('style'=>'float:left; margin-left:20px;'))
                . HTMLContainer::generateMarkup('div',$rel[RelLinkType::Parnt]->createMarkup($page), array('style'=>'float:left; margin-left:20px;'))
                . HTMLContainer::generateMarkup('div',$rel[RelLinkType::Child]->createMarkup($page), array('style'=>'float:left; margin-left:20px;'))
                . HTMLContainer::generateMarkup('div',$rel[RelLinkType::Relative]->createMarkup($page), array('style'=>'float:left; margin-left:20px;'));

        $coTab = $this->createOrgMarkup($rel, $page);

        $stuIcon = '';
        if ($student->numberMembers() > 0) {
            $stuIcon = HTMLContainer::generateMarkup('span', '', array('class'=>'ui-icon ui-icon-check', 'style'=>'float:left; margin-right: 0.3em;'));
        }

        $ul = HTMLContainer::generateMarkup('ul',
            HTMLContainer::generateMarkup('li', HTMLContainer::generateMarkup('a', 'Relatives', array('href'=>'#relvs')))
            . HTMLContainer::generateMarkup('li', HTMLContainer::generateMarkup('a', 'Company', array('href'=>'#copt')))
            .(is_null($student) === FALSE ? HTMLContainer::generateMarkup('li', HTMLContainer::generateMarkup('a', $stuIcon . $student->getTabTitle(), array('href'=>'#stu'))) : '')
            );

        $coptDiv = HTMLContainer::generateMarkup('div', $coTab, array('id'=>'copt', 'class'=>'ui-tabs-hide'));
        $relDiv = HTMLContainer::generateMarkup('div', $relTab, array('id'=>'relvs', 'class'=>'ui-tabs-hide'));

        return $ul . $coptDiv . $relDiv . $stuDiv;
    }

    /**
     *
     * @param array $rel Array of relationship types
     * @param string $page link
     * @return string HTML markup
     */
    public function createOrgMarkup(array $rel, $page = "NameEdit.php") {

        $table = new HTMLTable();
        $table->addHeaderTr(
                HTMLTable::makeTh('Title')
        );

        $table->addBodyTr(
                 // title
                HTMLTable::makeTd(HTMLInput::generateMarkup($this->get_title(), array('name' => 'txtTitle', 'size' => '10'))
                )
        );

        $coTab = $table->generateMarkup(array('style'=>'float:left;')) . HTMLContainer::generateMarkup('div', $rel[RelLinkType::Company]->createMarkup($page), array('style'=>'float:left; margin-left:20px;'));

        return $coTab;
    }


    public function loadRealtionships(PDO $dbh) {

       return array(
            RelLinkType::Sibling => new Siblings($dbh, $this->get_idName()),
            RelLinkType::Child => new Children($dbh, $this->get_idName()),
            RelLinkType::Parnt => new Parents($dbh, $this->get_idName()),
            RelLinkType::Spouse => new Partner($dbh, $this->get_idName()),
            RelLinkType::Company => new Company($dbh, $this->get_idName()),
            RelLinkType::Relative => new Relatives($dbh, $this->get_idName())
            );
    }


    private function prepBirthMonthMarkup($month) {

        $markup = "<select name='selBirthMonth' id='selBirthMonth'>";
        $monthList = array(0 => "", 1 => "(1) Jan", 2 => "(2) Feb", 3 => "(3) Mar", 4 => "(4) Apr", 5 => "(5) May", 6 => "(6) Jun", 7 => "(7) Jul", 8 => "(8) Aug", 9 => "(9) Spt", 10 => "(10) Oct", 11 => "(11) Nov", 12 => "(12) Dec");
        for ($i = 0; $i < 13; $i++) {
            if ($i === $month) {
                $markup .= "<option value='$i' selected='selected'>" . $monthList[$i] . "</option>";
            } else {
                $markup .= "<option value='$i'>" . $monthList[$i] . "</option>";
            }
        }
        $markup .= "</select>";
        return $markup;
    }

    public function getAssocDonorLabel() {
        return "Associate";
    }

    public function getAssocDonorList(array $rel) {
        $rA = array();
        $partner = $rel[RelLinkType::Spouse];

        if (count($partner->getRelNames()) > 0) {
            $rNames = $partner->getRelNames();
            $rA[$rNames[0]['Id']] = array(0=>$rNames[0]['Id'], 1=>'Spouse');
        }
        return $rA;
    }

    public function getDefaultDonor(array $rel) {

        $partner = $rel[RelLinkType::Spouse];

        if (count($partner->getRelNames()) > 0) {
            $rNames = $partner->getRelNames();
            return $rNames[0]['Id'];
        }
        return '';

    }

    /**
     *
     * @param PDO $dbh
     * @param array $post
     * @throws Hk_Exception_Runtime
     */
    protected function processMember(array $post) {
        // Convenience var
        $n = $this->nameRS;
        $idPrefix = $this->getIdPrefix();

        //  Name
        if (isset($post[$idPrefix.'txtFirstName'])) {
            $n->Name_First->setNewVal(ucfirst(trim(filter_var($post[$idPrefix.'txtFirstName'], FILTER_SANITIZE_STRING))));
        }

        if (isset($post[$idPrefix.'txtLastName'])) {
            $n->Name_Last->setNewVal(ucfirst(trim(filter_var($post[$idPrefix.'txtLastName'], FILTER_SANITIZE_STRING))));
        }

        if (isset($post[$idPrefix.'txtMiddleName'])) {
            $n->Name_Middle->setNewVal(ucfirst(trim(filter_var($post[$idPrefix.'txtMiddleName'], FILTER_SANITIZE_STRING))));
        }

        if (isset($post[$idPrefix.'selPrefix'])) {
            $n->Name_Prefix->setNewVal(filter_var($post[$idPrefix.'selPrefix'], FILTER_SANITIZE_STRING));
        }

        if (isset($post[$idPrefix.'selSuffix'])) {
            $n->Name_Suffix->setNewVal(filter_var($post[$idPrefix.'selSuffix'], FILTER_SANITIZE_STRING));
        }

        // Minimum requirements for saving a record.
        if ((is_null($n->Name_Last->getNewVal()) && $n->Name_Last->getStoredVal() == '') || $n->Name_Last->getNewVal() == '') {
            throw new Hk_Exception_Runtime("The Last Name cannot be blank.");
        }

        // Name Last-First
        if ($n->Name_First->getNewVal() != '') {
            $first = ', ' . $n->Name_First->getNewVal();
        } else {
            $first = '';
        }
        $n->Name_Last_First->setNewVal($n->Name_Last->getNewVal() . $first);


        // Name Full
        $uS = Session::getInstance();
        $prefix = '';
        $suffix = '';
        $qstring = '';
        if (isset($uS->nameLookups[GL_TableNames::NamePrefix][$n->Name_Prefix->getNewVal()])) {
            $prefix = $uS->nameLookups[GL_TableNames::NamePrefix][$n->Name_Prefix->getNewVal()][Member::DESC];
        }
        if (isset($uS->nameLookups[GL_TableNames::NameSuffix][$n->Name_Suffix->getNewVal()])) {
            $suffix = $uS->nameLookups[GL_TableNames::NameSuffix][$n->Name_Suffix->getNewVal()][Member::DESC];
        }

        if ($n->Name_Middle->getNewVal() != "") {
            $qstring .= trim($prefix . " " . $n->Name_First->getNewVal() . " " . $n->Name_Middle->getNewVal() . " " . $n->Name_Last->getNewVal() . " " . $suffix);
        } else {
            $qstring .= trim($prefix . " " . $n->Name_First->getNewVal() . " " . $n->Name_Last->getNewVal() . " " . $suffix);
        }
        $n->Name_Full->setNewVal($qstring);


        //  Title
        if (isset($post[$idPrefix.'txtTitle'])) {
            $n->Title->setNewVal(filter_var($post[$idPrefix.'txtTitle'], FILTER_SANITIZE_STRING));
        }

        //  Previous Name
        if (isset($post[$idPrefix.'txtPreviousName'])) {
            $n->Name_Previous->setNewVal(ucfirst(trim(filter_var($post[$idPrefix.'txtPreviousName'], FILTER_SANITIZE_STRING))));
        }

        //  Nickname
        if (isset($post[$idPrefix.'txtNickname'])) {
            $n->Name_Nickname->setNewVal(ucfirst(trim(filter_var($post[$idPrefix.'txtNickname'], FILTER_SANITIZE_STRING))));
        }

        //  Gender
        if (isset($post[$idPrefix.'selGender'])) {
            $n->Gender->setNewVal(filter_var($post[$idPrefix.'selGender'], FILTER_SANITIZE_STRING));
        }

        //  Age
        if (isset($post[$idPrefix.'selAgeRange'])) {
            $this->demogRS->Age_Bracket->setNewVal(filter_var($post[$idPrefix.'selAgeRange'], FILTER_SANITIZE_STRING));
        }

        //  Ethnicity
        if (isset($post[$idPrefix.'selEthnicity'])) {
            $this->demogRS->Ethnicity->setNewVal(filter_var($post[$idPrefix.'selEthnicity'], FILTER_SANITIZE_STRING));
        }

        //  Special Needs
        if (isset($post[$idPrefix.'selNeeds'])) {
            $this->demogRS->Special_Needs->setNewVal(filter_var($post[$idPrefix.'selNeeds'], FILTER_SANITIZE_STRING));
        }

        //  Media Source
        if (isset($post[$idPrefix.'selMedia'])) {
            $this->demogRS->Media_Source->setNewVal(filter_var($post[$idPrefix.'selMedia'], FILTER_SANITIZE_STRING));
        }

        //  Newsletter
        if (isset($post[$idPrefix.'cbnewsltr'])) {
            $this->demogRS->Newsletter->setNewVal(1);
        } else {
            $this->demogRS->Newsletter->setNewVal(0);
        }

        //  Birth Month
        if (isset($post[$idPrefix.'selBirthMonth'])) {
            $n->Birth_Month->setNewVal(filter_var($post[$idPrefix.'selBirthMonth'], FILTER_SANITIZE_NUMBER_INT));
        }

    }

    /**
     *
     * @param mixed $v
     * @throws Hk_Exception_InvalidArguement
     */
    public function set_companyRcrd($v) {
        if ($v == 1 || $v == TRUE) {
            throw new Hk_Exception_InvalidArguement("Individual Member Record cannot be set to Organization.");
        }
    }

    public function getAgeRange() {
        return $this->demogRS->Age_Bracket->getStoredVal();
    }

    public function setAgeRange($v) {
        $this->demogRS->Age_Bracket->setNewVal($v);
    }

    public function getEthnicity() {
        return $this->demogRS->Ethnicity->getStoredVal();
    }

     public function getMediaSource() {
        return $this->demogRS->Media_Source->getStoredVal();
    }

    public function setMediaSource($v) {
        $this->demogRS->Media_Source->setNewVal($v);
    }

     public function getNewsletter() {
        return $this->demogRS->Newsletter->getStoredVal();
    }

    public function setNewsletter($v) {
        $this->demogRS->Newsletter->setNewVal($v);
    }


}

?>
