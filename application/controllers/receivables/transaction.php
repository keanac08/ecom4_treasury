<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/transaction_model');
		session_check();
	}
	
	public function per_customer(){
		
		$data['content']    = 'receivables/transactions_per_customer_view';
		$data['head_title'] = 'Treasury | Home';
		$data['title']      = 'Transaction Summary<small>Per Customer</small>';
		
		$as_of_date_orig = $this->input->post('as_of_date');
		$customer_id = $this->input->post('customer_id');
		$customer_name = $this->input->post('customer_name');
		
		if($as_of_date_orig != NULL){
			$as_of_date1 =  date('d-M-y', strtotime($as_of_date_orig));
			$as_of_date2 =  date('m/d/Y', strtotime($as_of_date_orig));
		}
		else{
			$as_of_date1 = date('d-M-y');
			$as_of_date2 = date('m/d/Y');
		}
		
		$data['as_of_date'] = $as_of_date2;
		$data['customer_id'] = $customer_id;
		$data['customer_name'] = $customer_name;
		
		$data['results'] = $this->transaction_model->get_summary_per_customer($customer_id, $as_of_date1);

		$this->load->view('include/template',$data);
	}
	
	public function ajax_customer_list(){
		
		$q = strtolower('%'.$this->input->get('q').'%');
		
		$return_arr = array();
		$data =  $this->transaction_model->get_customers($q);
		foreach($data as $row){
			$row_array = array(
							'id'=>$row->CUSTOMER_ID,
							 'text' => $row->CUSTOMER_NAME
						);
			array_push($return_arr,$row_array);
		}
		echo json_encode($return_arr);
	}
}
