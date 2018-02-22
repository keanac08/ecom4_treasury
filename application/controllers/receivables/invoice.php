<?php 
class Invoice extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		session_check();
	}
	
	public function vehicle_by_due_date_modal(){

		$data[] = '';
		echo $this->load->view('reports/vehicle_invoice_by_due_date_view',$data, true);
	}
	
	public function vehicle_by_due_date_form(){
		
		$data['content'] = 'reports/vehicle_invoice_by_due_date_view';
		$data['head_title'] = 'Treasury | Reports';
		$data['title'] = 'Vehicle Invoice Report';
		$data['subtitle'] = 'By Due Date Range';
		
		$this->load->view('include/template',$data);
	}
}
