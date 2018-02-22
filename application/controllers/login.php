<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->model('login_model');
		$this->load->helper('string_helper');
		//~ session_check();
	}
	
	public function index(){
		$this->load->view('login_view');
	}
	
	public function ajax_validate(){
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$system_name = 'Treasury';
		
		$data = $this->login_model->validate_user($username, $password, $system_name);
		
		if(count($data) > 0){
			
			$image = file_exists('resources/images/emp_pics_thumb/'.$data[0]->USER_NAME.'.JPG') ? 
					'resources/images/emp_pics_thumb/'.$data[0]->USER_NAME.'.JPG' :
					'resources/images/emp_pics_thumb/default.png' ; 
			
			$treasury_user_data = array(
					'tre_portal_user_id' => $data[0]->USER_ID,
					'tre_portal_user_type' => $data[0]->USER_TYPE_NAME,
					'tre_portal_username' => $data[0]->USER_NAME,
					'tre_portal_firstname' => $data[0]->FIRST_NAME,
					'tre_portal_middlename' => $data[0]->MIDDLE_NAME,
					'tre_portal_lastname' => $data[0]->LAST_NAME,
					'tre_portal_fullname' => camelcase( $data[0]->FIRST_NAME . ' ' . $data[0]->LAST_NAME),
					'tre_portal_division' => $data[0]->DIVISION,
					'tre_portal_department' => $data[0]->DEPARTMENT,
					'tre_portal_section' => $data[0]->SECTION,
					'tre_portal_image' => base_url($image)
				);
			$this->session->set_userdata($treasury_user_data);
			echo 'success';
		}
		else{
			echo 'error';
		}
	}
	
	public function logout(){
		
		$user_data = $this->session->get_userdata();
		foreach($user_data as $key => $value){
			$this->session->unset_userdata($key);
		}
		redirect('login');
	}
}
