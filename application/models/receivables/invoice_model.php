<?php

class Invoice_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function get_invoices($from_date, $to_date, $profile_ids, $customer_id){
		
		$and = $profile_ids != 0 ? " AND hcpc.profile_class_id IN (".$profile_ids.") ":"";
		
		$sql = "SELECT hca.cust_account_id       customer_id,
					   hp.party_name,
					   hca.account_name,
					   ooha.attribute3           fleet_name,
					   soa.cs_number       cs_number,
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
							 wdd.attribute2
					   END
						  dr_number,
					   soa.invoice_orig_amount   invoice_amount,
					   soa.vat_orig_amount       vat_amount,
					   soa.wht_orig_amount       wht_amount,
					   apsa.amount_due_remaining balance,
					   soa.status                invoice_status,
					   receipt.receipt_number,
					   soa.invoice_currency_code,
					   soa.exchange_rate
				  FROM ipc.ipc_invoice_details soa
					   LEFT JOIN ra_customer_trx_all rcta
						  ON soa.customer_trx_id = rcta.customer_trx_id
					   LEFT JOIN ar_payment_schedules_all apsa
						  ON rcta.customer_trx_id = apsa.customer_trx_id
					   LEFT JOIN (  SELECT  araa.applied_customer_trx_id, max(receipt_number) receipt_number
								FROM ar_receivable_applications_all araa
									 LEFT JOIN ar_cash_receipts_all acra
										ON araa.cash_receipt_id = acra.cash_receipt_id
							   WHERE araa.display = 'Y'
							GROUP BY araa.applied_customer_trx_id) receipt
							ON receipt.applied_customer_trx_id = apsa.customer_trx_id
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
					   ".$and."
					   AND soa.customer_id = NVL(?, soa.customer_id)
					   ORDER BY  soa.status desc, soa.due_date"; //echo $sql;die();
		
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
					chk.check_number,
					chk.check_bank,
					to_char(chk.check_date, 'YYYY-MM-DD') check_date,
					chk.check_amount,
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
					LEFT JOIN (SELECT DISTINCT pdc.check_number,
									pdc.check_bank,
									unit.cs_number,
									pdc.check_date,
									pdc.check_amount,
									pdc.date_created
							  FROM ipc.ipc_treasury_pdc pdc
								   LEFT JOIN ipc.ipc_treasury_pdc_units unit
									  ON pdc.check_id = unit.check_id
								   LEFT JOIN ipc.ipc_treasury_approved_pdc app_pdc
									  ON     pdc.check_id = app_pdc.check_id
										 AND unit.cs_number = app_pdc.cs_number
							 WHERE app_pdc.check_id IS NOT NULL
								 AND app_pdc.check_id = (select max(check_id) 
												from ipc.ipc_treasury_approved_pdc
												 where cs_number = unit.cs_number)) chk
						ON msn.serial_number = chk.cs_number
				WHERE 1 = 1
					AND soa.profile_class_id IN (1040, 1043, 1045)
					AND soa.due_date BETWEEN ? AND ?
					AND soa.status = 'OP'";
		
