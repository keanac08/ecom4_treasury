<?php

class Soa_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_soa_per_customer($customer_id, $profile_class_ids, $as_of_date){
		
		$and = ' AND soa.profile_class_id IN ('.$profile_class_ids.')';
		
		$sql = "SELECT soa.customer_trx_id        invoice_id,
						MAX (soa.trx_number)       invoice_no,
						MAX (soa.trx_date)         invoice_date,
						MAX (soa.cs_number)       cs_number,
						MAX (soa.profile_class_id) profile_class_id,
						MAX (soa.payment_term)     payment_term,
						MAX (soa.delivery_date)    delivery_date,
						MAX (ooha.attribute3) fleet_name,
						MAX (ooha.cust_po_number) cust_po_number,
						due_date,
						CASE
							WHEN due_date IS NOT NULL AND due_date < TO_DATE ('".$as_of_date."')
								THEN
									TO_DATE ('".$as_of_date."') - due_date
								ELSE
									0
							END
						days_overdue,
						SUM (soa.invoice_orig_amount) transaction_amount,
						SUM (soa.wht_orig_amount)       wht_amount,
						SUM ((soa.invoice_orig_amount + NVL(adj.adjustment_amount,0)) - NVL (araa.paid_amount, 0)) balance
					 FROM IPC.IPC_INVOICE_DETAILS soa
					 LEFT JOIN ra_customer_trx_all rcta
						ON soa.customer_trx_id = rcta.customer_trx_id
					 LEFT JOIN oe_order_headers_all ooha
						ON rcta.interface_header_attribute1 = ooha.order_number 
					 LEFT JOIN
						(SELECT applied_customer_trx_id,
										applied_payment_schedule_id,
										SUM (amount_applied) paid_amount
							FROM ar_receivable_applications_all
								WHERE display = 'Y'
								AND gl_date <= '".$as_of_date."'
							GROUP BY applied_customer_trx_id, applied_payment_schedule_id) araa
						ON soa.customer_trx_id = araa.applied_customer_trx_id
							AND soa.payment_schedule_id = araa.applied_payment_schedule_id
					 LEFT JOIN
						(SELECT customer_trx_id,
								payment_schedule_id,
								MAX (apply_date) apply_date,
								SUM (amount) adjustment_amount
							FROM AR_ADJUSTMENTS_ALL
								WHERE 1 = 1
								AND gl_date <= '".$as_of_date."'
							GROUP BY payment_schedule_id, customer_trx_id) adj
						ON soa.customer_trx_id = adj.customer_trx_id
							AND soa.payment_schedule_id = adj.payment_schedule_id
					 WHERE     1 = 1
						 AND soa.trx_date <= '".$as_of_date."'
						 AND soa.customer_id = ?
						 ".$and."
						  AND (soa.invoice_orig_amount +NVL(adj.adjustment_amount,0)) - NVL (araa.paid_amount, 0)  > 1
					 GROUP BY ROLLUP (soa.due_date, soa.customer_trx_id)
					 ORDER BY due_Date nulls last";
		
		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
	
