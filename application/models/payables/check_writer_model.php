<?php

class Check_writer_model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_payables($params){
		
		$sql = "SELECT sup.segment1                                payee_code,
					   sup.vendor_name                             payee_name,
					   sup.attribute3                              ifs_payee_code,
					   ieba.bank_account_num                       account_number,
					   ac.doc_sequence_value                       reference_no,
					   TO_CHAR (ai.invoice_date, 'MM/DD/YYYY')     invoice_date,
					   ai.invoice_num                              invoice_no,
					   aip.amount                                  paid_amount,
					   ai.invoice_amount                           invoice_amount,
					   ail.included_tax_amount                     vat_amount,
					   ai.invoice_amount - ail.included_tax_amount invoice_wout_vat,
					   ail2.amount * -1                            wht_amount,
					   ail.tax_classification_code                 vat_code,
					   gt.tax_name                                 wht_code,
					   tr.tax_rate / 100                           wht_rate,
					   ac.check_number                             check_number,
					   ac.check_date                               check_date,
					   ac.amount                                   check_amount,
					   cpd.payment_document_name                   check_bank
				  FROM ap_checks_all ac
					   LEFT JOIN ce_payment_documents cpd
						  ON ac.payment_document_id = cpd.payment_document_id
					   LEFT JOIN ap_suppliers sup 
					      ON ac.vendor_id = sup.vendor_id
					   LEFT JOIN ap_invoice_payments_all aip 
					      ON ac.check_id = aip.check_id
					   LEFT JOIN ap_invoices_all ai 
					      ON aip.invoice_id = ai.invoice_id
					   LEFT JOIN
					   (  SELECT invoice_id,
								 SUM (included_tax_amount)   included_tax_amount,
								 MAX (tax_classification_code) tax_classification_code
							FROM ap_invoice_lines_all
						   WHERE line_type_lookup_code = 'item'
						GROUP BY invoice_id) ail
						  ON ai.invoice_id = ail.invoice_id
					   LEFT JOIN
					   (  SELECT invoice_id,
								 SUM (amount)                amount,
								 MAX (tax_classification_code) tax_classification_code,
								 MAX (awt_group_id)          awt_group_id
							FROM ap_invoice_lines_all
						   WHERE line_type_lookup_code = 'awt'
						GROUP BY invoice_id) ail2
						  ON ai.invoice_id = ail2.invoice_id
					   LEFT JOIN ap_awt_group_taxes_all gt ON ail2.awt_group_id = gt.GROUP_ID
					   LEFT JOIN ap_awt_tax_rates_all tr ON gt.tax_name = tr.tax_name
					   LEFT JOIN iby_account_owners iao
						  ON iao.account_owner_party_id = sup.party_id
					   LEFT JOIN iby_ext_bank_accounts ieba
						  ON ieba.ext_bank_account_id = iao.ext_bank_account_id
				 WHERE     1 = 1
					   AND ac.check_date BETWEEN ? AND ?
					   AND cpd.payment_document_name = ?
					   AND ac.status_lookup_code = 'NEGOTIABLE'";
					   
