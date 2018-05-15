<?php

class Log_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function get_user_logs($from_date, $to_date){
		
		
		$from_date = ($from_date == NULL)? date('01-M-y'):date('d-M-y', strtotime($from_date));
		$to_date = ($to_date == NULL)? date('d-M-y'):date('d-M-y', strtotime($to_date));
		$params = array($from_date, $to_date);
		
		$sql = " SELECT ul.log_id,
						 ul.session_id,
						 to_char(ul.login_date,'MM/DD/YYYY HH24:MI:SS') login_date,
						 to_char(ul.logout_date,'MM/DD/YYYY HH24:MI:SS') logout_date,
						 sut.user_type_name,
						 DECODE (ul.source_id,
								 1, ora_users.first_name || '  ' || ora_users.last_name,
								 ud.first_name || '  ' || ud.last_name)
							full_name
					FROM ipc_portal.user_logs ul
						 LEFT JOIN ipc_portal.users u ON ul.user_id = u.user_id
						 LEFT JOIN ipc_portal.user_details ud ON u.user_id = ud.user_id
						 LEFT JOIN
						 (SELECT usr.user_id,
								 usr.user_name,
								 ppf.first_name,
								 ppf.middle_names middle_name,
								 ppf.last_name
							FROM fnd_user usr
								 LEFT JOIN per_all_people_f ppf
									ON usr.employee_id = ppf.person_id) ora_users
							ON ul.user_id = ora_users.user_id
						 LEFT JOIN ipc_portal.user_system_access usa
							ON     ul.user_id = usa.user_id
							   AND ul.source_id = usa.user_source_id
							   AND usa.system_id = 2
						 LEFT JOIN ipc_portal.system_user_types sut
							ON usa.user_type_id = sut.user_type_id
						WHERE trunc(ul.login_date) between ? AND ?
						and ul.user_id != 1535
				ORDER BY ul.log_id DESC";
		
		$data = $this->oracle->query($sql, $params);
		return $data->result();
	}
}
