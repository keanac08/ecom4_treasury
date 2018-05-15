<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('users/log_model');
		session_check();
	}
	
	public function index(){
		
		$data['from_date'] = $this->input->post('from_date');
		$data['to_date'] = $this->input->post('to_date');
		
		$data['content'] = 'users/logs_view';
		$data['title'] = 'User Logs';
		$data['head_title'] = 'Treasury | User Logs';
		
		$data['result'] = $this->log_model->get_user_logs($this->input->post('from_date'), $this->input->post('to_date'));
		
		$this->load->view('include/template',$data);
	}
}
