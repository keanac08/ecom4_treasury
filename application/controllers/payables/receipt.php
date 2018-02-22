<?php 
class Receipt extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('file');
		$this->load->model('payables/check_writer_model');
		session_check();
	}
	
	public function unapplied_receipt_modal(){

		$data[] = '';
		echo $this->load->view('reports/unapplied_receipt_view',$data, true);
	}
}
