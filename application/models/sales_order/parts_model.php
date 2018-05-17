<?php

class Parts_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function get_parts_so_status($customer_id, $from_date, $to_date){
		
		$from_date = ($from_date == NULL)? date('01-M-y'):date('d-M-y', strtotime($from_date));
		$to_date = ($to_date == NULL)? date('d-M-y'):date('d-M-y', strtotime($to_date));
		$params = array($customer_id, $from_date, $to_date);
		
		//~ print_r($params);die();

		$sql = "SELECT DISTINCT CASE WHEN hcaa.account_name IS NULL
								THEN hp.party_name
								ELSE hcaa.account_name
								END customer_name,
								hcaa.account_name,
								ppf.first_name || ' ' || ppf.last_name prepared_by,
								ooha.cust_po_number,
								ooha.order_number,
								mtrh.request_number picklist_number,
								wnd.delivery_id dr_Reference,
								wdd.attribute2 dr_number,
								rcta.trx_number invoice_number,
								wnd.confirm_date,
								wdd.released_status
				  FROM oe_order_headers_all ooha
					   LEFT JOIN oe_order_lines_all oola ON ooha.header_id = oola.header_id
					   LEFT JOIN wsh_delivery_details wdd
						  ON oola.line_id = wdd.source_line_id
					   LEFT JOIN mtl_txn_request_lines mtrl
						  ON wdd.move_order_line_id = mtrl.line_id
					   LEFT JOIN mtl_txn_request_headers mtrh
						  ON mtrl.header_id = mtrh.header_id
					   LEFT JOIN wsh_delivery_assignments wda
						  ON wdd.delivery_detail_id = wda.delivery_detail_id
					   LEFT JOIN wsh_new_deliveries wnd ON wda.delivery_id = wnd.delivery_id
					   LEFT JOIN ra_customer_trx_all rcta
						  ON     TO_CHAR (wnd.delivery_id) = rcta.interface_header_attribute3
							 AND TO_CHAR (ooha.order_number) =
									rcta.interface_header_attribute1
					   LEFT JOIN fnd_user fu ON ooha.created_by = fu.user_id
					   LEFT JOIN per_people_f ppf
						  ON     fu.employee_id = ppf.person_id
							 AND fu.person_party_id = ppf.party_id
					   LEFT JOIN hz_cust_accounts_all hcaa
						  ON ooha.sold_to_org_id = hcaa.cust_account_id
					   LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
				 WHERE     1 = 1
					   AND ooha.ship_from_org_id = 102
				       AND ooha.sold_to_org_id = ?
					   AND wdd.released_status in ('R', 'Y','C')
				       AND TRUNC(ooha.ordered_date) between ? AND ?
				       -- AND ooha.order_number IN ('3010035407')";
		
		$data = $this->oracle->query($sql, $params);
		return $data->result();
	}
}
