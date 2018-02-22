<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('reports/reports_model');
		session_check();
	}
	
	public function index(){
		
		//~ echo $this->session->tre_portal_user_id;die('--');
		
		$data['content'] = 'reports/reports_dashboard_view';
		$data['title'] = 'Reports Dashboard';
		$data['head_title'] = 'Treasury | Reports Dashboard';

		$data['result'] = $this->reports_model->get_reports_per_user($this->session->tre_portal_user_type);
		
		$this->load->view('include/template',$data);
	}
}
