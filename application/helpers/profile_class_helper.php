<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_sales_type($profile_class_id){
	
	if(in_array($profile_class_id, array(1042,1044,1046,1049))){
		$sales_type = 'parts';
	}
	else if(in_array($profile_class_id, array(1043))){
		$sales_type = 'vehicle';
	}
	else if(in_array($profile_class_id, array(1040, 1045))){
		$sales_type = 'fleet';
	}
	else if(in_array($profile_class_id, array(1041, 1048))){
		$sales_type = 'others';
	}
	else if(in_array($profile_class_id, array(1050))){
		$sales_type = 'powertrain';
	}
	else if(in_array($profile_class_id, array(1047))){
		$sales_type = 'employee';
	}
	else{
		$sales_type = '';
	}
	
	return $sales_type;
}

function get_profile_class_group($profile_class_id){
	
	if(in_array($profile_class_id, array(1042,1044,1046,1049))){
		$profile_class_group = array(1042,1044,1046,1049);
	}
	else if(in_array($profile_class_id, array(1043))){
		$profile_class_group = array(1043);
	}
	else if(in_array($profile_class_id, array(1040, 1045))){
		$profile_class_group = array(1040, 1045);
	}
	else if(in_array($profile_class_id, array(1041, 1048))){
		$profile_class_group = array(1041, 1048);
	}
	else if(in_array($profile_class_id, array(1050))){
		$profile_class_group = array(1050);
	}
	else if(in_array($profile_class_id, array(1047))){
		$profile_class_group = array(1047);
	}
	else{
		$profile_class_group = '';
	}
	
	return $profile_class_group;
}

function get_profile_class_ids($sales_type){
	
	switch ($sales_type){
		case 'parts':
			$profile_class_id = '1042,1044,1046,1049';
			break;
		case 'vehicle-fleet':
			$profile_class_id = '1043,1040,1045';
			break;
		case 'vehicle':
			$profile_class_id = '1043';
			break;
		case 'fleet':
			$profile_class_id = '1040,1045';
			break;
		case 'others':
			$profile_class_id = '1041,1048';
			break;
		case 'powertrain':
			$profile_class_id = '1050';
			break;
		case 'employee':
			$profile_class_id = '1047';
			break;
		 default:
			$profile_class_id = 0;
	} 

	return $profile_class_id;
}

function get_user_access($user_type){
	
	switch ($user_type){
		case 'IPC Parts':
		case 'Dealer Parts':
			$profile_class_id = '1042,1044,1046,1049,1050';
			break;
		case 'IPC Vehicle':
			$profile_class_id = '1043';
			break;
		case 'IPC Fleet':
			$profile_class_id = '1040,1045';
			break;
		case 'IPC Vehicle-Fleet':
		case 'Dealer Vehicle':
			$profile_class_id = '1043,1040,1045';
			break;
		default:
			$profile_class_id = 0;
	} 

	return $profile_class_id;
}







