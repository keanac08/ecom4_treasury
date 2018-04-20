<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function nvl($string){
	return ($string != NULL) ? $string:'-';
}

function isset_obj($array_name, $object){
	return (isset($array_name)) ? $object:'-';
}

function nem($string){
	return (!empty($string)) ? $string:'-';
}







