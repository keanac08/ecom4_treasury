<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  

require_once APPPATH."/third_party/PHP_XLSXWriter/xlsxwriter.class.php";
 
class Excel extends XLSXWriter {

	public function __construct() {
		parent::__construct();
	}
}
