<?php

class Transaction_model extends CI_Model {
	
	private $oracle = NULL;
	
	public function __construct(){
		
		parent::__construct();
		$this->oracle = $this->load->database('oracle', true);
	}

	public function get_summary_per_customer($customer_id, $as_of_date, $profile_class_id){

		$and = $profile_class_id != NULL ? 'AND hcpc.profile_class_id IN ('.$profile_class_id.')':'';
		
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
							(soa.invoice_orig_amount  + NVL (adj.adjustment_amount, 0)) - NVL (araa.paid_amount, 0) balance
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
							 AND soa.customer_id = ?
							 )
					WHERE balance > 0
						 GROUP BY ROLLUP (profile_class_id)"; //echo $sql;die();
		
		$data = $this->oracle->query($sql,$customer_id);
		return $data->result();
	}
	
	public function get_summary_per_customer_pdf($customer_id, $as_of_date){

		//~ $and = $profile_class_id != NULL ? 'AND hcpc.profile_class_id IN ('.$profile_class_id.')':'';
		$and = '';
		if(in_array($this->session->tre_portal_user_type, array('IPC Parts','IPC Vehicle-Fleet','IPC Vehicle','IPC Fleet','Dealer Parts','Dealer Vehicle'))){
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
							(soa.invoice_orig_amount  + NVL (adj.adjustment_amount, 0)) - NVL (araa.paid_amount, 0) balance
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
							 AND soa.customer_id = ?
							 )
					WHERE balance > 0
						 GROUP BY ROLLUP (profile_class_id)";
		
		$data = $this->oracle->query($sql,$customer_id);
		return $data->result();
	}
	
	public function get_customers($customer_name, $profile_class_id){
		
		$and = $profile_class_id != NULL ? 'AND hcpc.profile_class_id IN ('.$profile_class_id.')':'';
				 
		$sql = "SELECT DISTINCT hca.cust_account_id customer_id,
						CASE
						  WHEN hca.account_name IS NOT NULL
						  THEN
							 hp.party_name || ' - ' || hca.account_name
						  ELSE
							 hp.party_name
					   END
						  customer_name
					FROM hz_cust_accounts_all hca
					LEFT JOIN hz_parties hp 
						ON hca.party_id = hp.party_id
					LEFT JOIN hz_customer_profiles hzp
						ON    hca.cust_account_id = hzp.cust_account_id
						-- AND rcta.bill_to_site_use_id = hzp.site_use_id
					LEFT JOIN hz_cust_profile_classes hcpc
						ON hzp.profile_class_id = hcpc.profile_class_id
					WHERE 1 = 1 
						AND hca.status = 'A'
						".$and."
						AND  lower(hp.party_name || ' - ' || hca.account_name) like ?";
				 
		$data = $this->oracle->query($sql,$customer_name);
		return $data->result();
	}
	
	public function get_customer($customer_id){
		
		$sql = "SELECT  hcca.cust_account_id customer_id,
						CASE
						  WHEN hcca.account_name IS NOT NULL
						  THEN
							 hp.party_name || ' - ' || hcca.account_name
						  ELSE
							 hp.party_name
					   END
						  customer_name
				  FROM hz_cust_accounts_all hcca
					   LEFT JOIN hz_parties hp ON hcca.party_id = hp.party_id
				 WHERE 1 = 1 
					AND  hcca.cust_account_id = ?
					AND hcca.status = 'A'";
				 
		$data = $this->oracle->query($sql,$customer_id);
		return $data->result();
	}
}