	public function get_soa_per_customer_excel($customer_id, $profile_class_ids, $as_of_date){
		
		$and = ' AND soa.profile_class_id IN ('.$profile_class_ids.')';
		
		$sql = "SELECT hcaa.cust_account_id account_number,
					   hp.party_name       customer_name,
					   hcaa.account_name   account_name,
					   hcpc.name           profile_class,
					   ooha.attribute3 fleet_name,
					   ooha.cust_po_number,
					   soa.customer_trx_id transaction_id,
					   soa.trx_number      transaction_number,
					   soa.trx_date        transaction_date,
					   soa.cs_number,
					   soa.payment_term,
					   soa.delivery_date,
					   soa.due_date,
					    CASE
							  WHEN due_Date IS NOT NULL AND due_Date < TO_DATE ('".$as_of_date."')
							  THEN
								 TO_DATE ('".$as_of_date."') - due_date
							  ELSE
								 0
						   END
							  days_overdue,
						 soa.invoice_orig_amount transaction_amount,
						 soa.wht_orig_amount      wht_amount,
						(soa.invoice_orig_amount +NVL(adj.adjustment_amount,0) - NVL (araa.paid_amount, 0)) balance,
						soa.invoice_currency_code currency,
						soa.exchange_rate
					FROM IPC.IPC_INVOICE_DETAILS soa
					 LEFT JOIN ra_customer_trx_all rcta
						ON soa.customer_trx_id = rcta.customer_trx_id
					 LEFT JOIN oe_order_headers_all ooha
						ON rcta.interface_header_attribute1 = to_char(ooha.order_number) 
					 LEFT JOIN
						(SELECT applied_customer_trx_id,
										applied_payment_schedule_id,
										SUM (amount_applied) paid_amount
							FROM ar_receivable_applications_all
								WHERE display = 'Y'
								AND gl_date <= '".$as_of_date."'
							GROUP BY applied_customer_trx_id, applied_payment_schedule_id) araa
						ON soa.customer_trx_id = araa.applied_customer_trx_id
							AND soa.payment_schedule_id = araa.applied_payment_schedule_id
					 LEFT JOIN
						(SELECT customer_trx_id,
								payment_schedule_id,
								MAX (apply_date) apply_date,
								SUM (amount) adjustment_amount
							FROM AR_ADJUSTMENTS_ALL
								WHERE 1 = 1
								AND gl_date <= '".$as_of_date."'
							GROUP BY payment_schedule_id, customer_trx_id) adj
						ON soa.customer_trx_id = adj.customer_trx_id
							AND soa.payment_schedule_id = adj.payment_schedule_id
							LEFT JOIN hz_cust_accounts_all hcaa
						  ON soa.customer_id = hcaa.cust_account_id
					   LEFT JOIN hz_customer_profiles hzp
						  ON     hcaa.cust_account_id = hzp.cust_account_id
							 AND soa.customer_site_use_id = hzp.site_use_id
					   LEFT JOIN hz_cust_profile_classes hcpc
						  ON hzp.profile_class_id = hcpc.profile_class_id
					   LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
					 WHERE     1 = 1
						AND soa.trx_date <= '".$as_of_date."'
						AND soa.customer_id = ?
						".$and."
						AND (soa.invoice_orig_amount +NVL(adj.adjustment_amount,0)) - NVL (araa.paid_amount, 0)  > 1
					 ORDER BY due_Date nulls last"; 
		
		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
	
	public function get_soa_per_customer_summary($customer_id, $profile_class_ids, $as_of_date){
		
		$and = ' AND soa.profile_class_id IN ('.$profile_class_ids.')';
		
		$sql = "SELECT 
					SUM (CASE WHEN days_overdue > 0 THEN (invoice_amount + adjustment_amount -paid_amount) ELSE 0 END) pastdue_receivables,
					SUM (CASE WHEN days_overdue = 0 AND delivery_date IS NOT NULL THEN (invoice_amount + adjustment_amount -paid_amount) ELSE 0 END) current_receivables,
					SUM (CASE WHEN  delivery_date IS NULL THEN (invoice_amount + adjustment_amount -paid_amount) ELSE 0 END) contingent_receivables,
					SUM(invoice_amount + adjustment_amount -paid_amount) total_receivables
				FROM (
					SELECT
						CASE
							WHEN due_date IS NOT NULL AND due_date < TO_DATE ('".$as_of_date."')
								THEN
									TO_DATE ('".$as_of_date."') - due_date
								ELSE
									0
							END
						days_overdue,
						soa.invoice_amount,
						soa.delivery_date,
						NVL(adj.adjustment_amount,0) adjustment_amount,
						NVL (araa.paid_amount, 0) paid_amount
					FROM IPC.IPC_INVOICE_DETAILS soa
						LEFT JOIN ra_customer_trx_all rcta
							ON soa.customer_trx_id = rcta.customer_trx_id
						LEFT JOIN oe_order_headers_all ooha
							ON rcta.interface_header_attribute1 = ooha.order_number
						LEFT JOIN
							(SELECT applied_customer_trx_id,
									applied_payment_schedule_id,
									SUM (amount_applied) paid_amount
								FROM ar_receivable_applications_all
									WHERE display = 'Y'
										AND gl_date <= '".$as_of_date."'
								GROUP BY applied_customer_trx_id, applied_payment_schedule_id) araa
							ON soa.customer_trx_id = araa.applied_customer_trx_id
							AND soa.payment_schedule_id = araa.applied_payment_schedule_id
						LEFT JOIN
							(SELECT customer_trx_id,
									payment_schedule_id,
									MAX (apply_date) apply_date,
									SUM (amount) adjustment_amount
								FROM AR_ADJUSTMENTS_ALL
									WHERE 1 = 1
									AND gl_date <= '".$as_of_date."'
								GROUP BY payment_schedule_id, customer_trx_id) adj
							ON soa.customer_trx_id = adj.customer_trx_id
							AND soa.payment_schedule_id = adj.payment_schedule_id
						WHERE     1 = 1
							AND soa.trx_date <= '".$as_of_date."'
							AND soa.customer_id = ?
							".$and."
							AND (soa.invoice_amount +NVL(adj.adjustment_amount,0)) - NVL (araa.paid_amount, 0)  > 1)";
		
		$data = $this->oracle->query($sql, $customer_id);
		return $data->result();
	}
	
	public function get_customer_details($customer_id, $profile_class_ids){
		
		$and = ' AND hcpc.profile_class_id IN ('.$profile_class_ids.')';
		
		$sql = "SELECT DISTINCT
						hca.cust_account_id customer_id,
						CASE
							WHEN hca.account_name IS NOT NULL THEN
								hp.party_name || ' - ' || hca.account_name
							ELSE
								hp.party_name
						END
						  customer_name,
						 hca.cust_account_id customer_id,
						 hca.account_number,
						 hca.account_name,
						 hp.party_name,
						 hcpc.profile_class_id,
						 hcpc.name         profile_class,
						 CASE hp.address1
							WHEN 'DEALERS-PARTS'
							THEN
								  hp.address2
							   || ' '
							   || hp.address3
							   || ' '
							   || hp.address4
							   || ' '
							   || hp.city
							ELSE
								  hp.address1
							   || ' '
							   || hp.address2
							   || ' '
							   || hp.address3
							   || ' '
							   || hp.address4
							   || ' '
							   || hp.city
						 END
							address,
							hcpc.profile_class_id
					FROM ar_payment_schedules_all aps
						 LEFT JOIN hz_cust_accounts hca
							ON aps.customer_id = hca.cust_account_id
						 LEFT JOIN hz_customer_profiles hzp
							ON     hca.cust_account_id = hzp.cust_account_id
							   AND aps.customer_site_use_id = hzp.site_use_id
						 LEFT JOIN hz_cust_profile_classes hcpc
							ON hzp.profile_class_id = hcpc.profile_class_id
						 LEFT JOIN hz_parties hp ON hca.party_id = hp.party_id
				   WHERE     1 = 1
						 AND aps.class IN ('INV', 'DM')
						 AND aps.status = 'OP'
						 AND hca.cust_account_id = ?
						 ".$and."
				ORDER BY account_name, party_name";
		
		$rows = $this->oracle->query($sql,$customer_id);
		if($rows->num_rows() > 0){
			$data = $rows->result();
			return $data[0];
		}
		else {
			return NULL;
		}
	}
	
	public function get_customers_per_profile($profile_class_ids, $customer_name){
		
		$and = ' AND hcpc.profile_class_id IN ('.$profile_class_ids.')';
		
		$sql = "SELECT  distinct hcaa.cust_account_id customer_id,
						CASE
						  WHEN hcaa.account_name IS NOT NULL
						  THEN
							 hp.party_name || ' - ' || hcaa.account_name
						  ELSE
							 hp.party_name
					   END
						  customer_name
				  FROM ar_payment_schedules_all aps
						 LEFT JOIN hz_cust_accounts_all hcaa
							ON aps.customer_id = hcaa.cust_account_id
						 LEFT JOIN hz_customer_profiles hzp
							ON     hcaa.cust_account_id = hzp.cust_account_id
							   AND aps.customer_site_use_id = hzp.site_use_id
						 LEFT JOIN hz_cust_profile_classes hcpc
							ON hzp.profile_class_id = hcpc.profile_class_id
						 LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
				   WHERE     1 = 1
					 ".$and."
					 AND  lower(hp.party_name || ' - ' || hcaa.account_name) like ?";
				 
		$data = $this->oracle->query($sql,$customer_name);
		return $data->result();
	}
	
	public function get_vehicle_invoice_details($customer_trx_id){
		
		$sql = "SELECT 
					apsa.customer_trx_id,
					apsa.trx_number, 
					to_char(apsa.trx_date,'MM/DD/YYYY') trx_date, 
					apsa.amount_due_original invoice_amount,
					apsa.amount_due_remaining balance,
					apsa.amount_line_items_original net_amount,
					apsa.tax_original vat_amount,
					 ROUND (CASE
                         WHEN SUBSTR (apsa.trx_number, 1, 3) = '521'
						 OR apsa.trx_number IN ('1926',
										   '1932',
										   '1927',
										   '1873',
										   '1890',
										   '1894',
										   '1899',
										   '1930',
										   '1925',
										   '1931')
                            THEN
                               (apsa.amount_line_items_original) * .02
                            ELSE
                               (apsa.amount_line_items_original) * .01
                         END,
                         2) wht_amount,
					apsa.amount_applied paid_amount,
					apsa.amount_adjusted adjusted_amount,
					apsa.invoice_currency_code currency,
					apsa.exchange_rate,
					CASE apsa.status WHEN 'OP' THEN 'Open' else'Closed' end status,
					ooha.order_number,
					to_char(ooha.ordered_date,'MM/DD/YYYY') ordered_date, 
					ottl.description order_type,
					ooha.attribute3 fleet_name,
					NVL(ooha.cust_po_number,'-') po_number,
					rcta.attribute3 cs_number,
					msib.attribute9 sales_model,
					msib.attribute8 body_color,
					msn.attribute2 chassis_number,
					rcta.attribute4 wb_number,
					rcta.attribute8 csr_number,
					rcta.attribute11 csr_date
				FROM ar_payment_schedules_all apsa
					LEFT JOIN ra_customer_trx_all rcta
						ON apsa.customer_trx_id = rcta.customer_trx_id
					LEFT JOIN mtl_serial_numbers msn
						ON rcta.attribute3 = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
						ON msn.inventory_item_id = msib.inventory_item_id
						AND msn.current_organization_id = msib.organization_id
					LEFT JOIN oe_order_headers_all ooha
						ON rcta.interface_header_attribute1 = to_char(ooha.order_number) 
					LEFT JOIN oe_transaction_types_tl ottl
						ON ooha.order_type_id = ottl.transaction_type_id
				WHERE apsa.customer_trx_id = ?";
				 
		$data = $this->oracle->query($sql,$customer_trx_id);
		return $data->result();
	}
	
	public function get_parts_invoice_details($customer_trx_id){
		
		$sql = "SELECT 
					apsa.customer_trx_id,
					apsa.trx_number, 
					to_char(apsa.trx_date,'MM/DD/YYYY') trx_date, 
					apsa.amount_due_original invoice_amount,
					apsa.amount_due_remaining balance,
					apsa.amount_line_items_original total_net_amount,
					apsa.tax_original total_vat_amount,
					 ROUND (CASE
                         WHEN SUBSTR (apsa.trx_number, 1, 3) = '521'
						 OR apsa.trx_number IN ('1926',
										   '1932',
										   '1927',
										   '1873',
										   '1890',
										   '1894',
										   '1899',
										   '1930',
										   '1925',
										   '1931')
                            THEN
                               (apsa.amount_line_items_original) * .02
                            ELSE
                               (apsa.amount_line_items_original) * .01
                         END,
                         2) wht_amount,
					apsa.amount_applied paid_amount,
					apsa.amount_adjusted adjusted_amount,
					apsa.invoice_currency_code currency,
					apsa.exchange_rate,
					CASE apsa.status WHEN 'OP' THEN 'Open' else'Closed' end status,
					ooha.order_number,
					to_char(ooha.ordered_date,'MM/DD/YYYY') ordered_date, 
					ottl.description order_type,
					NVL(ooha.cust_po_number,'-') po_number,
					rctla.qty,
					rctla.line_number,
					rctla.part_no,
					rctla.part_description,
					rctla.unit_selling_price,
					rctla.net_amount,
					rctla.vat_amount
				FROM ar_payment_schedules_all apsa
					LEFT JOIN ra_customer_trx_all rcta
						ON apsa.customer_trx_id = rcta.customer_trx_id
					LEFT JOIN (SELECT rctl.customer_trx_id,
									MAX(rctl.quantity_invoiced) qty,
									MAX(rctl.line_number) line_number,
									MAX(msib.segment1) part_no,
									MAX(CASE WHEN interface_line_attribute11 = 0 THEN msib.description ELSE NULL END) part_description,
									SUM(rctl.unit_selling_price) unit_selling_price,
									SUM(rctl.line_recoverable) net_amount,
									SUM(rctl.tax_recoverable) vat_amount
							FROM ra_customer_trx_lines_all rctl
							LEFT JOIN mtl_system_items_b msib
							ON rctl.inventory_item_id = msib.inventory_item_id
							and rctl.warehouse_id = msib.organization_id
							WHERE rctl.line_type = 'LINE'
							GROUP BY RCTL.customer_trx_id, rctl.inventory_item_id) rctla
						  ON rcta.customer_trx_id = rctla.customer_trx_id
					LEFT JOIN mtl_serial_numbers msn
						ON rcta.attribute3 = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
						ON msn.inventory_item_id = msib.inventory_item_id
						AND msn.current_organization_id = msib.organization_id
					LEFT JOIN oe_order_headers_all ooha
						ON rcta.interface_header_attribute1 = to_char(ooha.order_number) 
					LEFT JOIN oe_transaction_types_tl ottl
						ON ooha.order_type_id = ottl.transaction_type_id
				WHERE apsa.customer_trx_id = ?
				ORDER BY rctla.line_number";
				 
		$data = $this->oracle->query($sql,$customer_trx_id);
		return $data->result();
	}
}
