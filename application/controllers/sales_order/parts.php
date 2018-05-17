<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parts extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('sales_order/parts_model');
		session_check();
	}
	
	public function status(){
		
		$data['from_date'] = $this->input->post('from_date');
		$data['to_date'] = $this->input->post('to_date');
		$customer_id = $this->session->tre_portal_customer_id;
		
		$data['content'] = 'sales_order/parts_status_view';
		$data['title'] = 'Sales Order Status';
		$data['head_title'] = 'Treasury |Sales Order';
		
		$data['result'] = $this->parts_model->get_parts_so_status($customer_id, $data['from_date'], $data['to_date']);
		
		$this->load->view('include/template',$data);
	}
}
