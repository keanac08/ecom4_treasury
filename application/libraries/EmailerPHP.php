<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/PHPMailer/class.phpmailer.php";
require_once APPPATH."/third_party/PHPMailer/class.smtp.php";

class EmailerPHP extends PHPMailer {
	public function __construct() {
		parent::__construct();
		//~ $this->SMTPDebug = 3;                             // Enable verbose debug output
		$this->isSMTP();                                      // Set mailer to use SMTP
		$this->CharSet = "iso-8859-1";
		$this->Host = 'smtp.office365.com';                   // Specify main and backup SMTP servers
		$this->SMTPAuth = true;                               // Enable SMTP authentication
		$this->Username = 'interface-notification@isuzuphil.com';    // SMTP username
		$this->Password = 'Luk@71az55';                        // SMTP password
		$this->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$this->Port = 587;                                    // TCP port to connect to
		$this->From = 'interface-notification@isuzuphil.com';
		$this->FromName = 'System Notfication';
		$this->isHTML(true);                                  // Set 
	}
}
