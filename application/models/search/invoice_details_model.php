<?php

class Invoice_details_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_header_details($q){

		$sql = "SELECT soa.customer_trx_id invoice_id,
					   soa.trx_number      invoice_number,
					   soa.trx_date        invoice_date,
					   soa.payment_term    payment_terms,
					   soa.delivery_date,
					   rcta.cust_trx_type_id,
					   soa.due_date,
					   typ.description     invoice_type,
					   soa.status          invoice_status,
					   ooha.order_number,
					   ooha.attribute3 fleet_name,
					   ooha.ordered_date,
					   ottl.description    order_type,
					   ooha.cust_po_number,
					   hca.cust_account_id customer_id,
					   hca.account_number,
					   hca.account_name,
					   hp.party_name,
					   hcpc.profile_class_id,
					   hcpc.name           profile_class,
					   CASE
						  WHEN ottl.description IN
								  ('Fleet Sales Order', 'Vehicle Sales Order')
						  THEN
							 CONCAT ('313', LPAD (wdd.attribute2, 8, 0))
						  ELSE
							 oola.attribute10
					   END
						  dr_number,
					   wdd.source_line_id,
					    CASE soa.status WHEN 'OP' THEN 'Open' ELSE 'Closed' END status
				  FROM ipc.ipc_invoice_details soa
					   LEFT JOIN ra_customer_trx_all rcta
						  ON soa.customer_trx_id = rcta.customer_trx_id
					   LEFT JOIN oe_order_headers_all ooha
						  ON rcta.interface_header_attribute1 = TO_CHAR (ooha.order_number)
					   LEFT JOIN oe_order_lines_all oola
						  ON rcta.interface_header_attribute6 = oola.line_id
					   LEFT JOIN wsh_delivery_details wdd
						  ON oola.line_id = wdd.source_line_id
					   LEFT JOIN oe_transaction_types_tl ottl
						  ON ooha.order_type_id = ottl.transaction_type_id
					   LEFT JOIN ra_cust_trx_types_all typ
						  ON     soa.cust_trx_type_id = typ.cust_trx_type_id
							 AND rcta.org_id = typ.org_id
					   LEFT JOIN hz_cust_accounts_all hca
						  ON soa.customer_id = hca.cust_account_id
					   LEFT JOIN hz_parties hp ON hca.party_id = hp.party_id
					   LEFT JOIN hz_cust_profile_classes hcpc
						  ON soa.profile_class_id = hcpc.profile_class_id
				 WHERE 1 = 1
				AND (soa.trx_number = ? OR soa.cs_number = ?)";
		
		$data = $this->oracle->query($sql, array($q,$q));
		return $data->result();
	}
	
	public function get_vehicle_line_details($invoice_id){

					
		$sql = "SELECT rcta.attribute4 wb_number,
					   rcta.attribute8 csr_number,
					   msn.serial_number cs_number,
					   msn.attribute2 chassis_number,
					   msib.attribute9 sales_model,
					   msib.attribute8 body_color,
					   msn.lot_number,
					   msn.attribute3 engine_number,
					   msn.attribute7 aircon_number,
					   msn.attribute9 stereo_number,
					   msn.attribute6 key_number,
					   msn.attribute5 buyoff_date,
					   apsa.amount_due_original invoice_amount,
					   apsa.amount_due_remaining balance,
					   apsa.amount_line_items_original net_amount,
					   apsa.tax_original vat_amount,
					   ROUND (CASE
								 WHEN    SUBSTR (apsa.trx_number, 1, 3) = '521'
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
							  2)
						  wht_amount,
					   apsa.amount_applied paid_amount,
					   apsa.amount_adjusted adjusted_amount,
					   apsa.amount_credited credited_amount,
					   apsa.invoice_currency_code currency,
					   apsa.exchange_rate,
					   CASE apsa.status WHEN 'OP' THEN 'Open' ELSE 'Closed' END status
				  FROM ar_payment_schedules_all apsa
					   LEFT JOIN ra_customer_trx_all rcta
						  ON apsa.customer_trx_id = rcta.customer_trx_id
					   LEFT JOIN mtl_serial_numbers msn
						  ON rcta.attribute3 = msn.serial_number
					   LEFT JOIN mtl_system_items_b msib
						  ON     msn.inventory_item_id = msib.inventory_item_id
							 AND msn.current_organization_id = msib.organization_id
					   LEFT JOIN oe_order_headers_all ooha
						  ON rcta.interface_header_attribute1 = TO_CHAR (ooha.order_number)
					   LEFT JOIN oe_transaction_types_tl ottl
						  ON ooha.order_type_id = ottl.transaction_type_id
				 WHERE apsa.customer_trx_id = ?";
		
		$data = $this->oracle->query($sql, $invoice_id);
		return $data->result();
	}
	
	public function get_parts_line_details($invoice_id){
		
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
					apsa.amount_credited credited_amount,
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
				 
		$data = $this->oracle->query($sql,$invoice_id);
		return $data->result();
	}
	
	public function get_payment_details($invoice_id){
		
		$sql = "SELECT araa.amount_applied,
					   araa.apply_date,
					   araa.application_type,
					   acra.currency_code,
					   acra.receipt_number,
					   acra.receipt_date
				  FROM ar_receivable_applications_all araa
					   LEFT JOIN ar_cash_receipts_all acra
						  ON araa.cash_receipt_id = acra.cash_receipt_id
				 WHERE 1 = 1 AND araa.display = 'Y' 
				 AND araa.applied_customer_trx_id = ?
				UNION ALL
				SELECT (ada.amount * -1) amount_applied,
					   ada.apply_date,
					   'ADJ'             application_type,
					   NULL              currency_code,
					   NULL              receipt_number,
					   NULL              rceipt_date
				  FROM AR_ADJUSTMENTS_ALL ada
				 WHERE 1 = 1 
				 AND ada.customer_trx_id = ?";
				 
		$data = $this->oracle->query($sql, array($invoice_id, $invoice_id));
		return $data->result();
	}
}
