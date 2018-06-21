<?php 
class Payment extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		session_check();
		$this->load->model('receivables/payment_model');
	}
	
	public function vehicle(){
		
		$data['content'] = 'receivables/payment_vehicle_view';
		$data['head_title'] = 'Treasury | Payments';
		$data['title'] = 'Payments';
		$data['subtitle'] = 'Vehicle';
		$data['results'] = $this->payment_model->get_vehicle_tagged();
		
		$this->load->view('include/template',$data);
	}
	
	public function parts(){
		
		$data['content'] = 'receivables/payment_parts_view';
		$data['head_title'] = 'Treasury | Payments';
		$data['title'] = 'Payments';
		$data['subtitle'] = 'Parts';
		$data['results'] = $this->payment_model->get_parts_invoiced();
		
		$this->load->view('include/template',$data);
	}
}
