<?php

class Receipt_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_unapplied_receipts($params){
		
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
		                cba.bank_account_name";
		
		$data = $this->oracle->query($sql, $params);
		return $data->result_array();
	}
	

}
