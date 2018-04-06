<?php

class Reserved_units_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function get_tagged_per_customer($customer_id){
		
		$sql = "SELECT 
					ooha.header_id,
					ooha.order_number,
					oola.line_number,
					ooha.ordered_date,
					hcaa.account_name,
					hcaa.cust_account_id customer_id,
					mr.reservation_id,
					msn.serial_number cs_number,
					msib.attribute9 sales_model,
					msib.attribute8 body_color,
					rt.name payment_terms,
					msn.d_attribute20 reserved_date,
					 NVL(substr(ottl.description, 1, instr(ottl.description,' ')), ottl.description) order_type,
					trunc(sysdate - msn.d_attribute20) aging,
					--oola.unit_selling_price net_amount,
					--oola.tax_value vat_amount,
					oola.unit_selling_price - (oola.unit_selling_price * .01) +  oola.tax_value amount_due
				FROM oe_order_headers_all ooha
				INNER JOIN oe_order_lines_all oola
					ON ooha.header_id = oola.header_id
				INNER JOIN ra_terms_tl rt
					ON oola.payment_term_id = rt.term_id
				INNER JOIN oe_transaction_types_tl ottl
					ON ooha.order_type_id = ottl.transaction_type_id
				LEFT JOIN ipc.ipc_order_return ret
					ON oola.line_id = ret.line_id
				LEFT JOIN oe_order_holds_all hold
					ON  oola.line_id = hold.line_id
				LEFT JOIN mtl_reservations mr
					ON oola.line_id = mr.demand_source_line_id
				LEFT JOIN mtl_serial_numbers msn
					ON  mr.reservation_id = msn.reservation_id
				LEFT JOIN mtl_system_items_b msib
					ON msn.inventory_item_id = msib.inventory_item_id
					AND msn.current_organization_id = msib.organization_id
				LEFT JOIN hz_cust_accounts_all hcaa
					ON ooha.sold_to_org_id = hcaa.cust_account_id
				WHERE 1 = 1
					AND ret.line_id IS NULL
					AND NVL (hold.released_flag, NVL (oola.attribute20, 'N')) = 'N'
					AND ooha.flow_status_code IN ('ENTERED','BOOKED')
					AND ooha.order_type_id NOT IN (1150,1151)
					AND ooha.ship_from_org_id = 121
					AND ooha.sold_to_org_id = ?
				ORDER BY aging DESC, reserved_date";//echo $sql;

		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
}
