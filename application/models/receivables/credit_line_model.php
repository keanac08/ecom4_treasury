<?php

class Credit_line_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_parts_credit_line_status(){

		$sql = "SELECT 
					CASE
					  WHEN ACCOUNT_NAME IS NOT NULL
					  THEN
						 CUSTOMER_NAME || ' - ' || ACCOUNT_NAME
					  ELSE
						CUSTOMER_NAME
				   END
					  customer_name,
				   PROFILE_CLASS,
				   TERM,
				   CREDIT_LIMIT,
				   EXPOSURE_AR_BALANCE_TOTAL,
				   EXPOSURE_OPEN_SO_ONHOLD + EXPOSURE_OPEN_SO_NOT_ONHOLD EXPOSURE_OPEN_SO,
				   TOTAL_EXPOSURE,
				   AVAILABLE_CREDIT_LIMIT
				FROM IPC_PARTS_CREDIT_MONITORING_V
				ORDER BY AVAILABLE_CREDIT_LIMIT ASC";
		
		$data = $this->oracle->query($sql);
		return $data->result();
	}
}
