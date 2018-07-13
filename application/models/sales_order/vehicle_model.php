<?php

class Vehicle_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function new_request_for_invoice($params){
		$sql = "INSERT INTO ipc.ipc_vehicle_for_invoice (
					customer_id,
					cs_number,
					date_requested)
				VALUES (?,?,?)";
		$this->oracle->query($sql, $params);
	}
	
	public function get_tagged($customer_id){
		
		$sql = "SELECT mr.reservation_id,
					   hp.party_name,
					   hcaa.cust_account_id,
					   hcaa.account_name,
					   msn.serial_number cs_number,
					   type.name order_type,
					   msn.attribute2                            chassis_number,
					   msib.attribute9                                       sales_model,
					   msib.attribute8                                       body_color,
					    msib.attribute11 || ' / ' || msn.attribute3 engine,
					   msib.attribute19 || ' / ' || msn.attribute7 aircon,
					   msib.attribute20 || ' / ' || msn.attribute9 stereo,
					   msn.attribute6                            key_no,
					   NVL (hold.released_flag, NVL (oola.attribute20, 'N')) released_flag,
					   msn.d_attribute20                                     tagged_date,
					   for_inv.date_requested                                     for_invoice_date,
					   (select count(*) from ipc_calendars_view where date_time_Start between TRUNC (msn.d_attribute20 + 1) and TRUNC (SYSDATE) and TRIM(DAY_OF_WEEK_DESC) not in ('Sunday','Saturday')) aging,
					   oola.unit_selling_price                               net_amount,
					   oola.tax_value                                        vat_amount,
					   oola.unit_selling_price +  oola.tax_value amount,
					   round(oola.unit_selling_price * .01,2) wht,
					   round(( oola.unit_selling_price +  oola.tax_value )  -  (oola.unit_selling_price * .01),2) amount_due
				  FROM mtl_reservations mr
					   LEFT JOIN oe_order_lines_all oola
						  ON oola.line_id = mr.demand_source_line_id
					   LEFT JOIN oe_order_headers_all ooha 
						  ON ooha.header_id = oola.header_id
					   LEFT JOIN oe_transaction_types_tl type
						  ON ooha.order_type_id = type.transaction_type_id
					   LEFT JOIN oe_order_holds_all hold
						  ON ooha.header_id = hold.header_id AND oola.line_id = hold.line_id
					   LEFT JOIN mtl_serial_numbers msn
						  ON mr.reservation_id = msn.reservation_id
					   LEFT JOIN mtl_system_items_b msib
						  ON     msib.inventory_item_id = msn.inventory_item_id
							 AND msib.organization_id = msn.current_organization_id
					   LEFT JOIN hz_cust_accounts_all hcaa
						  ON hcaa.cust_account_id = ooha.sold_to_org_id
					   LEFT JOIN hz_parties hp ON hp.party_id = hcaa.party_id
					   LEFT JOIN ipc.ipc_vehicle_for_invoice for_inv
					      ON msn.serial_number = for_inv.cs_number
					      AND  hcaa.cust_account_id = for_inv.customer_id
				 WHERE     1 = 1
					   AND msn.c_attribute30 IS NULL
					   AND mr.organization_id = 121
					   AND hcaa.cust_account_id = ?
					   AND NVL (hold.released_flag, NVL (oola.attribute20, 'N')) = 'N'
					   AND msn.serial_number IS NOT NULL
					   AND NVL (hold.order_hold_id, 1) = (SELECT NVL (MAX (order_hold_id), 1)
															FROM oe_order_holds_all
														   WHERE line_id = oola.line_id)
						ORDER BY for_inv.date_requested, aging DESC";
		
		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
	
	
}
