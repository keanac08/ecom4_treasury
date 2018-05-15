<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/check_warehousing_model');
		session_check();
	}
	
	public function pdc(){
		
		$data['from_date'] = $this->input->post('from_date');
		$data['to_date'] = $this->input->post('to_date');
		
		$data['content'] = 'receivables/user_logs_view';
		$data['title'] = 'User Logs';
		$data['head_title'] = 'Treasury | User Logs';
		
		$data['result'] = $this->check_warehousing_model->get_approved_pdc($data['from_date'], $data['to_date']);
		
		$this->load->view('include/template',$data);
	}
}
