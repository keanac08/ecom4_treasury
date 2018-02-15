<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receivables extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables_model');
		session_check();
	}
	
	public function summary(){
		
		$as_of_date = $this->input->post('as_of_date') != NULL ? date('m/d/Y', strtotime($this->input->post('as_of_date'))) : date('m/d/Y');
		
		$data['content'] = 'receivables_summary_view';
		$data['title'] = 'Accounts Receivable Summary';
		$data['head_title'] = 'Treasury | Receivables Summary';

		$data['results'] = $this->receivables_model->get_receivables_summary(date('d-M-y', strtotime($as_of_date)));
		$data['as_of_date'] = $as_of_date;
		
		$this->load->view('include/template',$data);
	}
	
	public function profile_summary(){
		
		$profile_class_id = $this->uri->segment(3);
		$as_of_date = DateTime::createFromFormat('mdY', $this->uri->segment(4));
		$as_of_date =  $as_of_date->format('m/d/Y');
		
		$data['content'] = 'profile_class_receivables_summary_view';
		$data['title'] = 'Accounts Receivable Summary';
		$data['head_title'] = 'Treasury | Receivables Summary';

		$data['results'] = $this->receivables_model->get_receivables_profile_summary(date('d-M-y', strtotime($as_of_date)), $profile_class_id);
		$data['profile_class'] = $this->receivables_model->get_profile_class_name($profile_class_id);
		$data['profile_class_id'] = $profile_class_id;
		$data['as_of_date'] = $as_of_date;
		
		$this->load->view('include/template',$data);
	}
}
