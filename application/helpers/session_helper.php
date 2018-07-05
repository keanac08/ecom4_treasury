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
		else{
			if(time()-$CI->session->userdata('tre_portal_session_start') > 600){
				if(strpos($CI->uri->uri_string(),'ajax_invoice_details')){
					$CI->session->set_userdata('tre_portal_last_link', 'receivables/transaction/per_customer');
				}
				else{
					$CI->session->set_userdata('tre_portal_last_link', $CI->uri->uri_string());
				}
				redirect('login/lock_screen');
			}
			else{
				$CI->session->tre_portal_session_start = time();
			}
		}
	}
}
