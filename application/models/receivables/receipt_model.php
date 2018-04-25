<?php

class Receipt_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_unapplied_receipts($from_date,$to_date){
		
		$sql = "SELECT acra.receipt_number,
			            acra.doc_sequence_value,
			            party.party_name customer_name,
			            cust.account_number,
			            cust.account_name,
			            site_uses.location,
			            cbb.bank_name,
			            cba.bank_account_name,
			            acra.currency_code,
			            CASE 
			                WHEN acra.status = 'UNID' THEN acra.amount
			                ELSE 0
			            END unidentified_amount,
			            acra.amount - sum(nvl(arra.amount_applied,0) * nvl(arra.trans_to_receipt_rate,1)) unapplied_amount,
			            sum(nvl(arra.amount_applied,0) * nvl(arra.trans_to_receipt_rate,1)) applied_amount,
			            acra.amount receipt_amount,
			            to_char(acra.receipt_date,'YYYY-MM-DD') receipt_date,
			            DECODE(acra.status,'UNID','Unidentified','UNAPP','Unapplied') status
				FROM ar_cash_receipts_all acra 
				        LEFT JOIN ar_receivable_applications_all arra
				            ON arra.cash_receipt_id = acra.cash_receipt_id
				            AND arra.display = 'Y' 
				        LEFT JOIN  HZ_CUST_SITE_USES_ALL SITE_USES
				            ON SITE_USES.SITE_USE_ID = ACRA.CUSTOMER_SITE_USE_ID
				        LEFT JOIN HZ_CUST_ACCOUNTS CUST
				            ON ACRA.PAY_FROM_CUSTOMER = CUST.CUST_ACCOUNT_ID
				        LEFT JOIN HZ_PARTIES PARTY
				            ON CUST.PARTY_ID = PARTY.PARTY_ID
				        LEFT JOIN ce_bank_acct_uses_all remit_bank
				             ON remit_bank.bank_acct_use_id = acra.remit_bank_acct_use_id
				        LEFT JOIN ce_bank_accounts cba
				            ON cba.bank_account_id = remit_bank.bank_account_id
				        LEFT JOIN ce_bank_branches_v cbb
				            ON cbb.branch_party_id = cba.bank_branch_id
				WHERE 1 = 1
				          and acra.status IN ('UNID','UNAPP')
				          and TO_DATE(acra.receipt_date) BETWEEN ? AND ?
				GROUP BY acra.RECEIPT_NUMBER,
		                acra.amount,
		                acra.receipt_date,
		                party.party_name,
		                cust.account_number,
		                cust.account_name,
		                site_uses.location,
		                acra.doc_sequence_value,
		                acra.status,
		                acra.currency_code,
		                cbb.bank_name,
		                cba.bank_account_name"; //echo $sql;print_r($params);die();
		
