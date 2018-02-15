<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('session_check')){
	function session_check(){
		// Get current CodeIgniter instance
		$CI =& get_instance();
		// We need to use $CI->session instead of $this->session
		$user_data =  $CI->session->userdata('tre_portal_user_id');
		if (!isset($user_data)){ 
			$CI->session->set_flashdata('login_error', 1);
			redirect('login');
		}
	}
}
