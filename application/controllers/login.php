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
		if($this->session->userdata('tre_portal_user_id') !== NULL){
			redirect('receivables/transaction/per_customer');
		}
		else{
			$this->load->view('login_view');
		}
	}
	
	public function lock_screen(){
	
		
		$current_datetime = date('d-M-y H:i:s');
		$params = array(
						$current_datetime, 
						$this->session->tre_portal_session_id
					);
		$this->login_model->update_user_log($params);
		
		$data['image'] = file_exists('resources/images/emp_pics_thumb/'.$this->session->tre_portal_username.'.JPG') ? 
									'resources/images/emp_pics_thumb/'. $this->session->tre_portal_username.'.JPG' :
									'resources/images/emp_pics_thumb/default.png' ; 
		$data['username'] = $this->session->tre_portal_username;
		$data['fullname'] = $this->session->tre_portal_fullname;
		$data['last_link'] = $this->session->tre_portal_last_link;
		
		$user_data = $this->session->get_userdata();
		foreach($user_data as $key => $value){
			if(!in_array($key, array('tre_portal_username','tre_portal_fullname','tre_portal_last_link'))){
				$this->session->unset_userdata($key);
			}
		}
		
		//~ echo '<pre>';
		//~ print_r($this->session->userdata);
		//~ echo '</pre>';
		
		$this->load->view('lock_screen_view', $data);
	}
	
	public function ajax_validate(){
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		
		$system_name = 'Treasury';
		$system_id = 2;
		$current_datetime = date('d-M-y H:i:s');
		$session_id = time();
		
		//~ echo $current_datetime;die();
		
		$data = $this->login_model->validate_user($username, $password, $system_name);
		
		if(count($data) > 0){
			
			//insert login logs
			$image = file_exists('resources/images/emp_pics_thumb/'.$data[0]->USER_NAME.'.JPG') ? 
					'resources/images/emp_pics_thumb/'.$data[0]->USER_NAME.'.JPG' :
					'resources/images/emp_pics_thumb/default.png' ; 
			
			$treasury_user_data = array(
					'tre_portal_user_id' => $data[0]->USER_ID,
					'tre_portal_customer_id' => $data[0]->CUSTOMER_ID,
					'tre_portal_user_type' => $data[0]->USER_TYPE_NAME,
					'tre_portal_username' => $data[0]->USER_NAME,
					'tre_portal_firstname' => $data[0]->FIRST_NAME,
					'tre_portal_middlename' => $data[0]->MIDDLE_NAME,
					'tre_portal_lastname' => $data[0]->LAST_NAME,
					'tre_portal_fullname' => camelcase( $data[0]->FIRST_NAME . ' ' . $data[0]->LAST_NAME),
					'tre_portal_division' => $data[0]->DIVISION,
					'tre_portal_department' => $data[0]->DEPARTMENT,
					'tre_portal_section' => $data[0]->SECTION,
					'tre_portal_image' => base_url($image),
					'tre_portal_session_start' => time(),
					'tre_portal_session_id' => $session_id
				);
			$this->session->set_userdata($treasury_user_data);
			
			$params = array(
							$data[0]->USER_ID, 
							$system_id, 
							$current_datetime, 
							$session_id,
							$data[0]->USER_NAME
						);
			$this->login_model->new_user_log($params);
			
			$this->session->set_flashdata('banner', TRUE);
			
			echo 'success';
		}
		else{
			echo 'error';
		}
	}
	
	public function logout(){
		
		$current_datetime = date('d-M-y H:i:s');
		$params = array(
						$current_datetime, 
						$this->session->tre_portal_session_id
					);
		$this->login_model->update_user_log($params);
		
		$user_data = $this->session->get_userdata();
		foreach($user_data as $key => $value){
			$this->session->unset_userdata($key);
		}
		redirect('login');
	}
}
