<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('sales_order/vehicle_model');
		session_check();
	}
	
	public function tagged(){
		
		$customer_id = $this->session->tre_portal_customer_id;
		
		$data['content'] = 'sales_order/tagged_units_view';
		$data['title'] = 'Sales Order <small>Tagged Units</small>';
		$data['head_title'] = 'Treasury | Tagged Units';
		
		$data['result'] = $this->vehicle_model->get_tagged($customer_id);
		
		$this->load->view('include/template',$data);
	}
	
	public function request_for_invoice(){
		
		//~ echo '<pre>';
		//~ print_r($this->input->post('cs_numbers'));
		//~ echo '</pre>';
		
		foreach($this->input->post('cs_numbers') as $row){
			
			$params = array(
							$this->session->tre_portal_customer_id,
							$row,
							date('d-M-y')
						);
			$this->vehicle_model->new_request_for_invoice($params);
		}
	}
}
