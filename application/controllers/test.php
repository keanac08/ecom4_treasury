<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
	
	public function index(){
		
		$array = $this->uri->uri_to_assoc(4);
		echo $array['joe'];
	}
	
}
