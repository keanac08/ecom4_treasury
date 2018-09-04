<?php

class Payment_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function get_vehicle_tagged($customer_id, $cs_numbers = NULL){
		
		if($cs_numbers != NULL){
			$and = " AND msn.serial_number IN (".$cs_numbers.") ";
		}
		else{
			$and = " AND msn.serial_number IS NOT NULL ";
		}
		
		$sql = "SELECT mr.reservation_id,
					   hp.party_name,
					   hcaa.cust_account_id,
					   hcaa.account_name,
					   msn.serial_number cs_number,
					    type.name order_type,
					    rt.name payment_terms,
					   msib.attribute9                                       sales_model,
					   msib.attribute8                                       body_color,
					   NVL (hold.released_flag, NVL (oola.attribute20, 'N')) released_flag,
					   msn.d_attribute20                                     tagged_date,
					   -- TRUNC (SYSDATE) - TRUNC (msn.d_attribute20)           aging,
					   (select count(*) from ipc_calendars_view where date_time_Start between TRUNC (msn.d_attribute20 + 1) and TRUNC (SYSDATE) and TRIM(DAY_OF_WEEK_DESC) not in ('Sunday','Saturday')) aging,
					   oola.unit_selling_price                               net_amount,
					   oola.tax_value                                        vat_amount,
					   oola.unit_selling_price +  oola.tax_value amount,
					   round(oola.unit_selling_price * .01,2) wht,
					   round(( oola.unit_selling_price +  oola.tax_value )  -  (oola.unit_selling_price * .01),2) amount_due,
					   	CASE TRIM(rt.name) 
							WHEN '30 Days'  THEN ROUND(((oola.unit_selling_price - (oola.unit_selling_price *     .01)) * 1.12) - ((oola.unit_selling_price - (oola.unit_selling_price *     .01)) * .01),2)
							WHEN '45 Days'  THEN ROUND(((oola.unit_selling_price - (oola.unit_selling_price * .0128)) * 1.12) - ((oola.unit_selling_price - (oola.unit_selling_price * .0128)) * .01),2)
							WHEN '60 Days'  THEN ROUND(((oola.unit_selling_price - (oola.unit_selling_price * .0156)) * 1.12) - ((oola.unit_selling_price - (oola.unit_selling_price * .0156)) * .01),2)
							ELSE 0
						END discounted_amount
				  FROM mtl_reservations mr
					   LEFT JOIN oe_order_lines_all oola
						  ON oola.line_id = mr.demand_source_line_id
					   LEFT JOIN oe_order_headers_all ooha 
						ON ooha.header_id = oola.header_id
						   LEFT JOIN ra_terms_tl rt
                          ON oola.payment_term_id = rt.term_id
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
					  --  LEFT JOIN ipc.ipc_vehicle_for_invoice for_inv
					  --    ON msn.serial_number = for_inv.cs_number
					  --    AND  hcaa.cust_account_id = for_inv.customer_id
				 WHERE     1 = 1
						-- AND for_inv.cs_number is not null
					   AND msn.c_attribute30 IS NULL
					   AND mr.organization_id = 121
					   AND hcaa.cust_account_id = ?
					   AND NVL (hold.released_flag, NVL (oola.attribute20, 'N')) = 'N'
					   ".$and."
					   AND NVL (hold.order_hold_id, 1) = (SELECT NVL (MAX (order_hold_id), 1)
															FROM oe_order_holds_all
														   WHERE line_id = oola.line_id)"; //echo $sql;
																								
		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
	
