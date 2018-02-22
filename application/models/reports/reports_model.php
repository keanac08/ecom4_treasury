<?php

class Reports_model extends CI_Model {
	
	public function __construct(){
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_reports_per_user($user_type){
		
		$user_type = $user_type == 'Administrator' ? NULL : $user_type;
		
		$sql = "SELECT vr.report_id, vr.name, vr.link, vr.type
				FROM ipc.ipc_treasury_reports vr
				LEFT JOIN ipc.ipc_treasury_report_access vra
				ON vr.report_id = vra.report_id
				WHERE 1 = 1
				AND vra.user_type = NVL(?, vra.user_type)
				GROUP BY vr.report_id, vr.name, vr.link, vr.type
				ORDER BY name";

		$data = $this->oracle->query($sql, $user_type);
		return $data->result();
	}
}