		$data = $this->oracle->query($sql, array($from_date,$to_date));
		return $data->result_array();
	}
	
	public function get_collection_receipts_header($receipt_id){
		
		$sql = "SELECT acra.cash_receipt_id,
					acra.comments, 
					acra.doc_sequence_value receipt_number,
					TO_CHAR (acra.receipt_date, 'MON DD, YYYY') date1, 
					acra.receipt_number receipt_num,
					acra.currency_code curr, 
					acra.amount receipt_amount, 
					acra.attribute1 check_bank,
					acra.attribute2 check_number, 
					acra.customer_receipt_reference REFERENCE,
					TO_CHAR (acra.receipt_date, 'Mon dd, yyyy') date2, 
					acra.pay_from_customer,
					 hp2.party_name,
					acra.customer_site_use_id, acra.amount * NVL (acra.exchange_rate, 1) amount123,
					(SELECT 
						 max(hca.class_code) || ' : ' || max(hccd.class_code_description)
							from HZ_CODE_ASSIGNMENTS hca,
							HZ_CLASS_CODE_DENORM hccd
							where 
							hca.class_code= hccd.class_code and 
							hca.class_category = hccd.class_category and
							hca.end_date_active is null
							and hca.owner_table_id = hp2.party_id
							 ) BUSINESS_STYLE,
							regexp_replace(cust.address,'DEALERS-PARTS|DEALERS-VEHICLE|DEALERS-OTHERS|DEALERS-FLEET') AS ADDRESS_ALL,
							hcsua.TAX_REFERENCE TAX_R_ALL,
						  (select count(cash_receipt_id)
								from AR_RECEIVABLE_APPLICATIONS_ALL where cash_receipt_id = acra.cash_receipt_id
								AND STATUS = 'APP') applied_count
				FROM ar_cash_receipts_all acra,
						 hz_cust_accounts hca2,
						 hz_parties hp2,
						 hz_cust_site_uses_all hcsua,
						  APPS.IPC_CUSTOMERS_V cust
				  WHERE acra.pay_from_customer = hca2.cust_account_id
							and hca2.party_id = hp2.party_id
							and cust.site_use_id =  acra.CUSTOMER_SITE_USE_ID
							and acra.type = 'CASH'
							and acra.status <> 'REV'
							AND acra.CUSTOMER_SITE_USE_ID = hcsua.SITE_USE_ID
							and acra.org_id =  82
							AND cash_receipt_id = ?
				--        AND acra.doc_sequence_value = '70100005144'
				ORDER BY acra.doc_sequence_value"; 
		
		$data = $this->oracle->query($sql, $receipt_id);
		return $data->result();
	}
	
	public function get_collection_receipts_lines($receipt_id){
		
		$sql = "SELECT trx_number,
					cs_number,
					 invoice_amount,
					 CASE 
						WHEN invoice_amount <> amount_applied THEN round(wht_tax,2) 
						ELSE 0
					 end wht,
					 amount_applied,
					 cash_receipt_id,
					 customer_trx_id,
					 tax_reference_all,
					 CASE 
						WHEN ABS(round(amount_due,2) - round(wht_tax,2)) = round(wht_tax,2) THEN 0
						ELSE round(amount_due,2) - round(wht_tax,2)
					 END balance_payable
					FROM (SELECT rcta.trx_number,
					rcta.attribute3 cs_number,
						 SUM (rctla.extended_amount) invoice_amount,
						 CASE WHEN hca.account_name = 'IPC Teammembers' THEN 0 
						 ELSE CASE 
							WHEN hca.cust_account_id IN('16085') THEN SUM(rctla.line_recoverable) * 0.05
							WHEN rcta.trx_number LIKE '521%' THEN SUM(rctla.line_recoverable) * 0.02
							ELSE SUM(rctla.line_recoverable) * 0.01
						 END
					 END wht_tax,
					 arra.amount_applied,
					 arra.cash_receipt_id,
					 rcta.customer_trx_id,
					 hcsua.tax_reference tax_reference_all,
					 apsa.amount_due_remaining amount_due,
					 hca.account_name
			FROM  ar_receivable_applications_all arra
					  INNER JOIN ra_customer_trx_all rcta
						ON arra.applied_customer_trx_id = rcta.customer_trx_id
					  INNER JOIN ra_customer_trx_lines_all rctla
						ON rctla.customer_trx_id = rcta.customer_trx_id
					  INNER JOIN hz_cust_site_uses_all hcsua
						ON rcta.bill_to_site_use_id = hcsua.site_use_id
					  INNER JOIN hz_cust_acct_sites_all hcasa
						ON hcsua.cust_acct_site_id = hcasa.cust_acct_site_id
					  INNER JOIN hz_cust_accounts_all hca
					  ON hca.cust_account_id = hcasa.cust_account_id
					  INNER JOIN ar_payment_schedules_all apsa
						ON apsa.customer_trx_id = rcta.customer_trx_id
			WHERE 1 = 1
					AND arra.cash_receipt_id  = ?
					AND arra.display = 'Y'
			--            AND acra.doc_sequence_value like '701%14487'
			GROUP BY rcta.trx_number,
					rcta.attribute3,
					arra.cash_receipt_id,
					rcta.customer_trx_id,
					hcsua.tax_reference,
					arra.amount_applied,
					apsa.amount_due_remaining,
					hca.account_name,
					hca.cust_account_id)"; 
					
		$sql = "SELECT 
				   araa.cash_receipt_id,
				   apsa.trx_number,
				   rcta.attribute3 cs_number,
				   araa.amount_applied,
				   apsa.amount_due_original invoice_amount,
				   apsa.amount_due_remaining balance_payable,
				   ROUND ( (apsa.amount_due_original / 1.12) * .01, 2) wht_amount
			  FROM AR_RECEIVABLE_APPLICATIONS_all araa
				   LEFT JOIN AR_RECEIVABLES_TRX_ALL arta
					  ON araa.receivables_trX_id = arta.receivables_trX_id
				   LEFT JOIN ar_payment_schedules_all apsa
					  ON araa.APPLIED_PAYMENT_SCHEDULE_ID = apsa.PAYMENT_SCHEDULE_ID
				   LEFT JOIN ra_customer_trx_all rcta
					  ON apsa.customer_trx_id = rcta.customer_trx_id
			 WHERE 1 = 1 AND araa.cash_receipt_id = ? AND araa.display = 'Y'"; //773225 test receipt
		
		$data = $this->oracle->query($sql, $receipt_id);
		return $data->result();
	}
	
	public function get_receipt_id($search_key){
		
		$and = $this->session->tre_portal_customer_id == NULL ? '':' AND acra.pay_from_customer = ' . $this->session->tre_portal_customer_id;
		
		$sql = "SELECT DISTINCT araa.cash_receipt_id receipt_id,
					   acra.doc_sequence_value receipt_number,
					   acra.amount             receipt_amount
				  FROM ar_receivable_applications_all araa
					   LEFT JOIN ar_cash_receipts_all acra
						  ON araa.cash_receipt_id = acra.cash_receipt_id
					   LEFT JOIN ra_customer_trx_all rcta
						  ON araa.applied_customer_trx_id = rcta.customer_trx_id
				 WHERE     1 = 1
					   AND araa.display = 'Y'
					   ".$and."
					   AND (rcta.trx_number = ? OR rcta.attribute3 = ? OR acra.doc_sequence_value = ?)
					   AND araa.cash_receipt_id IS NOT NULL";
				
		$data = $this->oracle->query($sql, array($search_key,$search_key,$search_key));
		return $data->result();
	}
}
