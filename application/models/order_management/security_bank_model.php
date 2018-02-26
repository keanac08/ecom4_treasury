<?php

class Security_bank_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_tagged(){
		
		$sql = "SELECT hcca.cust_account_id customer_id,
						msn.serial_number || '-' || TO_CHAR(sysdate, 'MMDDYYYY') INVOICE_NUMBER,
						to_char(d_attribute20, 'MM/DD/YYYY') invoice_date,
						to_char(d_attribute20, 'MM/DD/YYYY') invoice_due_date,
						round((oola.unit_selling_price + oola.tax_value) - (oola.unit_selling_price * .01),2) invoice_amount,
						'INV' document_type,
						'0222025472001' collection_acct_no,
						msib.attribute9 model,
						'NULL' serial_number,
						msn.attribute3 engine_number,
						msn.serial_number cs_number,
						NVL(reverse(substr(reverse(substr(msn.attribute1,7)),-4)),EXTRACT(year FROM sysdate)) year_model,
						msib.attribute8 body_color,
						'0' excempt,
						'0' zero_rated,
						'0' discount,
						-- CASE WHEN oola.unit_list_price - oola.unit_selling_price > 0 THEN  round(oola.unit_list_price - oola.unit_selling_price,2) ELSE 0 END discount,
						ROUND(oola.unit_selling_price - (oola.unit_selling_price * .01),2) net_amount,
						ooha.order_number po_ref_no,
						'NULL' wb_number,
						msn.attribute6 key_number,
						round(oola.tax_value,2) vat,
						CASE hp.address1 WHEN 'DEALERS-PARTS' THEN 'NULL' ELSE hp.address1 end address1,
						hp.address2,
						hp.address3,
						msn.lot_number,
						round(oola.unit_selling_price * .01,2) wht
					FROM oe_order_headers_all ooha
						 LEFT JOIN oe_order_lines_all oola 
							ON ooha.header_id = oola.header_id
						 LEFT JOIN mtl_reservations mr
							ON oola.line_id = mr.demand_source_line_id
						 LEFT JOIN mtl_serial_numbers msn
							ON mr.reservation_id = msn.reservation_id
						 LEFT JOIN hz_cust_accounts_all hcca
							ON oola.sold_to_org_id = hcca.cust_account_id
						 LEFT JOIN hz_parties hp ON hcca.party_id = hp.party_id
						 LEFT JOIN mtl_system_items_b msib
							ON oola.inventory_item_id = msib.inventory_item_id
							   AND oola.ship_from_org_id = msib.organization_id
						 LEFT JOIN oe_transaction_types_tl ottl
							ON ooha.order_type_id = ottl.transaction_type_id
						 LEFT JOIN ra_terms_tl rt 
							ON oola.payment_term_id = rt.term_id
						 LEFT JOIN oe_order_holds_all hold 
							ON oola.line_id = hold.line_id
				   WHERE 1 = 1
						 AND oola.ship_from_org_id = 121
						 AND hcca.cust_account_id = 15096
						 AND msn.serial_number IS NOT NULL
						 AND NVL (hold.RELEASED_FLAG, NVL (oola.ATTRIBUTE20, 'N')) = 'N'
						 AND msn.serial_number NOT IN (SELECT cs_number FROM ipc.ipc_sb_tagged_units)";
					   
		$data = $this->oracle->query($sql);
		return $data->result();
	}
	
	public function insert_sent_tagged($cs_number){
		
		$sql = "INSERT INTO ipc.ipc_sb_tagged_units (cs_number) VALUES (?)";
		$this->oracle->query($sql, $cs_number);
	}
	
	public function truncate_tagged_units(){
		
		$sql = "TRUNCATE TABLE ipc.ipc_sb_tagged_units";
		$this->oracle->query($sql);
	}
}
