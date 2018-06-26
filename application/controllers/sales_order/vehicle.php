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
		$data['title'] = 'Sales Order Reservation';
		$data['head_title'] = 'Treasury | Tagged Units';
		
		$data['result'] = $this->vehicle_model->get_tagged($customer_id);
		
		$this->load->view('include/template',$data);
	}
}
