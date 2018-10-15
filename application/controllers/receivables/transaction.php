<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class Transaction extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/transaction_model');
		session_check();
	}
	
	public function per_customer(){
		
		//~ echo '<pre>';
		//~ print_r($this->session->userdata);
		//~ echo '</pre>';
		
		$data['content']    = 'receivables/transactions_per_customer_view';
		$data['head_title'] = 'Treasury | Home';
		$data['title']      = 'Transaction Summary<small>Per Customer</small>';
		
		$as_of_date_orig = $this->input->post('as_of_date');
		
		if(in_array($this->session->tre_portal_user_type, array('Administrator','Regular User'))){
			$customer_id = $this->input->post('customer_id');
			$customer_name = $this->input->post('customer_name');
			$profile_class_id = NULL;
		}
		else if(in_array($this->session->tre_portal_user_type, array('IPC Parts','IPC Vehicle-Fleet','IPC Vehicle', 'IPC Fleet'))){
			
			$this->load->helper('profile_class_helper');
			
			$profile_class_id = get_user_access($this->session->tre_portal_user_type);
			$customer_id = $this->input->post('customer_id');
			$customer_name = $this->input->post('customer_name');
		}
		else if($this->session->tre_portal_user_type == 'Dealer Admin'){
			
			$customer_id = $this->session->tre_portal_customer_id;
			$rows = $this->transaction_model->get_customer($customer_id);
			$customer_name = $rows[0]->CUSTOMER_NAME;
			$profile_class_id = NULL;
			
			$this->session->set_userdata('tre_portal_customer_name', $customer_name);
		}
		else if(in_array($this->session->tre_portal_user_type, array('Dealer Parts','Dealer Vehicle'))){
			
			$this->load->helper('profile_class_helper');
			$profile_class_id = get_user_access($this->session->tre_portal_user_type);
			
			$customer_id = $this->session->tre_portal_customer_id;
			$rows = $this->transaction_model->get_customer($customer_id);
			$customer_name = $rows[0]->CUSTOMER_NAME;
			
			$this->session->set_userdata('tre_portal_customer_name', $customer_name);
		}
		
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
		
		$data['results'] = $this->transaction_model->get_summary_per_customer($customer_id, $as_of_date1, $profile_class_id);

		$this->load->view('include/template',$data);
	}
	
	public function ajax_customer_list(){
		
		$q = strtolower('%'.$this->input->get('q').'%');
		
		$return_arr = array();
		
		if(in_array($this->session->tre_portal_user_type, array('IPC Parts','IPC Vehicle-Fleet','IPC Vehicle','IPC Fleet'))){
			$this->load->helper('profile_class_helper');
			$profile_class_id = get_user_access($this->session->tre_portal_user_type);
			
		}
		else{
			$profile_class_id = NULL;
		}
		
		//~ echo $profile_class_id;
		
		$data =  $this->transaction_model->get_customers($q, $profile_class_id);
		
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
