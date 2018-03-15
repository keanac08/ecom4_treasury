<?php

class Check_warehousing_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function get_tagged_units($cs_numbers){

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
					AND msn.serial_number IN (".$cs_numbers.")
					--and ooha.order_number = '3010035319'
					ORDER BY due_date asc nulls last";
		
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function get_approved_pdc($from_date, $to_date){
		
		$from_date = ($from_date == NULL)? date('01-M-y'):date('d-M-y', strtotime($from_date));
		$to_date = ($to_date == NULL)? date('d-M-y'):date('d-M-y', strtotime($to_date));
		$params = array($from_date, $to_date);
		
		$sql = " SELECT DISTINCT pdc.check_id,
							  pdc.check_number,
							  pdc.check_bank,
							  pdc.check_date,
							  pdc.check_amount,
							  app_pdc.date_approved,
							  app_pdc.date_deposit
				FROM ipc.ipc_treasury_pdc pdc
					 LEFT JOIN ipc.ipc_treasury_pdc_units unit
						ON pdc.check_id = unit.check_id
					 LEFT JOIN ipc.ipc_treasury_approved_pdc app_pdc
						ON pdc.check_id = app_pdc.check_id
			   WHERE app_pdc.check_id IS NOT NULL
			   AND pdc.check_date between ? AND ?
			ORDER BY app_pdc.date_approved DESC";
		$data = $this->oracle->query($sql, $params);
		return $data->result();
	}
	
	public function update_check_deposit_date($check_id, $deposit_date){
		
		$sql = "UPDATE ipc.ipc_treasury_approved_pdc set date_deposit = ? WHERE check_id = ?";
		$this->oracle->query($sql, array($deposit_date, $check_id));
	}
	
	public function get_last_pdc_header_id(){
		
		$sql = "SELECT ipc.ipc_pdc_sequence.currval last_id
				FROM dual";
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function new_pdc_header($params){
		$sql = "INSERT INTO ipc.ipc_treasury_pdc (
					check_number,
					check_bank,
					check_date,
					check_amount)
				VALUES (?,?,?,?)";
		$this->oracle->query($sql, $params);
	}
	
