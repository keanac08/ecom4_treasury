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
					   msib.attribute9                                       sales_model,
					   msib.attribute8                                       body_color,
					   NVL (hold.released_flag, NVL (oola.attribute20, 'N')) released_flag,
					   msn.d_attribute20                                     tagged_date,
					   TRUNC (SYSDATE) - TRUNC (msn.d_attribute20)           aging,
					   oola.unit_selling_price                               net_amount,
					   oola.tax_value                                        vat_amount,
					   oola.unit_selling_price +  oola.tax_value amount,
					   round(oola.unit_selling_price * .01,2) wht,
					   round(( oola.unit_selling_price +  oola.tax_value )  -  (oola.unit_selling_price * .01),2) amount_due
				  FROM mtl_reservations mr
					   LEFT JOIN oe_order_lines_all oola
						  ON oola.line_id = mr.demand_source_line_id
					   LEFT JOIN oe_order_headers_all ooha ON ooha.header_id = oola.header_id
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
				 WHERE     1 = 1
					   AND msn.c_attribute30 IS NULL
					   AND mr.organization_id = 121
					   AND hcaa.cust_account_id = ?
					   AND NVL (hold.released_flag, NVL (oola.attribute20, 'N')) = 'N'
					   ".$and."
					   AND NVL (hold.order_hold_id, 1) = (SELECT NVL (MAX (order_hold_id), 1)
															FROM oe_order_holds_all
														   WHERE line_id = oola.line_id)";
																								
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
}
