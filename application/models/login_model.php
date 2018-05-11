<?php

class Login_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function validate_user($username,$password,$system_name){

		$sql = "SELECT tab.user_id,
						tab.user_name,
						tab.first_name,
						tab.middle_name,
						tab.last_name,
						tab.division,
						tab.department,
						tab.section,
						tab.customer_id,
						tab.source_id,
						ut.user_type_name
					FROM (SELECT usr.user_id,
								usr.user_name,
								ppf.first_name,
								ppf.middle_names middle_name,
								ppf.last_name,
								ppf.attribute2 division,
								ppf.attribute3 department,
								ppf.attribute4 section,
								NULL customer_id,
								1 source_id
							FROM fnd_user usr LEFT JOIN per_all_people_f ppf
								   ON usr.employee_id = ppf.person_id
								WHERE usr.user_name = ?
								   AND usr.end_date IS NULL
								   AND IPC_DECRYPT_ORA_USR_PWD(usr.encrypted_user_password) = ?
							UNION
							SELECT u.user_id,
								   u.user_name,
								   ud.first_name,
								   ud.middle_name,
								   ud.last_name,
								   ud.division,
								   ud.department,
								   ud.section,
								   u.customer_id,
								   2 source_id
							  FROM ipc_portal.users u
								   LEFT JOIN ipc_portal.user_details ud ON u.user_id = ud.user_id
							 WHERE u.status_id = 1 AND ud.status_id = 1
										 and u.user_name = ?
										 and u.passcode = ?
						) tab
					LEFT JOIN ipc_portal.user_system_access usa
						ON tab.user_id = usa.user_id
					LEFT JOIN ipc_portal.system_user_types ut
						ON usa.user_type_id = ut.user_type_id
					LEFT JOIN ipc_portal.systems sys
						ON usa.system_id = sys.system_id
				WHERE 1 = 1
					AND sys.system_name = ?";
		$params = array(
						$username,
						$password,
						$username,
						$password,
						$system_name
					);
		$data = $this->oracle->query($sql,$params);
		return $data->result();
	}
	
	public function new_user_log($params){
		
		$sql = "INSERT INTO IPC_PORTAL.USER_LOGS (
						user_id,
						system_id,
						login_date,
						session_id)
				VALUES (?,?,to_date(?, 'DD-MON-YY HH24:MI:SS'),?)";
		$this->oracle->query($sql, $params);
	}
	
	public function update_user_log($params){
		
		$sql = "UPDATE IPC_PORTAL.USER_LOGS SET logout_date = to_date(?, 'DD-MON-YY HH24:MI:SS') WHERE session_id = ?";
		$this->oracle->query($sql, $params);
	}
	
	
}