	public function new_pdc_units_header($check_id, $cs_numbers){
		$sql = "INSERT INTO ipc.ipc_treasury_pdc_units (
					check_id,
					cs_number)
					SELECT ?, serial_number 
						FROM mtl_serial_numbers 
						WHERE serial_number IN (".$cs_numbers.")";
		$this->oracle->query($sql, $check_id);
	}
	
	public function get_check_details_for_releasing($batch_id, $check_id){
		
		if($batch_id != NULL){
			$and = 'AND pdc.batch_id = ' . $batch_id;
		}
		else if ($check_id != NULL){
			$and = 'AND pdc.check_id = ' . $check_id;
		}
		
		$sql = "SELECT pdc.check_id,
						 pdc.check_number,
						 pdc.check_bank,
						 pdc.check_date,
						 pdc.check_amount,
						 msn.serial_number                                cs_number,
						 NVL (so.account_name, hca.account_name)          account_name,
						 msib.attribute9                                  sales_model,
						 so.order_type_desc                               order_type,
						 so.payment_terms,
						 so.fleet_name,
						 so.released_flag,
						 so.so_line_id,
						 rcta.trx_number,
						 TO_CHAR (
							  TO_DATE (TO_CHAR (SYSDATE, 'DD-MON-YY'))
							+ NVL (
								 SUBSTR (so.payment_terms,
										 0,
										 INSTR (so.payment_terms, ' ') - 1),
								 so.payment_terms),
							'MM/DD/YYYY')
							due_date,
						 so.unit_selling_price + so.tax_value             transaction_amount,
						 ROUND (
							(so.unit_selling_price - (so.unit_selling_price * .01)) + tax_value,
							2)
							amount_due,
						 ROUND (rctla.invoice_amount - rctla.wht_amount, 2) invoice_amount_due,
						 CASE
							WHEN rcta.trx_number IS NOT NULL
							THEN
							   'Invoiced'
							WHEN so.so_header_status = 'ENTERED'
							THEN
							   'Entered'
							WHEN     wdd.released_status = 'R'
								 AND so.so_header_status = 'BOOKED'
								 AND so.released_flag = 'N'
							THEN
							   'Booked / Credit Hold'
							WHEN     wdd.released_status = 'R'
								 AND so.so_header_status = 'BOOKED'
								 AND so.released_flag = 'Y'
							THEN
							   'Booked / Credit Hold Released'
							WHEN     wdd.released_status = 'R'
								 AND so.so_header_status = 'BOOKED'
								 AND so.released_flag IS NULL
							THEN
							   'Booked / Credit Hold Not Applied'
							WHEN wdd.released_status = 'S'
							THEN
							   'For Invoice'
							WHEN wdd.released_status = 'Y'
							THEN
							   'For Invoice'
							WHEN wdd.released_status = 'C'
							THEN
							   'For Invoice'
							ELSE
							   'Not Yet Booked'
						 END
							status
					FROM ipc.ipc_treasury_pdc pdc
						 LEFT JOIN ipc.ipc_treasury_pdc_units unit
							ON pdc.check_id = unit.check_id
						 LEFT JOIN mtl_serial_numbers msn ON unit.cs_number = msn.serial_number
						 LEFT JOIN IPC_SALES_ORDER_V so ON msn.serial_number = so.serial_number
						 LEFT JOIN mtl_system_items_b msib
							ON     msn.inventory_item_id = msib.inventory_item_id
							   AND msn.current_organization_id = msib.organization_id
						 LEFT JOIN WSH_DELIVERY_DETAILS WDD
							ON so.so_line_id = wdd.SOURCE_LINE_ID
						 LEFT JOIN wsh_delivery_assignments wda
							ON wdd.DELIVERY_DETAIL_ID = wda.DELIVERY_DETAIL_ID
						 LEFT JOIN ra_customer_trx_all rcta
							ON msn.serial_number = rcta.attribute3
						 LEFT JOIN hz_cust_accounts hca
							ON rcta.sold_to_customer_id = hca.cust_account_id
						 LEFT JOIN
						 (  SELECT customer_trx_id,
								   MAX (warehouse_id)                         warehouse_id,
								   MAX (inventory_item_id)                    inventory_item_id,
								   MAX (quantity_invoiced)                    quantity_invoiced,
								   MAX (INTERFACE_LINE_ATTRIBUTE2)            order_type,
								   SUM (LINE_RECOVERABLE)                     net_amount,
								   SUM (TAX_RECOVERABLE)                      vat_amount,
								   SUM (LINE_RECOVERABLE) + SUM (TAX_RECOVERABLE) invoice_amount,
								   SUM (LINE_RECOVERABLE) * .01               wht_amount
							  FROM ra_customer_trx_lines_all
							 WHERE line_type = 'LINE'
						  GROUP BY customer_trx_id) rctla
							ON rcta.customer_trx_id = rctla.customer_trx_id
				   WHERE 1 = 1 
					".$and."
				ORDER BY pdc.check_id, so.account_name";
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function new_release_lines($line_ids){
	
		$sql = "INSERT INTO OE_HOLD_RELEASES (hold_release_id,
						creation_date,
						created_by,
						last_update_date,
						last_updated_by,
						hold_source_id,
						release_reason_code)
					SELECT OE_HOLD_RELEASES_S.NEXTVAL,
							 SYSDATE,
							 1394,
							 SYSDATE,
							 1394,
							 HOLD_SOURCE_ID,
							 'MANUAL_RELEASE_MARGIN_HOLD' 
						FROM IPC_SALES_ORDER_V 
					  WHERE 1 = 1
					  and HOLD_SOURCE_ID is not null
					  and SO_LINE_ID IN (".$line_ids.")";
		$this->oracle->query($sql);
		
		$sql = "UPDATE OE_ORDER_HOLDS_ALL
				SET RELEASED_FLAG = 'Y',
					hold_release_id = OE_HOLD_RELEASES_S.CURRVAL,
					LAST_UPDATE_DATE = TO_CHAR(SYSDATE, 'DD-MON-YY')
				WHERE LINE_ID IN (".$line_ids.")";
		$this->oracle->query($sql);
	
		$sql = "UPDATE OE_ORDER_LINES_ALL
				SET attribute20 = 'Y',
				attribute19 = to_char(sysdate, 'MM/DD/YYYY HH24:MI:SS')
				WHERE LINE_ID IN (".$line_ids.")";
		$this->oracle->query($sql);
	}
	
	public function new_approved_pdc($check_id, $line_id, $cs_number){
	
		$sql = "INSERT INTO ipc.ipc_treasury_approved_pdc(
						check_id,
						line_id,
						cs_number)
					VALUES(?,?,?)";
		$this->oracle->query($sql, array($check_id, $line_id, $cs_number));
	}
	
	public function get_approved_check_unit_details($check_id){
	
		$sql = "SELECT
					pdc.check_id, 
					pdc.check_number,
					pdc.check_bank, 
					pdc.check_amount, 
					pdc.check_date, 
						CASE WHEN trx_number IS NULL THEN
							oola.unit_selling_price - (oola.unit_selling_price * .01) +  oola.tax_value
						ELSE
							ROUND (rctla.invoice_amount - rctla.wht_amount, 2)
						END
					amount_due,
					NVL(rcta.trx_number, '-') trx_number,
					msn.serial_number cs_number,
					msib.attribute9 sales_model,
					NVL(hcaa.account_name, hca.account_name) account_name
					FROM ipc.ipc_treasury_pdc pdc
					LEFT JOIN ipc.ipc_treasury_pdc_units pdcu
						ON pdc.check_id = pdcu.check_id
					LEFT JOIN mtl_serial_numbers msn
						ON pdcu.cs_number = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
						ON msn.inventory_item_id = msib.inventory_item_id
						AND msn.current_organization_id = msib.organization_id
					LEFT JOIN mtl_reservations mr
						ON msn.reservation_id = mr.reservation_id
					LEFT JOIN oe_order_lines_all oola
						ON oola.line_id = mr.demand_source_line_id
					LEFT JOIN  oe_order_headers_all ooha
						ON oola.header_id = ooha.header_id
					LEFT JOIN hz_cust_accounts_all hcaa
						ON ooha.sold_to_org_id = hcaa.cust_account_id
					LEFT JOIN ra_customer_trx_all rcta
						ON msn.serial_number = rcta.attribute3
					LEFT JOIN
					 ( SELECT customer_trx_id,
							   MAX (warehouse_id)                         warehouse_id,
							   MAX (inventory_item_id)                    inventory_item_id,
							   MAX (quantity_invoiced)                    quantity_invoiced,
							   MAX (INTERFACE_LINE_ATTRIBUTE2)            order_type,
							   SUM (LINE_RECOVERABLE)                     net_amount,
							   SUM (TAX_RECOVERABLE)                      vat_amount,
							   SUM (LINE_RECOVERABLE) + SUM (TAX_RECOVERABLE) invoice_amount,
							   SUM (LINE_RECOVERABLE) * .01               wht_amount
						  FROM ra_customer_trx_lines_all
						 WHERE line_type = 'LINE'
					  GROUP BY customer_trx_id) rctla
						ON rcta.customer_trx_id = rctla.customer_trx_id
					LEFT JOIN hz_cust_accounts hca
						ON rcta.sold_to_customer_id = hca.cust_account_id
					WHERE pdc.check_id = ?";
		$data = $this->oracle->query($sql, $check_id);
		return $data->result();
	
	}
	
	public function get_pdc_details($check_id){
		
		$sql = "SELECT pdc.check_id,
						 pdc.check_number,
						 pdc.check_bank,
						 pdc.check_date,
						 pdc.check_amount,
						 msn.serial_number                                cs_number,
						 NVL (so.account_name, hca.account_name)          account_name,
						 msib.attribute9                                  sales_model,
						 so.order_type_desc                               order_type,
						 so.payment_terms,
						 so.fleet_name,
						 so.released_flag,
						 rcta.trx_number,
						 TO_CHAR (
							  TO_DATE (TO_CHAR (SYSDATE, 'DD-MON-YY'))
							+ NVL (
								 SUBSTR (so.payment_terms,
										 0,
										 INSTR (so.payment_terms, ' ') - 1),
								 so.payment_terms),
							'MM/DD/YYYY')
							due_date,
						 so.unit_selling_price + so.tax_value             transaction_amount,
						 ROUND (
							(so.unit_selling_price - (so.unit_selling_price * .01)) + tax_value,
							2)
							amount_due,
						 ROUND (rctla.invoice_amount - rctla.wht_amount, 2) invoice_amount_due,
						 CASE
							WHEN rcta.trx_number IS NOT NULL
							THEN
							   'Invoiced'
							WHEN so.so_header_status = 'ENTERED'
							THEN
							   'Entered'
							WHEN     wdd.released_status = 'R'
								 AND so.so_header_status = 'BOOKED'
								 AND so.released_flag = 'N'
							THEN
							   'Booked / Credit Hold'
							WHEN     wdd.released_status = 'R'
								 AND so.so_header_status = 'BOOKED'
								 AND so.released_flag = 'Y'
							THEN
							   'Booked / Credit Hold Released'
							WHEN     wdd.released_status = 'R'
								 AND so.so_header_status = 'BOOKED'
								 AND so.released_flag IS NULL
							THEN
							   'Booked / Credit Hold Not Applied'
							WHEN wdd.released_status = 'S'
							THEN
							   'For Invoice'
							WHEN wdd.released_status = 'Y'
							THEN
							   'For Invoice'
							WHEN wdd.released_status = 'C'
							THEN
							   'For Invoice'
							ELSE
							   'Not Yet Booked'
						 END
							status
					FROM ipc.ipc_treasury_pdc pdc
						 LEFT JOIN ipc.ipc_treasury_pdc_units unit
							ON pdc.check_id = unit.check_id
						 LEFT JOIN mtl_serial_numbers msn ON unit.cs_number = msn.serial_number
						 LEFT JOIN IPC_SALES_ORDER_V so ON msn.serial_number = so.serial_number
						 LEFT JOIN mtl_system_items_b msib
							ON     msn.inventory_item_id = msib.inventory_item_id
							   AND msn.current_organization_id = msib.organization_id
						 LEFT JOIN WSH_DELIVERY_DETAILS WDD
							ON so.so_line_id = wdd.SOURCE_LINE_ID
						 LEFT JOIN wsh_delivery_assignments wda
							ON wdd.DELIVERY_DETAIL_ID = wda.DELIVERY_DETAIL_ID
						 LEFT JOIN ra_customer_trx_all rcta
							ON msn.serial_number = rcta.attribute3
						 LEFT JOIN hz_cust_accounts hca
							ON rcta.sold_to_customer_id = hca.cust_account_id
						 LEFT JOIN
						 (  SELECT customer_trx_id,
								   MAX (warehouse_id)                         warehouse_id,
								   MAX (inventory_item_id)                    inventory_item_id,
								   MAX (quantity_invoiced)                    quantity_invoiced,
								   MAX (INTERFACE_LINE_ATTRIBUTE2)            order_type,
								   SUM (LINE_RECOVERABLE)                     net_amount,
								   SUM (TAX_RECOVERABLE)                      vat_amount,
								   SUM (LINE_RECOVERABLE) + SUM (TAX_RECOVERABLE) invoice_amount,
								   SUM (LINE_RECOVERABLE) * .01               wht_amount
							  FROM ra_customer_trx_lines_all
							 WHERE line_type = 'LINE'
						  GROUP BY customer_trx_id) rctla
							ON rcta.customer_trx_id = rctla.customer_trx_id
				   WHERE 1 = 1 
					AND pdc.check_id = ?
				   and msn.current_organization_id = 121
				ORDER BY pdc.check_id, so.account_name";
		$data = $this->oracle->query($sql, $check_id);
		return $data->result();
	}
	
	public function get_unit_check_details($q){
	
		$sql = "SELECT
					pdc.check_id, 
					pdc.check_number,
					pdc.check_bank, 
					pdc.check_amount, 
					pdc.check_date, 
						CASE WHEN trx_number IS NULL THEN
							oola.unit_selling_price - (oola.unit_selling_price * .01) +  oola.tax_value
						ELSE
							ROUND (rctla.invoice_amount - rctla.wht_amount, 2)
						END
					amount_due,
					NVL(rcta.trx_number, '-') trx_number,
					msn.serial_number cs_number,
					msib.attribute9 sales_model,
					NVL(hcaa.account_name, hca.account_name) account_name
					FROM ipc.ipc_treasury_pdc pdc
					LEFT JOIN ipc.ipc_treasury_pdc_units pdcu
						ON pdc.check_id = pdcu.check_id
					LEFT JOIN mtl_serial_numbers msn
						ON pdcu.cs_number = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
						ON msn.inventory_item_id = msib.inventory_item_id
						AND msn.current_organization_id = msib.organization_id
					LEFT JOIN mtl_reservations mr
						ON msn.reservation_id = mr.reservation_id
					LEFT JOIN oe_order_lines_all oola
						ON oola.line_id = mr.demand_source_line_id
					LEFT JOIN  oe_order_headers_all ooha
						ON oola.header_id = ooha.header_id
					LEFT JOIN hz_cust_accounts_all hcaa
						ON ooha.sold_to_org_id = hcaa.cust_account_id
					LEFT JOIN ra_customer_trx_all rcta
						ON msn.serial_number = rcta.attribute3
					LEFT JOIN
					 ( SELECT customer_trx_id,
							   MAX (warehouse_id)                         warehouse_id,
							   MAX (inventory_item_id)                    inventory_item_id,
							   MAX (quantity_invoiced)                    quantity_invoiced,
							   MAX (INTERFACE_LINE_ATTRIBUTE2)            order_type,
							   SUM (LINE_RECOVERABLE)                     net_amount,
							   SUM (TAX_RECOVERABLE)                      vat_amount,
							   SUM (LINE_RECOVERABLE) + SUM (TAX_RECOVERABLE) invoice_amount,
							   SUM (LINE_RECOVERABLE) * .01               wht_amount
						  FROM ra_customer_trx_lines_all
						 WHERE line_type = 'LINE'
					  GROUP BY customer_trx_id) rctla
						ON rcta.customer_trx_id = rctla.customer_trx_id
					LEFT JOIN hz_cust_accounts hca
						ON rcta.sold_to_customer_id = hca.cust_account_id
					WHERE to_char(pdcu.cs_number) = ? OR to_char(pdc.check_id) = ? OR to_char(pdc.check_number) = ?";
		$data = $this->oracle->query($sql, array($q,$q,$q));
		return $data->result();
	
	}
	
	public function get_unit_check_details_excel($from, $to){
	
		$sql = "SELECT DISTINCT pdc.check_id,
						pdc.check_number,
						pdc.check_bank,
						to_char(pdc.check_date,'YYYY-MM-DD') check_date,
						pdc.check_amount,
						to_char(apdc.date_approved,'YYYY-MM-DD') date_approved
		  FROM ipc.ipc_treasury_approved_pdc apdc
			   LEFT JOIN ipc.ipc_treasury_pdc pdc ON apdc.check_id = pdc.check_id
		 WHERE pdc.check_date BETWEEN ? AND ?";
		$data = $this->oracle->query($sql, array($from, $to));
		return $data->result_array();
	
	}
}
