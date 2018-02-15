<?php

class Invoice_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}
	
	public function get_vehicle_invoice_by_duedate($from_date, $to_date){
		
		$sql = "SELECT hcaa.account_number account_number,
					   hp.party_name       customer_name,
					   hcaa.account_name   account_name,
					   ooha.attribute3,
					   hcpc.name           profile_class,
					   soa.customer_trx_id transaction_id,
					   soa.trx_number      transaction_number,
					   soa.trx_date        transaction_date,
					   soa.cs_number,
					   msib.attribute9 sales_model,
					   msib.attribute8 body_color,
					   soa.payment_term,
					   soa.delivery_date,
					   soa.due_date,
					   soa.days_overdue,
					   soa.transaction_amount,
					   soa.wht_amount,
					   soa.balance,
					   pdc.check_number,
					   pdc.check_bank,
					   pdc.check_date,
					   pdc.check_amount
				  FROM IPC.IPC_INVOICE_DETAILS soa
					   LEFT JOIN (SELECT *
						  FROM (SELECT pdcu.*,
									   unit.cs_number,
									   RANK ()
										  OVER (PARTITION BY cs_number ORDER BY unit.check_id DESC)
										  rnk
								  FROM ipc.ipc_treasury_pdc_units unit
									   LEFT JOIN ipc.ipc_treasury_pdc pdcu
										  ON unit.check_id = pdcu.check_id
								 WHERE 1 = 1 AND pdcu.batch_id IS NOT NULL)
						 WHERE rnk = 1) pdc
						  ON soa.cs_number = pdc.cs_number
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
						  ON     hcaa.cust_account_id = hzp.cust_account_id
							 AND soa.customer_site_use_id = hzp.site_use_id
					   LEFT JOIN hz_cust_profile_classes hcpc
						  ON hzp.profile_class_id = hcpc.profile_class_id
					   LEFT JOIN hz_parties hp ON hcaa.party_id = hp.party_id
				 WHERE     1 = 1
					   AND soa.due_date BETWEEN ? AND ?
					   AND soa.status = 'OP'
					   AND soa.profile_class_id IN (1043, 1040, 1045)";
		
		$data = $this->oracle->query($sql, array($from_date, $to_date));
		return $data->result_array();
	}
}