		$data = $this->oracle->query($sql, $params);
		return $data->result();
		
	}
	
	public function get_payables_bu($params){
		
		$sql = "SELECT SUP.SEGMENT1                                PAYEE_CODE,
					   SUP.VENDOR_NAME                             PAYEE_NAME,
					   SUP.ATTRIBUTE3                              IFS_PAYEE_CODE,
					   AC.DOC_SEQUENCE_VALUE                       REFERENCE_NO,
					   '' account_number,
					   TO_CHAR(AI.INVOICE_DATE, 'MM/DD/YYYY')      INVOICE_DATE,
					   AI.INVOICE_NUM                              INVOICE_NO,
					   AIP.AMOUNT                                  PAID_AMOUNT,
					   AI.INVOICE_AMOUNT                           INVOICE_AMOUNT,
					   AIL.INCLUDED_TAX_AMOUNT                     VAT_AMOUNT,
					   AI.INVOICE_AMOUNT - AIL.INCLUDED_TAX_AMOUNT INVOICE_WOUT_VAT,
					   AIL2.AMOUNT * -1                            WHT_AMOUNT,
					   AIL.TAX_CLASSIFICATION_CODE                 VAT_CODE,
					   GT.TAX_NAME                                 WHT_CODE,
					   TR.TAX_RATE / 100                           WHT_RATE,
					   AC.CHECK_NUMBER                             CHECK_NUMBER,
					   AC.CHECK_DATE                               CHECK_DATE,
					   AC.AMOUNT                                   CHECK_AMOUNT,
					   CPD.PAYMENT_DOCUMENT_NAME                   CHECK_BANK
				  FROM AP_CHECKS_ALL AC
					   LEFT JOIN CE_PAYMENT_DOCUMENTS CPD
						  ON AC.PAYMENT_DOCUMENT_ID = CPD.PAYMENT_DOCUMENT_ID
					   LEFT JOIN AP_SUPPLIERS SUP ON AC.VENDOR_ID = SUP.VENDOR_ID
					   LEFT JOIN AP_INVOICE_PAYMENTS_ALL AIP ON AC.CHECK_ID = AIP.CHECK_ID
					   LEFT JOIN AP_INVOICES_ALL AI ON AIP.INVOICE_ID = AI.INVOICE_ID
					   LEFT JOIN
					   (  SELECT INVOICE_ID,
								 SUM (INCLUDED_TAX_AMOUNT)   INCLUDED_TAX_AMOUNT,
								 MAX (TAX_CLASSIFICATION_CODE) TAX_CLASSIFICATION_CODE
							FROM AP_INVOICE_LINES_ALL
						   WHERE LINE_TYPE_LOOKUP_CODE = 'ITEM'
						GROUP BY INVOICE_ID) AIL
						  ON AI.INVOICE_ID = AIL.INVOICE_ID
					   LEFT JOIN
					   (  SELECT INVOICE_ID,
								 SUM (AMOUNT)                AMOUNT,
								 MAX (TAX_CLASSIFICATION_CODE) TAX_CLASSIFICATION_CODE,
								 MAX (AWT_GROUP_ID)          AWT_GROUP_ID
							FROM AP_INVOICE_LINES_ALL
						   WHERE LINE_TYPE_LOOKUP_CODE = 'AWT'
						GROUP BY INVOICE_ID) AIL2
						  ON AI.INVOICE_ID = AIL2.INVOICE_ID
					   LEFT JOIN AP_AWT_GROUP_TAXES_ALL GT ON AIL2.AWT_GROUP_ID = GT.GROUP_ID
					   LEFT JOIN AP_AWT_TAX_RATES_ALL TR ON GT.TAX_NAME = TR.TAX_NAME
				 WHERE 1 = 1
					   AND AC.CHECK_DATE BETWEEN ? AND ?
					   AND CPD.PAYMENT_DOCUMENT_NAME = ?
					   AND AC.STATUS_LOOKUP_CODE = 'NEGOTIABLE'";
		$data = $this->oracle->query($sql, $params);
		return $data->result();
		
	}
	
	public function get_payables_rcbc($params){
		
		$sql = "SELECT
					max(sup.segment1)     payee_code,
					TO_CHAR(sysdate, 'MMDDYY')    check_date,
					''               other1,
					REPLACE(TO_CHAR(sum(aip.amount),'fm99999999990.00'), '.','') amount,
					''               other2,
					'Supplier payments'               remarks,
					''               other3,
					''               other4,
					''               other5,
					''               other6,
					'N'               ewt,
					'PASEO DE STA. ROSA STA ROSA LAGUNA' delivery_address,
					''         zip_code,
					''               alpha_numeric_code,
					''               first_month_of_the_qtr,
					''               second_month_of_the_qtr,
					''               third_month_of_the_qtr,
					'' total_amount,
					'' tax_witheld,
					'1'        pickup_or_delivery,
					'318'               pickup_branch,
					'PURC010'               purpose_code,
					REPLACE(REPLACE(REGEXP_REPLACE(TRANSLATE(max(sup.vendor_name), ' ,''/.', ' '), ' {2,}', ' ' ),'&','AND'),'Ñ','N') payee_name2,
					CASE WHEN  sum(aip.amount) > 1000000 THEN 'Production related supplies' ELSE '' END particulars,
					'ISUZU'               authorized_collector,
					'1'               payee_or_collector
				 FROM ap_checks_all ac
					LEFT JOIN ce_payment_documents cpd
						ON ac.payment_document_id = cpd.payment_document_id
						 LEFT JOIN ap_invoice_payments_all aip
						 ON ac.check_id = aip.check_id
					   LEFT JOIN ap_suppliers sup ON ac.vendor_id = sup.vendor_id
					 WHERE     1 = 1
					  AND ac.check_date BETWEEN ? AND ?
						AND cpd.payment_document_name = ?
					  AND ac.status_lookup_code = 'NEGOTIABLE'
					  GROUP BY ac.check_id";
		$data = $this->oracle->query($sql, $params);
		return $data->result_array();
		
	}
	
	public function get_payables_cnt($params){
		
		$sql = "SELECT COUNT(ac.check_id) cnt
				FROM AP_CHECKS_ALL AC
					LEFT JOIN CE_PAYMENT_DOCUMENTS CPD
					ON AC.PAYMENT_DOCUMENT_ID = CPD.PAYMENT_DOCUMENT_ID
				WHERE 1 = 1
					AND AC.CHECK_DATE BETWEEN ? AND ?
					AND CPD.PAYMENT_DOCUMENT_NAME = ?
					AND AC.STATUS_LOOKUP_CODE = 'NEGOTIABLE'";
					
		$data = $this->oracle->query($sql, $params);
		$rows = $data->result();
		return $rows[0];
		
	}
	
	public function insert_payables_header($params){
			
		$sql = "INSERT INTO IPC.IPC_CHECK_DISBURSEMENT_HEADERS (
					check_bank,
					check_from,
					check_to,
					created_by,
					date_created)
				VALUES(?,?,?,?,SYSDATE)";
		$this->oracle->query($sql, $params);
		
	}
	
	public function get_last_header_id(){
			
		$sql = "select IPC.IPC_CHECK_DISB_SEQ.currval last_header_id from dual";
		$data = $this->oracle->query($sql);
		$rows = $data->result();
		return $rows[0];
	}
	
	public function insert_payables_lines($params){
			
		$sql = "INSERT INTO IPC.IPC_CHECK_DISBURSEMENT_LINES (
					header_id,
					ifs_payee_code,
					payee_code,
					payee_name,
					account_number,
					reference_no,
					invoice_date,
					invoice_no,
					paid_amount,
					invoice_amount,
					vat_amount,
					invoice_wout_vat_amount,
					wht_amount,
					vat_code,
					wht_code,
					wht_rate,
					check_amount,
					check_no)
				VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$this->oracle->query($sql, $params);
	}
	
	public function get_payables_summary($header_id){
	
		$sql = "SELECT IFS_PAYEE_CODE,
						PAYEE_CODE,
						PAYEE_NAME,
						REFERENCE_NO,
						CHECK_NO,
						CHECK_AMOUNT,
						CHECK_BANK,
						CHECK_FROM,
						CHECK_TO,
						SUM(PAID_AMOUNT) PAID_AMOUNT
						  FROM IPC_CHECK_DISBURSEMENT_HEADERS CDH
							LEFT JOIN IPC_CHECK_DISBURSEMENT_LINES CDL 
							   ON CDH.ID = CDL.HEADER_ID
						 WHERE CDH.ID = ?
						 GROUP BY IFS_PAYEE_CODE,
						PAYEE_CODE,
						PAYEE_NAME,
						REFERENCE_NO,
						CHECK_NO,
						CHECK_AMOUNT,
						CHECK_BANK,
						CHECK_FROM,
						CHECK_TO
						ORDER BY IFS_PAYEE_CODE asc nulls first, CHECK_NO";
	
		$data = $this->oracle->query($sql, $header_id);
		return $data->result();
	}
	
	public function get_payables_per_line($header_id){
	
		$sql = "SELECT header_id,
					   ifs_payee_code,
					   payee_code,
					   payee_name,
					   reference_no,
					   invoice_date,
					   invoice_no,
					   paid_amount,
					   invoice_amount,
					   vat_amount,
					   invoice_wout_vat_amount,
					   wht_amount,
					   vat_code,
					   wht_code,
					   wht_rate,
					   check_amount,
					   check_no,
					   check_bank,
					   check_from,
					   check_to,
					   account_number
				  FROM IPC_CHECK_DISBURSEMENT_HEADERS CDH
					   LEFT JOIN IPC_CHECK_DISBURSEMENT_LINES CDL ON CDH.ID = CDL.HEADER_ID
				 WHERE CDH.ID = ?";
	
		$data = $this->oracle->query($sql, $header_id);
		return $data->result_array();
	}
}