		$data = $this->oracle->query($sql, array($from_date, $to_date));
		return $data->result_array();
	}
	
	public function get_parts_invoice_by_duedate($from_date, $to_date){
		
		
		$sql = "SELECT
					soa.customer_id,
					hp.party_name       customer_name,
					hcaa.account_name   account_name,
					hcpc.name           profile_class,
					soa.customer_trx_id invoice_id,
					ooha.cust_po_number,
					soa.trx_number      invoice_number,
					to_char(soa.trx_date, 'YYYY-MM-DD')      invoice_date,
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
					AND soa.profile_class_id IN (1042,1044,1046,1049,1050)
					AND soa.due_date BETWEEN ? AND ?
					AND soa.status = 'OP'";
		
		$data = $this->oracle->query($sql, array($from_date, $to_date));
		return $data->result_array();
	}
	
	public function get_vehicle_invoice_print($invoice_id)
	{
		$sql = "SELECT rcta.customer_trx_id,
					   rcta.sold_to_customer_id,
					   rcta.invoice_currency_code currency,
					   cust.cust_account_id,
					   rcta.trx_number,
					   rcta.trx_date,
					   rtl.name                            payment_terms,
					   rcta.interface_header_attribute1                        so_number,
					   rcta.attribute4                                         wb_number,
					   rcta.attribute9 dr_number,
					   msn.inventory_item_id,                                   
					   msn.attribute1                                          csr_number,
					   msn.attribute12                                         csr_or_number,
					   msib.segment1                                           model_code,
					   msib.attribute9                                         sales_model,
					   msn.lot_number                                          lot_number,
					   msn.serial_number                                       cs_number,
					   msn.attribute2                                          chassis_number,
					   msib.attribute11                                        engine_type,
					   msn.attribute3                                          engine_no,
					   msib.attribute8                                         body_color,
					   msib.attribute17                                        fuel,
					   msib.attribute14                                        gvw,
					   msn.attribute6                                          key_no,
					   msib.attribute13                                        tire_specs,
					   msn.attribute8                                          battery,
					   msib.attribute16                                        displacement,
					   msib.attribute21                                        year_model,
					   msit.items1,
					   msit.items2,
					   cust.party_name,
					   cust.account_name,
					   cust.tax_reference,
					   cust.address,
					   ooha.attribute3                     						fleet_name,
					   cust.business_style,
					   cust.class_code,
					   rctla.vatable_sales,
					   rctla.exempt,
					   rctla.discount,
					   rctla.vatable_sales + rctla.discount                    amt_net_of_vat,
					   rctla.vat_amount,
					   rctla.vatable_sales + rctla.discount + rctla.vat_amount total_sales,
					   CONCAT('313', LPAD(wdd.attribute2,8,0)) dr_number
				  FROM ra_customer_trx_all rcta
					   INNER JOIN
					   (  SELECT customer_trx_id,
								 SUM (
									CASE
									   WHEN LINE_RECOVERABLE > 0 AND TAX_RECOVERABLE > 0 THEN LINE_RECOVERABLE
									   ELSE 0
									END)
									vatable_sales,
								  SUM (
									CASE
									   WHEN LINE_RECOVERABLE > 0 AND TAX_RECOVERABLE = 0 THEN LINE_RECOVERABLE
									   ELSE 0
									END)
									exempt,
								 SUM (
									CASE
									   WHEN LINE_RECOVERABLE < 0 THEN LINE_RECOVERABLE
									   ELSE 0
									END)
									discount,
								 SUM (TAX_RECOVERABLE) vat_amount,
								 max(interface_line_attribute6) so_line_id
							FROM ra_customer_trx_lines_all
						   WHERE line_type = 'LINE'
						GROUP BY customer_trx_id) rctla
						  ON rcta.customer_trx_id = rctla.customer_trx_id
						LEFT JOIN wsh_delivery_details wdd
							on rctla.so_line_id = wdd.source_line_id
						INNER JOIN oe_order_headers_all ooha
							ON rcta.interface_header_attribute1 = ooha.order_number
						INNER JOIN ra_terms_tl rtl ON ooha.payment_term_id = rtl.term_id
					   INNER JOIN mtl_serial_numbers msn
						  ON rcta.attribute3 = msn.serial_number
					   INNER JOIN mtl_system_items_b msib
						  ON     msib.inventory_item_id = msn.inventory_item_id
							 AND msib.organization_id = msn.current_organization_id
					   LEFT  JOIN
						  (SELECT HCAA.cust_account_id,
								 MAX (hp.party_name)             party_name,
								 MAX (hcaa.account_name)         account_name,
								 DECODE(regexp_replace(MAX(hl.address1),'DEALERS-PARTS|DEALERS-VEHICLE|DEALERS-OTHERS|DEALERS-FLEET|FLEET-PARTS|FLEET'), '', MAX(hl.address2) || ' ' || MAX(hl.address3), regexp_replace(MAX(hl.address1),'DEALERS-PARTS|DEALERS-VEHICLE|DEALERS-OTHERS|DEALERS-FLEET|FLEET-PARTS|FLEET') || ' ' || MAX(hl.address2) || ' ' || MAX(hl.address3)) address,
								 MAX (hccd.class_code_description) business_style,
								 MAX (hca.class_code)            class_code,
								 MAX (hcsua.tax_reference)       tax_reference
							FROM hz_cust_accounts_all hcaa
								 LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
								 LEFT JOIN HZ_CODE_ASSIGNMENTS hca
									ON hca.owner_table_id = hp.party_id AND hca.end_date_active IS NULL
								 LEFT JOIN HZ_CLASS_CODE_DENORM hccd
									ON     hca.class_code = hccd.class_code
									   AND hca.class_category = hccd.class_category
								 LEFT JOIN hz_cust_acct_sites_all hcasa
									ON hcaa.cust_account_id = hcasa.cust_account_id
								 LEFT JOIN hz_cust_site_uses_all hcsua
									ON hcasa.cust_acct_site_id = hcsua.cust_acct_site_id
								 LEFT JOIN hz_party_sites hps
									ON hcasa.party_site_id = hps.party_site_id
								 LEFT JOIN hz_locations hl
									ON hps.location_id = hl.location_id
						GROUP BY HCAA.cust_account_id ) cust
						  ON rcta.sold_to_customer_id = cust.cust_account_id 
					   LEFT JOIN (SELECT inventory_item_id, 
										organization_id,
										MAX(SUBSTR(LONG_DESCRIPTION, 1, INSTR(LONG_DESCRIPTION, CHR(10),1,12)-1)) items1,
										MAX(SUBSTR(LONG_DESCRIPTION,INSTR(TRIM(LONG_DESCRIPTION), CHR(10),1,12)+1,3000)) items2
							   FROM MTL_SYSTEM_ITEMS_TL
							 GROUP BY inventory_item_id, organization_id) msit
						   ON msib.inventory_item_id = msit.inventory_item_id
						   AND msit.organization_id = 121
						WHERE 1 = 1   
							-- AND rcta.customer_trx_id = 
							AND rcta.customer_trx_id = ?";
		$data = $this->oracle->query($sql, $invoice_id);
		return $data->result();
	}
	
	public function get_parts_invoice_header_print($invoice_id){
		
		$sql = "SELECT rcta.customer_trx_id,
						rcta.invoice_currency_code currency,
					   rcta.trx_number,
					   rcta.trx_date,
					   rcta.comments,
					   ooha.order_number,
					   ooha.cust_po_number,
					   INTERFACE_HEADER_ATTRIBUTE3 dr_reference,
					   rt.name payment_terms,
					   rcta.attribute1             remarks,
					   rcta.attribute2             addl_remarks,
					   cust.party_name,
					   cust.account_name,
					   cust.tax_reference,
					   cust.address,
					   cust.class_code,
					   cust.business_style
				  FROM ra_customer_trx_all rcta
					   LEFT JOIN oe_order_headers_all ooha
						  ON rcta.interface_header_attribute1 = TO_CHAR (ooha.order_number)
					   LEFT JOIN ra_terms_tl rt ON ooha.payment_term_id = rt.term_id
					   LEFT JOIN
					   (  SELECT HCAA.cust_account_id,
								 MAX (hp.party_name)             party_name,
								 MAX (hcaa.account_name)         account_name,
								 DECODE (
									REGEXP_REPLACE (
									   MAX (hl.address1),
									   'DEALERS-PARTS|DEALERS-VEHICLE|DEALERS-OTHERS|DEALERS-FLEET|FLEET-PARTS|FLEET|POWERTRAIN'),
									'', MAX (hl.address2) || ' ' || MAX (hl.address3),
									   REGEXP_REPLACE (
										  MAX (hl.address1),
										  'DEALERS-PARTS|DEALERS-VEHICLE|DEALERS-OTHERS|DEALERS-FLEET|FLEET-PARTS|FLEET|POWERTRAIN')
									|| ' '
									|| MAX (hl.address2)
									|| ' '
									|| MAX (hl.address3))
									address,
								  MAX (hca.class_code)            class_code,
								 MAX (hccd.class_code_description) business_style,
								 MAX (hcsua.tax_reference)       tax_reference
							FROM hz_cust_accounts_all hcaa
								 LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
								 LEFT JOIN HZ_CODE_ASSIGNMENTS hca
									ON     hca.owner_table_id = hp.party_id
									   AND hca.end_date_active IS NULL
								 LEFT JOIN HZ_CLASS_CODE_DENORM hccd
									ON     hca.class_code = hccd.class_code
									   AND hca.class_category = hccd.class_category
								 LEFT JOIN hz_cust_acct_sites_all hcasa
									ON hcaa.cust_account_id = hcasa.cust_account_id
								 LEFT JOIN hz_cust_site_uses_all hcsua
									ON hcasa.cust_acct_site_id = hcsua.cust_acct_site_id
								 LEFT JOIN hz_party_sites hps
									ON hcasa.party_site_id = hps.party_site_id
								 LEFT JOIN hz_locations hl ON hps.location_id = hl.location_id
						GROUP BY HCAA.cust_account_id) cust
						  ON rcta.sold_to_customer_id = cust.cust_account_id
				 WHERE 1 = 1 AND rcta.customer_trx_id = ?";
 
		$data = $this->oracle->query($sql, $invoice_id);
		$rows = $data->result();
		return $rows[0];
	}
	
	public function get_parts_invoice_lines_print($invoice_id){
				
		$sql = "SELECT MAX (rctla.sales_order_line) so_line,
						 MAX (msib.segment1)        part_number,
						 NVL(MAX (msib.description), MAX(rctla.description))  part_description,
						 MAX (
                            CASE rcta.cust_trx_type_id WHEN 2081
                            THEN  rctla.quantity_invoiced
                            ELSE
                               CASE WHEN rctla.unit_selling_price > 0
                                  THEN rctla.quantity_invoiced
                                   ELSE 0
                                END
							END)
							qty,
						 SUM (
						 CASE rcta.cust_trx_type_id WHEN 2081
						 THEN rctla.unit_selling_price
						 ELSE
							CASE WHEN rctla.unit_selling_price > 0
							    THEN rctla.unit_selling_price
							   ELSE 0
							   END
							END)
							unit_price,
						 SUM (
						   CASE rcta.cust_trx_type_id WHEN 2081
						   THEN rctla.extended_amount
						   ELSE
							CASE WHEN rctla.extended_amount > 0
							   THEN rctla.extended_amount
							   ELSE 0
							END
							END)
							amount,
						 SUM (
						  CASE rcta.cust_trx_type_id WHEN 2081
						   THEN rctla.extended_amount
						   ELSE
                                CASE
                                   WHEN  rctla.extended_amount < 0
                                   THEN
                                      rctla.extended_amount
                                   ELSE
                                      0
                                 END
							END)
							discount,
						 SUM (rctla.tax_recoverable) VAT,
						 MAX (wdd.attribute2)     dr_number,
						 MAX (wdd.batch_id)             picklist_number
					FROM ra_customer_trx_lines_all rctla
                        LEFT JOIN ra_customer_Trx_all rcta
                            ON rctla.customer_trx_id = rcta.customer_trx_id
						 LEFT JOIN mtl_system_items_b msib
							ON     rctla.inventory_item_id = msib.inventory_item_id
							   AND NVL(rctla.warehouse_id,1) = decode(rctla.warehouse_id,null,1,msib.organization_id)
						 LEFT JOIN oe_order_lines_all oola
							ON rctla.interface_line_attribute6 = oola.line_id
						 LEFT JOIN (select source_line_id, batch_id, attribute2 FROM wsh_delivery_details group by SOURCE_LINE_ID, batch_id, attribute2) wdd
							ON oola.line_id = wdd.source_line_id
				   WHERE 1 = 1 AND rctla.line_type = 'LINE' AND rctla.customer_trx_id = ?
				GROUP BY rctla.inventory_item_id
				ORDER BY TO_NUMBER (so_line)";
 
		$data = $this->oracle->query($sql, $invoice_id);
		return $data->result();
	}
	
	public function get_others_invoice_lines_print($invoice_id){
		
		$sql = "SELECT 
				   rcta.customer_trx_id,
				   rcta.trx_number,
				   rcta.trx_date,
				   rctla.line_number,
				   rctla.description,
				   rctla.extended_amount amount,
				   rctla.tax_recoverable vat
			  FROM ra_customer_trx_all rcta
				   LEFT JOIN ra_customer_trx_lines_all rctla
					  ON rcta.customer_trx_id = rctla.customer_trx_id
			 WHERE 1 = 1 AND rcta.customer_trx_id = ? AND rctla.line_type = 'LINE'";
 
		$data = $this->oracle->query($sql, $invoice_id);
		return $data->result();
	}
}
