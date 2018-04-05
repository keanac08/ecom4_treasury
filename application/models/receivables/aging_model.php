<?php

class Aging_Model extends CI_Model {
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_receivables_aging($as_of_date){
		
		$and = '';
		if(in_array($this->session->tre_portal_user_type, array('IPC Parts','IPC Vehicle-Fleet','IPC Vehicle','IPC Fleet'))){
			$this->load->helper('profile_class_helper');
			$profile_class_id = get_user_access($this->session->tre_portal_user_type);
			$and = $profile_class_id != NULL ? 'AND hcpc.profile_class_id IN ('.$profile_class_id.')':'';
		}
		
		$sql = "SELECT profile_class_id,
						CASE WHEN profile_class_id IS NULL THEN 'Total' ELSE MAX(profile_class_name) END profile_class,
						SUM(CASE WHEN days_overdue = 0 AND delivery_date IS NOT NULL THEN balance ELSE 0 END) current_receivables,
						SUM(CASE WHEN delivery_date IS NULL THEN balance ELSE 0 END) contingent_receivables,
						SUM(CASE WHEN days_overdue > 0 THEN balance ELSE 0 END) past_due,
						SUM(balance) total
								FROM (
									SELECT      
										soa.profile_class_id,
										CASE
											WHEN soa.due_date IS NOT NULL AND soa.due_date < to_date('".$as_of_date."')
												THEN to_Date('".$as_of_date."') - soa.due_date
											ELSE 0
										END
										days_overdue,
										soa.delivery_date,
										hcpc.name profile_class_name,
										 soa.invoice_amount + (NVL(adj.adjustment_amount,0) * NVL(soa.EXCHANGE_RATE, 1)) - (NVL (araa.paid_amount , 0) * NVL(soa.EXCHANGE_RATE, 1))balance
									 FROM IPC.IPC_INVOICE_DETAILS soa
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
									  LEFT JOIN hz_parties hp 
										ON hcaa.party_id = hp.party_id
									 LEFT JOIN hz_cust_profile_classes hcpc
										ON soa.profile_class_id = hcpc.profile_class_id
									 WHERE     1 = 1
										 ".$and."
										 AND soa.trx_date <= '".$as_of_date."'
										 )
								WHERE balance > 0
									 GROUP BY ROLLUP (profile_class_id)";
		$data = $this->oracle->query($sql);
		return $data->result();
	}

	public function get_receivables_profile_summary($as_of_date, $profile_class_id){
	
		$sql = "SELECT customer_id,
						max(profile_class_id) profile_class_id,
						CASE
						  WHEN customer_id IS NULL THEN 'Total'
						  WHEN MAX(account_name) IS NOT NULL
						  THEN
							 MAX(party_name) || ' - ' || MAX(account_name)
						  ELSE
							 MAX(party_name)
						END
						  customer_name,
						SUM(CASE WHEN days_overdue = 0 AND delivery_date IS NOT NULL THEN balance ELSE 0 END) current_receivables,
						SUM(CASE WHEN delivery_date IS NULL THEN balance ELSE 0 END) contingent_receivables,
						SUM(CASE WHEN days_overdue > 0 THEN balance ELSE 0 END) past_due,
						SUM(balance) total
								FROM (
									SELECT      
										soa.profile_class_id,
										soa.customer_id,
										hcca.account_name,
										hp.party_name,
										CASE
											WHEN soa.due_date IS NOT NULL AND soa.due_date < to_date('".$as_of_date."')
												THEN to_Date('".$as_of_date."') - soa.due_date
											ELSE 0
										END
										days_overdue,
										soa.delivery_date,
										hcpc.name profile_class_name,
										 soa.invoice_amount + (NVL(adj.adjustment_amount,0) * NVL(soa.EXCHANGE_RATE, 1)) - (NVL (araa.paid_amount , 0) * NVL(soa.EXCHANGE_RATE, 1)) balance
									 FROM IPC.IPC_INVOICE_DETAILS soa
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
									  LEFT JOIN hz_parties hp 
										ON hcaa.party_id = hp.party_id
									 LEFT JOIN hz_cust_profile_classes hcpc
										ON soa.profile_class_id = hcpc.profile_class_id
								      LEFT JOIN hz_cust_accounts_all hcca
                                        ON soa.customer_id = hcca.cust_account_id
                                      LEFT JOIN hz_parties hp
                                        ON  hcca.party_id = hp.party_id
									 WHERE     1 = 1
										 AND soa.trx_date <= '".$as_of_date."'
										 AND soa.profile_class_id = ?
										 )
								WHERE balance > 0
									 GROUP BY ROLLUP (customer_id)";
		$data = $this->oracle->query($sql, $profile_class_id);
		return $data->result();
	}