	public function get_vehicle_tagged_w_terms($customer_id, $sales_type){
		
		if($sales_type == 'vehicle'){
			$and = " AND type.name like 'VHL.%'";
		}
		else{
			$and = " AND type.name like 'FLT.%'";
		}
		
		$sql = "SELECT mr.reservation_id,
					   hp.party_name,
					   hcaa.cust_account_id,
					   hcaa.account_name,
					   msn.serial_number cs_number,
					    type.name order_type,
					    rt.name payment_terms,
					   msib.attribute9                                       sales_model,
					   msib.attribute8                                       body_color,
					   NVL (hold.released_flag, NVL (oola.attribute20, 'N')) released_flag,
					   msn.d_attribute20                                     tagged_date,
					   -- TRUNC (SYSDATE) - TRUNC (msn.d_attribute20)           aging,
					   (select count(*) from ipc_calendars_view where date_time_Start between TRUNC (msn.d_attribute20 + 1) and TRUNC (SYSDATE) and TRIM(DAY_OF_WEEK_DESC) not in ('Sunday','Saturday')) aging,
					   oola.unit_selling_price                               net_amount,
					   oola.tax_value                                        vat_amount,
					   oola.unit_selling_price +  oola.tax_value amount,
					   round(oola.unit_selling_price * .01,2) wht,
					   round(( oola.unit_selling_price +  oola.tax_value )  -  (oola.unit_selling_price * .01),2) amount_due,
					   	CASE TRIM(rt.name) 
							WHEN '30 Days'  THEN ROUND(((oola.unit_selling_price - (oola.unit_selling_price *     .01)) * 1.12) - ((oola.unit_selling_price - (oola.unit_selling_price *     .01)) * .01),2)
							WHEN '45 Days'  THEN ROUND(((oola.unit_selling_price - (oola.unit_selling_price * .0128)) * 1.12) - ((oola.unit_selling_price - (oola.unit_selling_price * .0128)) * .01),2)
							WHEN '60 Days'  THEN ROUND(((oola.unit_selling_price - (oola.unit_selling_price * .0156)) * 1.12) - ((oola.unit_selling_price - (oola.unit_selling_price * .0156)) * .01),2)
							ELSE 0
						END discounted_amount
				  FROM mtl_reservations mr
					   LEFT JOIN oe_order_lines_all oola
						  ON oola.line_id = mr.demand_source_line_id
					   LEFT JOIN oe_order_headers_all ooha 
						ON ooha.header_id = oola.header_id
						   LEFT JOIN ra_terms_tl rt
                          ON oola.payment_term_id = rt.term_id
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
					  --  LEFT JOIN ipc.ipc_vehicle_for_invoice for_inv
					  --    ON msn.serial_number = for_inv.cs_number
					  --    AND  hcaa.cust_account_id = for_inv.customer_id
				 WHERE     1 = 1
						-- AND for_inv.cs_number is not null
					   AND msn.c_attribute30 IS NULL
					   AND mr.organization_id = 121
					   AND hcaa.cust_account_id = ?
					   ".$and."
					   AND  rt.name IN ('30 Days', '45 Days', '60 Days')
					   AND NVL (hold.released_flag, NVL (oola.attribute20, 'N')) = 'N'
					   AND msn.serial_number IS NOT NULL
					   AND NVL (hold.order_hold_id, 1) = (SELECT NVL (MAX (order_hold_id), 1)
															FROM oe_order_holds_all
														   WHERE line_id = oola.line_id)
						ORDER BY aging DESC"; //echo $sql;
																								
		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
	
	public function get_parts_invoiced($customer_id, $invoices = NULL){
		
		if($invoices != NULL){
			$and = " AND soa.trx_number IN (".$invoices.") ";
		}
		else{
			$and = " ";
		}
		
		$sql = "SELECT soa.customer_id, soa.customer_trx_id,
						 hp.party_name,
						 hcaa.account_name,
						 soa.trx_number invoice_number,
						 soa.payment_term,
						 soa.due_date,
						 SUM (AMOUNT_DUE_ORIGINAL) invoice_amount,
						 SUM (AMOUNT_DUE_REMAINING) balance,
						 SUM (AMOUNT_DUE_ORIGINAL) - (SUM(AMOUNT_LINE_ITEMS_ORIGINAL) * 0.1) amount_due
					FROM ipc.ipc_invoice_details soa
						 LEFT JOIN ar_payment_schedules_all apsa
							ON soa.customer_trx_id = apsa.customer_trx_id
						  LEFT JOIN hz_cust_accounts_all hcaa
							ON soa.customer_id = hcaa.cust_account_id
						  LEFT JOIN hz_parties hp
							ON hcaa.party_id = hp.party_id
				   WHERE    1 = 1
						 and soa.customer_id = ?
						 AND soa.profile_class_id = 1042
						 AND soa.status = 'OP'
						 ".$and."
				GROUP BY soa.customer_trx_id,
						 soa.trx_number,
						 soa.customer_id, 
						 soa.due_date,
						 soa.payment_term,
						 hp.party_name,
						 hcaa.account_name
				ORDER BY soa.due_date";
																								
		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
	
	public function get_tagged_units_per_customer($customer_id, $payment_terms){
		
		if($payment_terms == 'dated'){
			$and = "AND  rt.name = '0 Day'";
		}
		else{
			$and = "AND  rt.name IN ('30 Days', '45 Days', '60 Days')";
		}
		
		$sql = "SELECT mr.reservation_id,
					   hp.party_name,
					   hcaa.cust_account_id,
					   hcaa.account_name,
					   msn.serial_number cs_number,
					   type.name order_type,
					   rt.name payment_terms,
					   msib.attribute9                                       sales_model,
					   msib.attribute8                                       body_color,
					   msn.d_attribute20                                     tagged_date,
					   for_inv.date_requested                                     for_invoice_date,
					   (select count(*) from ipc_calendars_view where date_time_Start between TRUNC (msn.d_attribute20 + 1) and TRUNC (SYSDATE) and TRIM(DAY_OF_WEEK_DESC) not in ('Sunday','Saturday')) aging,
					   oola.unit_selling_price                               net_amount,
					   oola.tax_value                                        vat_amount,
					   oola.unit_selling_price +  oola.tax_value amount,
					   round(oola.unit_selling_price * .01,2) wht,
					   round(( oola.unit_selling_price +  oola.tax_value )  -  (oola.unit_selling_price * .01),2) amount_due,
						CASE TRIM(rt.name) 
							WHEN '30 Days'  THEN ROUND(((oola.unit_selling_price - (oola.unit_selling_price *     .01)) * 1.12) - ((oola.unit_selling_price - (oola.unit_selling_price *     .01)) * .01),2)
							WHEN '45 Days'  THEN ROUND(((oola.unit_selling_price - (oola.unit_selling_price * .0128)) * 1.12) - ((oola.unit_selling_price - (oola.unit_selling_price * .0128)) * .01),2)
							WHEN '60 Days'  THEN ROUND(((oola.unit_selling_price - (oola.unit_selling_price * .0156)) * 1.12) - ((oola.unit_selling_price - (oola.unit_selling_price * .0156)) * .01),2)
							ELSE 0
						END discounted_amount
				  FROM mtl_reservations mr
					   LEFT JOIN oe_order_lines_all oola
						  ON oola.line_id = mr.demand_source_line_id
					   LEFT JOIN oe_order_headers_all ooha 
						  ON ooha.header_id = oola.header_id
					   LEFT JOIN oe_transaction_types_tl type
						  ON ooha.order_type_id = type.transaction_type_id
					   LEFT JOIN oe_order_holds_all hold
						  ON ooha.header_id = hold.header_id AND oola.line_id = hold.line_id
					    LEFT JOIN ra_terms_tl rt
                          ON oola.payment_term_id = rt.term_id
					   LEFT JOIN mtl_serial_numbers msn
						  ON mr.reservation_id = msn.reservation_id
					   LEFT JOIN mtl_system_items_b msib
						  ON msib.inventory_item_id = msn.inventory_item_id
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
						AND hcaa.cust_account_id = ? -- 15096 
						AND NVL (hold.released_flag, NVL (oola.attribute20, 'N')) = 'N'
						AND msn.serial_number IS NOT NULL
						".$and."
						AND NVL (hold.order_hold_id, 1) = (SELECT NVL (MAX (order_hold_id), 1)
															FROM oe_order_holds_all
														   WHERE line_id = oola.line_id)
						ORDER BY for_inv.date_requested, aging DESC";
						
		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
	
	public function get_tagged_for_check_payments($cs_numbers){

		$sql = "SELECT
					ooha.header_id,
					ooha.order_number,
					oola.line_number,
					ooha.ordered_date,
					--ooha.booked_flag,
					--ottt.name order_type,
					ottt.description order_type,
					hp.party_name,
					hcaa.account_name,
					ooha.flow_status_code status,
					TO_DATE ( (SYSDATE + REGEXP_REPLACE (rt.name, '[^0-9]', '')),'DD-MON-RRRR') due_date,
					--mr.reservation_id,
					msn.serial_number cs_number,
					msib.attribute9 sales_model,
					msib.attribute8 body_color,
					--oola.unit_list_price,
					oola.unit_selling_price net_amount,
					oola.tax_value vat_amount,
					oola.unit_selling_price - (oola.unit_selling_price * .01) +  oola.tax_value amount_due,
					--hold.hold_source_id hold_id,
					--hold.released_flag,
					 NVL (hold.released_flag, NVL (oola.attribute20, 'N')) released_flag
					--
					-- select oola.*
				FROM oe_order_headers_all ooha
					LEFT JOIN oe_order_lines_all oola
						ON ooha.header_id = oola.header_id
					LEFT JOIN ipc.ipc_order_return ret
						ON oola.line_id = ret.line_id
					LEFT JOIN oe_transaction_types_tl ottt
						ON ooha.order_type_id = ottt.transaction_type_id
					LEFT JOIN oe_order_holds_all hold
						ON  oola.line_id = hold.line_id
					LEFT JOIN mtl_reservations mr
						ON oola.line_id = mr.demand_source_line_id
					LEFT JOIN mtl_serial_numbers msn
						ON  mr.RESERVATION_ID = msn.RESERVATION_ID
					LEFT JOIN mtl_system_items_b msib
						ON oola.inventory_item_id = msib.inventory_item_id
						AND oola.ship_from_org_id = msib.organization_id
					LEFT JOIN ra_terms rt
						ON oola.payment_term_id = rt.term_id
					LEFT JOIN hz_cust_accounts_all hcaa
						ON ooha.sold_to_org_id = hcaa.cust_account_id
					LEFT JOIN hz_parties hp
						ON hcaa.party_id = hp.party_id
				WHERE 1 = 1
					AND ooha.ship_from_org_id = 121
					AND ooha.flow_status_code IN ('ENTERED','BOOKED')
					AND ooha.order_type_id NOT IN (1150,1151)
					AND ret.line_id IS NULL
					AND NVL (hold.released_flag, NVL (oola.attribute20, 'N')) = 'N'
					AND msn.serial_number IS NOT NULL
					AND hold.hold_release_id IS NULL
					AND msn.serial_number IN (".$cs_numbers.")
					--and ooha.order_number = '3010035319'
					ORDER BY due_date asc nulls last";
		
		$data = $this->oracle->query($sql);
		return $data->result();
	}
}
