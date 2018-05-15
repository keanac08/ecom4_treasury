<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function short_date($date){
	return ($date != NULL) ? date('m/d/Y', strtotime($date)) : '-';
}

function long_date($date){
	return ($date != NULL) ? date('m/d/Y g:i a', strtotime($date)) : '-';
}

function excel_date($date){
	return ($date != NULL) ? date('Y-m-d', strtotime($date)) : NULL;
}

function oracle_date($date){
	return ($date != NULL) ? date('d-M-y', strtotime($date)) : NULL;
}








