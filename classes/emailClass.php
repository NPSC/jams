<?php
/**
 * emailClass.php
 *
 * @category  Site
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

/**
 * Description of emailClass
 *
 *
 */
class emailClass {

    protected $to = "";
    protected $from = "";
    protected $replyTo = '';
    protected $cc = "";
    protected $bcc = "";
    protected $subj = "";
    protected $body = "";
    protected $headers = "";
    private $CR = "\r\n";

    function __construct() {
        // To send HTML mail, the Content-type header must be set
        $this->set_headers('MIME-Version: 1.0' . $this->CR . 'Content-type: text/html; charset=iso-8859-1' . $this->CR);
        // Additional headers
        //$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";

    }



    public function send() {

        if (($smtp = ini_get('SMTP')) === FALSE || $smtp == '') {
            return FALSE;
        }


        if ($this->checkValidity() === FALSE) {
            return false;
        }

        $hdr = $this->get_headers();

        // Additional headers
        $hdr .= $this->get_from_header();

        if ($this->get_bcc() != "") {
            $hdr .= $this->get_bcc_header ();
        }

        if ($this->get_replyTo() != "") {
            $hdr .= $this->get_replyTo_header ();
        }

        if ($this->get_cc() != "") {
            $hdr .= $this->get_cc_header ();
        }

        return mail($this->get_to(), $this->get_subject(), $this->get_body(), $hdr);

    }


    function checkValidity() {
        if ($this->get_subject() == "" || $this->get_body() == "" || $this->get_to() == "" || $this->get_from() == "") {
           return false;
        } else {
            return true;
        }
    }


//********************************************************************
    public function set_headers($v) {
        $this->headers = $v;
    }
    public function get_headers() {
        return $this->headers;
    }

//********************************************************************
    public function set_to($v) {
        $this->to = $v;
    }
    public function get_to() {
        return $this->to;
    }
//********************************************************************
    public function set_bcc($v) {
        $this->bcc = $v;
    }
    public function get_bcc() {
        return $this->bcc;
    }
    protected function get_bcc_header() {
        return 'Bcc: ' . $this->get_bcc() . $this->CR;
    }
//********************************************************************
    public function set_cc($v) {
        $this->cc = $v;
    }
    public function get_cc() {
        return $this->cc;
    }
    protected function get_cc_header() {
        return 'Cc: ' . $this->get_cc() . $this->CR;
    }
//********************************************************************
    public function set_from($v) {
        $this->from = $v;
    }
    public function get_from() {
        return $this->from;
    }
    protected function get_from_header() {
        return 'From: ' . $this->get_from() . $this->CR;
    }
//********************************************************************
    public function set_replyTo($v) {
        $this->replyTo = $v;
    }
    public function get_replyTo() {
        return $this->replyTo;
    }
    protected function get_replyTo_header() {
        return 'Reply-To: ' . $this->get_replyTo() . $this->CR;
    }
//********************************************************************
    public function set_subject($v) {
        $this->subj = $v;
    }
    public function get_subject() {
        return $this->subj;
    }
//********************************************************************
    public function set_body($v) {
        $this->body = $v;
    }
    public function get_body() {
        return $this->body;
    }
}
?>