	public function get_profile_class_name($profile_class_id){
		
		$sql = "select name
				from hz_cust_profile_classes
				where profile_class_id = ?";
		$data = $this->oracle->query($sql, $profile_class_id);
		return $data->result();
	}
	
	public function get_receivables_summary_excel($as_of_date, $profile_class_id, $customer_id){
		
		$and = '';
		$and2 = '';
		
		if(in_array($this->session->tre_portal_user_type, array('Administrator','Dealer Admin'))){
			$and = ($profile_class_id != 'NULL' AND $profile_class_id != NULL) ? 'AND soa.profile_class_id = ' . $profile_class_id: '';
			$and2 = $customer_id != NULL ? 'AND soa.customer_id = ' . $customer_id: '';
		}
		else if(in_array($this->session->tre_portal_user_type, array('IPC Parts'))){
			$this->load->helper('profile_class_helper');
			$profile_class_id = get_user_access($this->session->tre_portal_user_type);
			$and = $profile_class_id != NULL ? 'AND hcpc.profile_class_id IN ('.$profile_class_id.')':'';
			$and2 = $customer_id != NULL ? 'AND soa.customer_id = ' . $customer_id: '';
		}
		
		$sql = "SELECT customer_id,
						customer_name,
						account_name,
						fleet_name,
						cust_po_number,
						profile_class,
						account_code,
						invoice_id,
						invoice_number,
						TO_CHAR(invoice_date, 'YYYY-MM-DD') invoice_date,
						cs_number,
						sales_model,
						body_color,
						payment_terms,
						TO_CHAR(delivery_date, 'YYYY-MM-DD') delivery_date,
						TO_CHAR(due_date, 'YYYY-MM-DD') due_date,
						days_overdue,
						invoice_amount_orig,
						vat_amount_orig,
						balance_orig,
						invoice_currency_code currency_code,
						exchange_rate,
						invoice_amount_php,
						vat_amount_php,
						wht_amount_php,
						balance_php,
						TO_CHAR(last_payment_date,'YYYY-MM-DD') last_payment_date,
						CASE WHEN  (balance_php - wht_amount_php) < 1
							THEN balance_php
							ELSE 0
						END cwt_balance,
						CASE WHEN  (balance_php - wht_amount_php) >= 1
							THEN balance_php
							ELSE 0
						END amount_due_balance,
						CASE WHEN days_overdue = 0 THEN balance_php ELSE 0 END current_,
						CASE WHEN days_overdue BETWEEN 1 and 15 THEN balance_php ELSE 0 END past_due_1_to_15,
						CASE WHEN days_overdue BETWEEN 16 and 30 THEN balance_php ELSE 0 END past_due_16_to_30,
						CASE WHEN days_overdue BETWEEN 31 and 60 THEN balance_php ELSE 0 END past_due_31_to_60,
						CASE WHEN days_overdue BETWEEN 61 and 90 THEN balance_php ELSE 0 END past_due_61_to_90,
						CASE WHEN days_overdue BETWEEN 91 and 120 THEN balance_php ELSE 0 END past_due_91_to_120,
						CASE WHEN days_overdue BETWEEN 121 and 360 THEN balance_php ELSE 0 END past_due_121_to_360,
						CASE WHEN days_overdue BETWEEN 361 and 720 THEN balance_php ELSE 0 END past_due_361_to_720,
						CASE WHEN days_overdue > 720 THEN balance_php ELSE 0 END past_due_over_720,
						CASE WHEN days_overdue > 0 THEN balance_php ELSE 0 END past_due
						FROM (
							SELECT 
								hcaa.cust_account_id customer_id,
								hp.party_name       customer_name,
								hcaa.account_name   account_name,
								ooha.attribute3 fleet_name,
								ooha.cust_po_number cust_po_number,
								hcpc.name           profile_class,
								gcc.segment6 account_code,
								soa.customer_trx_id invoice_id,
								soa.trx_number      invoice_number,
								soa.trx_date        invoice_date,
								soa.cs_number,
								msib.attribute9 sales_model,
								msib.attribute8 body_color,
								soa.payment_term payment_terms,
								soa.delivery_date,
								soa.due_date,
								CASE
									WHEN soa.due_date IS NOT NULL AND soa.due_date < to_date('".$as_of_date."')
										THEN to_Date('".$as_of_date."') - soa.due_date
									ELSE 0
								END
								days_overdue,
								soa.invoice_orig_amount invoice_amount_orig,
								soa.vat_orig_amount vat_amount_orig,
								soa.invoice_orig_amount + NVL(adj.adjustment_amount,0)  - NVL (araa.paid_amount , 0)balance_orig,
								soa.invoice_amount invoice_amount_php,
								soa.vat_amount vat_amount_php,
								soa.wht_orig_amount wht_amount_php,
								soa.invoice_amount + (NVL(adj.adjustment_amount,0) * NVL(soa.EXCHANGE_RATE, 1)) - (NVL (araa.paid_amount , 0) * NVL(soa.EXCHANGE_RATE, 1))balance_php,
								araa2.apply_date last_payment_date,
								soa.invoice_currency_code,
								soa.exchange_rate
							 FROM IPC.IPC_INVOICE_DETAILS soa
							 LEFT JOIN ra_customer_trx_all rcta
								ON soa.customer_trx_id = rcta.customer_trx_id
							 LEFT JOIN oe_order_headers_all ooha
								ON rcta.interface_header_attribute1 = to_char(ooha.order_number) 
							 LEFT JOIN mtl_serial_numbers msn
								ON rcta.attribute3 = msn.serial_number
								AND msn.c_attribute30 IS NULL
							 LEFT JOIN mtl_system_items_b msib
								ON msn.inventory_item_id = msib.inventory_item_id
								AND msn.current_organization_id = msib.organization_id
							 LEFT JOIN ra_cust_trx_line_gl_dist_all gld
								ON soa.customer_trx_id = gld.customer_trx_id
								and gld.account_class = 'REC'
							 LEFT JOIN gl_code_combinations gcc
								on gld.code_combination_id = gcc.code_combination_id
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
								(SELECT applied_customer_trx_id,
												applied_payment_schedule_id,
												MAX (apply_date) apply_date
									FROM ar_receivable_applications_all
										WHERE display = 'Y'
										AND application_type = 'CASH'
										AND gl_date <= '".$as_of_date."'
									GROUP BY applied_customer_trx_id, applied_payment_schedule_id) araa2
								ON soa.customer_trx_id = araa2.applied_customer_trx_id
									AND soa.payment_schedule_id = araa2.applied_payment_schedule_id
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
							  LEFT JOIN hz_parties hp 
								ON hcaa.party_id = hp.party_id
							 LEFT JOIN hz_cust_profile_classes hcpc
								ON soa.profile_class_id = hcpc.profile_class_id
							 WHERE     1 = 1
								 ".$and. " " .$and2."
								 AND soa.trx_date <= '".$as_of_date."'
								 AND soa.invoice_amount + (NVL(adj.adjustment_amount,0) * NVL(soa.EXCHANGE_RATE, 1)) - (NVL (araa.paid_amount , 0) * NVL(soa.EXCHANGE_RATE, 1)) > 0)";
			//~ echo $sql;die();
		$data = $this->oracle->query($sql);
		return $data->result_array();	
	}
}
