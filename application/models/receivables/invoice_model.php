<?php

class Invoice_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function get_invoices($from_date, $to_date, $customer_id){
		
		$sql = "SELECT hca.cust_account_id       customer_id,
					   hp.party_name,
					   hca.account_name,
					   ooha.attribute3           fleet_name,
					   soa.customer_trx_id       invoice_id,
					   soa.trx_number            invoice_number,
					   to_char(soa.trx_date, 'YYYY-MM-DD') invoice_date,
					   soa.payment_term          payment_terms,
					   to_char(soa.delivery_date, 'YYYY-MM-DD') delivery_date,
					   to_char(soa.due_date, 'YYYY-MM-DD') due_date,
					   typ.description           invoice_type,
					   ooha.order_number,
					   to_char(ooha.ordered_date, 'YYYY-MM-DD') ordered_date,
					   ottl.description          order_type,
					   ooha.cust_po_number,
					   hcpc.name                 profile_class,
					   CASE
						  WHEN ottl.description IN
								  ('Fleet Sales Order', 'Vehicle Sales Order')
						  THEN
							 CONCAT ('313', LPAD (wdd.attribute2, 8, 0))
						  ELSE
							 oola.attribute10
					   END
						  dr_number,
					   soa.invoice_orig_amount   invoice_amount,
					   soa.vat_orig_amount       vat_amount,
					   soa.wht_orig_amount       wht_amount,
					   apsa.amount_due_remaining balance,
					   soa.status                invoice_status,
					   soa.invoice_currency_code,
					   soa.exchange_rate
				  FROM ipc.ipc_invoice_details soa
					   LEFT JOIN ra_customer_trx_all rcta
						  ON soa.customer_trx_id = rcta.customer_trx_id
					   LEFT JOIN ar_payment_schedules_all apsa
						  ON rcta.customer_trx_id = apsa.customer_trx_id
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
				 WHERE     1 = 1
					   AND soa.trx_date BETWEEN ? AND ?
					   AND soa.customer_id = NVL(?, soa.customer_id)";
		
		$data = $this->oracle->query($sql, array($from_date, $to_date, $customer_id));
		return $data->result_array();
	}
	
	public function get_vehicle_invoice_by_duedate($from_date, $to_date){
		
		
		$sql = "SELECT
					soa.customer_id,
					hp.party_name       customer_name,
					hcaa.account_name   account_name,
					ooha.attribute3 fleet_name,
					hcpc.name           profile_class,
					soa.customer_trx_id invoice_id,
					soa.trx_number      invoice_number,
					to_char(soa.trx_date, 'YYYY-MM-DD')      invoice_date,
					soa.cs_number,
					msib.attribute9 sales_model,
					msib.attribute8 body_color,
					soa.payment_term,
					to_char(soa.delivery_date, 'YYYY-MM-DD')delivery_date,
					to_char(soa.due_date, 'YYYY-MM-DD') due_date,
					CASE
						WHEN soa.due_date IS NOT NULL AND soa.due_date < TRUNC(SYSDATE)
							THEN
								TRUNC(SYSDATE) - soa.due_date
							ELSE
								0
						END
					days_overdue,
					soa.invoice_amount ,
					soa.wht_orig_amount wht_amount,
					apsa.amount_due_remaining balance,
					to_char(to_date('".$from_date."'), 'YYYY-MM-DD') from_duedate,
					to_char(to_date('".$to_date."'), 'YYYY-MM-DD') to_duedate
				FROM ipc.ipc_invoice_details soa
					LEFT JOIN ar_payment_schedules_all apsa
						ON soa.customer_trx_id = apsa.customer_trx_id
					LEFT JOIN mtl_serial_numbers msn
						ON soa.cs_number = msn.serial_number
					LEFT JOIN mtl_system_items_b msib
						ON msn.inventory_item_id = msib.inventory_item_id
						AND msn.current_organization_id = msib.organization_id
					LEFT JOIN ra_customer_trx_all rcta
						ON soa.customer_trx_id = rcta.customer_trx_id
					LEFT JOIN oe_order_headers_all ooha
						ON rcta.interface_header_attribute1 = ooha.order_number
					LEFT JOIN hz_cust_accounts_all hcaa
						ON soa.customer_id = hcaa.cust_account_id
					LEFT JOIN hz_customer_profiles hzp
						ON hcaa.cust_account_id = hzp.cust_account_id
						AND soa.customer_site_use_id = hzp.site_use_id
					LEFT JOIN hz_cust_profile_classes hcpc
						ON hzp.profile_class_id = hcpc.profile_class_id
					LEFT JOIN hz_parties hp 
						ON hcaa.party_id = hp.party_id
				WHERE 1 = 1
					AND soa.profile_class_id IN (1040, 1043, 1045)
					AND soa.due_date BETWEEN ? AND ?
					AND soa.status = 'OP'";
		
		$data = $this->oracle->query($sql, array($from_date, $to_date));
		return $data->result_array();
	}
}
