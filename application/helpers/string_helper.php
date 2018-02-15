<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function CAMELCASE($string){
	return UCWORDS(STRTOLOWER($string));
}
