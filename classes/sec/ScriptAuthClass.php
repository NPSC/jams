<?php
/**
 * ScriptAuthClass.php
 *
 * @category  Site
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
class ScriptAuthClass extends SecurityComponent {

    private $siteCode = "";
    private $indexPage = "";
    private $pageCodes = array();

    private $fileName = "";
    private $pageTitle = "";
    private $loginPage = "";
    private $defaultPage = "";
    private $pageType = "";
    private $path = "";
    private $hostName = "";
    private $siteURL = "";

    function __construct(PDO $dbh) {

        $scriptName = filter_var($_SERVER['SCRIPT_NAME'], FILTER_SANITIZE_STRING);
        $serverName = filter_var($_SERVER['SERVER_NAME'], FILTER_SANITIZE_URL);

        if (is_null($scriptName) || $scriptName === FALSE) {
            session_destroy();
            exit('Script name not set.');
        }

        if (is_null($serverName) || $serverName === FALSE) {
            session_destroy();
            exit('Server name not set.');
        }

        // find out what page we are on
        $parts = explode("/", $scriptName);
        $this->fileName = $parts[count($parts) - 1];

        $parts[count($parts) - 1] = "";   // remove file name
        $this->path = implode("/", $parts);


        // remove leading www if present.
        $hostParts = explode(".", $serverName);
        if (strtolower($hostParts[0]) == "www") {
            $hostParts[0] = "";
            $this->hostName = implode(".", $hostParts);
            // remove first dot
            $this->hostName = substr($this->hostName, 1);
        } else {
            $this->hostName = $serverName;
        }

        $this->siteURL = "https://" . $this->hostName . $this->path;


        // try reading the web site table
        try {
            $site = self::loadWebSite($dbh, $this->hostName, $this->path);
        } catch (Hk_Exception $hex) {
            session_destroy();
            exit("error: ".$hex->getMessage());
        }

        if (!is_null($site)) {
            $this->siteCode = $site["Site_Code"];
            $this->indexPage = $site["Index_Page"];
            $this->defaultPage = $site["Default_Page"];
        }

        // try reading the page table
        if ($this->siteCode != "" && $this->fileName != "") {
            $uS = Session::getInstance();

            if (isset($uS->webPages[$this->fileName])) {
                $page = $uS->webPages[$this->fileName];

                if (!is_null($page)) {
                    //$this->pageId = $r["idPage"];
                    $this->pageCodes = $page["Codes"];
                    $this->pageTitle = $page["Title"];
                    $this->loginPage = $page["Login"];
                    $this->pageType = $page["Type"];
                }
            }
        }
    }

    public function Authorize_Or_Die() {

        self::die_if_not_Logged_In($this->pageType, $this->loginPage, $this->fileName);

        $tokn = self::is_Admin();

        if ($tokn == FALSE) {
            // Not the admin, so check authorization codes.
            $tokn = self::does_User_Code_Match($this->pageCodes);

            if ($tokn == FALSE) {
                if ($this->pageType == "p") {
                    include("../errorPages/forbid.html");
                } else if ($this->pageType == "s") {
                    $rtn = array("error" => "Unauthorized");
                    echo json_encode($rtn);
                } else {
                    echo("No Such Page.  <a href='" . $this->indexPage . "'>Continue</a>");
                }
                exit();
            }
        }
    }

    public function get_Login_Page() {
        if ($this->loginPage != '') {
            return $this->loginPage;
        } else {
            return 'index.php';
        }
    }

    public function get_Default_Page() {
        return $this->defaultPage;
    }

    public function get_Site_Code() {
        return $this->siteCode;
    }

    public function get_Page_Title() {
        return $this->pageTitle;
    }

    public function get_ScriptFilename() {
        return $this->fileName;
    }

    public function get_Page_Type() {
        return $this->pageType;
    }

    public function generateMenu($pageHeader, $resourceURL) {
        // only generate menu for pages, not services or components
        if ($this->pageType != WebPageCode::Page) {
            return '';
        }
        $menu = array();
        $uS = Session::getInstance();
        $pageAnchors = array();

        foreach ($uS->webPages as $fn => $r) {

            if ($r['Type'] != WebPageCode::Page) {
                continue;
            }

            if (self::is_Admin() || self::does_User_Code_Match($r['Codes'])) {
                // remove leading underline char - special code for level 1 page names.
                $fn = str_replace('_', '', $fn);
                // Get my data
                if (self::is_TheAdmin()) {
                    // Show auth codes for admin account for sanity check
                    $pageAnchors[$r['idPage']]['Title'] = $r['Title'] . " (" . $r['Codes'][0] . ")";
                    $pageAnchors[$r['idPage']]['File_Name'] = $fn;
                } else {
                    $pageAnchors[$r['idPage']]['Title'] = $r['Title'];
                    $pageAnchors[$r['idPage']]['File_Name'] = $fn;
                }
                $menu[$r['Parent']][$r['Position']] = $r['idPage'];
            }
        }


        //$markup = "<script type='text/javascript' src='" . $resourceURL . "js/topNav.js'></script>";

        $markup = "<header><section id='global-nav'><nav><div class='constrain'>" . self::getSiteIcons($uS->ssl, $uS->siteList, $uS->tutURL);
        $markup .= "<div id='global-title'>$pageHeader</div><div style='float:right;'>";
        // process
        foreach ($menu["0"] as $item) {

            $markup .= "<ul class='links'>";

            if (isset($menu[$item])) {
                $markup .= "<li class='dropdown'><a href='#'>" . $pageAnchors[$item]["Title"] . "</a>";
                // children to capture
                $chd = '';
                foreach ($menu[$item] as $k => $chld) {
                    if (strtolower($k) != "none") {
                        $chd .= "<li><a href='".$pageAnchors[$chld]["File_Name"]."'>" . $pageAnchors[$chld]["Title"] . "</a></li>";
                    }
                }
                if ($chd != '') {
                    $markup .= "<ul>" . $chd . "</ul>";
                }
            } else {
                $markup .= "<li><a href='".$pageAnchors[$item]["File_Name"]."'>" . $pageAnchors[$item]["Title"] . "</a>";
            }
            $markup .= "</li></ul>";
        }

        $markup .= "</div></div></nav></section></header>
            <div id='version'>User:" . $uS->username . ", Build:" . $uS->ver . "</div>";

        return $markup;
    }


    protected static function getSiteIcons($isSSL, $siteList, $tutorialURL) {

        $mu = "<ul class='projects'>";
        $siteCount = 0;
        $proto = 'http://';
        if ($isSSL) {
            $proto = 'https://';
        }

        foreach ($siteList as $r) {

            if ($r["Site_Code"] != "r" && (self::is_Admin() || self::does_User_Code_Match($r["Groups"]))) {
                $siteCount++;
                // put in the site list.
                $mu .= "<li class='project'>"
                      . "<a title='" . $r["Description"] . "' class=' ui-corner-all' href='" . $proto . $r["HTTP_Host"] . $r["Relative_Address"] . $r["Default_Page"] . "'>"
                        . "<span class='" . $r["Class"] . "' ></span></a>"
                      . "</li>";
            }
        }

        // Tutorial site
        if ($tutorialURL != '') {
            $siteCount++;
            // put in the site list.
            $mu .= "<li class='project'>"
                  . "<a title='Tutorial Site' class=' ui-corner-all' href='" . $tutorialURL . "' target='blank'>"
                  . "<span class='ui-icon ui-icon-video' ></span></a>"
                  . "</li>";
        }

        if ($siteCount > 1) {
            $mu .= "</ul>";
        } else {
            // Only 1 site = no markup.
            $mu = '';
        }

        return $mu;
    }


}
