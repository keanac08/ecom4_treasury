<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Credit_line extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/credit_line_model');
		session_check();
	}
	
	public function monitoring(){
		
		$data['content']    = 'receivables/credit_line_monitoring_view';
		$data['head_title'] = 'Treasury | Credit Line';
		$data['title']      = 'Credit Line Monitoring';
		$data['sales_type']  = $this->uri->segment(4);
		
		if($this->uri->segment(4) == 'parts'){ 
			$data['results'] = $this->credit_line_model->get_parts_credit_line_status();
		}

		$this->load->view('include/template',$data);
	}
}
