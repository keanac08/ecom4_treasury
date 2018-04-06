<?php 
class Reserved_units extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		session_check();
	}
	
	public function reserved_units_modal(){

		$data[] = '';
		echo $this->load->view('reports/vehicle_invoice_by_due_date_view',$data, true);
	}
	
}
