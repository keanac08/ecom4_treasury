<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soa extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/soa_model');
		session_check();
	}
	
	public function admin(){
		
		$this->load->helper('profile_class_helper');
		
		$sales_type = $this->uri->segment(4);
		$profile_class_ids = get_profile_class_ids($sales_type);
		
		if($this->uri->segment(6)){
			$customer_id = $this->uri->segment(6) / 101403;
			$as_of_date = DateTime::createFromFormat('mdY', $this->uri->segment(7));
			$as_of_date =  $as_of_date->format('m/d/Y');
		}
		else{
			$as_of_date = $this->input->post('as_of_date') != NULL ? $this->input->post('as_of_date') : date('m/d/Y');
			
			if(in_array($this->session->tre_portal_user_type, array('Administrator','IPC Parts','IPC Vehicle-Fleet','IPC Vehicle','IPC Fleet'))){
				$customer_id = $this->input->post('customer_id') != NULL ? $this->input->post('customer_id') : 0;
			}
			else if(in_array($this->session->tre_portal_user_type, array('Dealer Admin','Dealer Vehicle','Dealer Parts'))){
				$customer_id = $this->session->tre_portal_customer_id;
			}
		}
		
		$rows = $this->soa_model->get_customer_details($customer_id, $profile_class_ids);
		if(empty($rows)){
			$customer_id = 0;
		}
		else{
			$data['customer_details'] = $rows;
		}
		
		$data['customer_details'] = $rows;
		
		$data['customer_id'] = $customer_id;
		$data['as_of_date'] = $as_of_date;
		$data['sales_type'] = $sales_type;
		
		$data['content']    = 'receivables/soa_admin_view';
		$data['head_title'] = 'Treasury | SOA';
		$data['title'] = 'Statement of Account<small>' . UCWORDS($sales_type) . ' Transaction</small>';
		$data['soa_detailed'] = $this->soa_model->get_soa_per_customer($customer_id, $profile_class_ids, date('d-M-y', strtotime($as_of_date)));
		$this->load->view('include/template',$data);
	}
	
	public function ajax_customers_per_profile(){
		
		$this->load->helper('profile_class_helper');
		
		$profile_class_ids = get_profile_class_ids($this->input->post('sales_type'));
		$q = strtolower('%'.$this->input->post('q').'%');
		
		$return_arr = array();
		$data =  $this->soa_model->get_customers_per_profile($profile_class_ids,$q);
		foreach($data as $row){
			$row_array = array(
							'id'=> $row->CUSTOMER_ID,
							 'text' => $row->CUSTOMER_NAME
						);
			array_push($return_arr,$row_array);
		}
		echo json_encode($return_arr);
	}
	
	public function ajax_invoice_details(){
		
		$customer_trx_id = $this->input->post('customer_trx_id');
		$profile_class = $this->input->post('profile_class');
		
		if($profile_class == 'Dealers-Vehicle' OR $profile_class == 'Dealers-Fleet' OR $profile_class == 'Fleet Accounts' ){
			$data['result'] =  $this->soa_model->get_vehicle_invoice_details($customer_trx_id);
			$data['data'] = $data['result'][0];
			echo $this->load->view('ajax/vehicle_invoice_details_view',$data,true);
		}
		else{
			if($profile_class == 'Dealers-Others' OR $profile_class == 'Other Customers'){
				$rows =  $this->soa_model->get_others_invoice_details($customer_trx_id);
			}
			else{	
				$rows =  $this->soa_model->get_parts_invoice_details($customer_trx_id);
			}
			
			$x = 0;
			$net_amount = 0;
			$vat_amount = 0;
			$lines = array();
			foreach($rows as $row){
				$lines[] = array(
								'QTY' => $row->QTY,
								'PART_NO' => $row->PART_NO,
								'LINE_NUMBER' => $row->LINE_NUMBER,
								'PART_DESCRIPTION' => $row->PART_DESCRIPTION,
								'UNIT_SELLING_PRICE' => $row->UNIT_SELLING_PRICE,
								'NET_AMOUNT' => $row->NET_AMOUNT
							);
				$net_amount += $row->NET_AMOUNT;
				$vat_amount += $row->VAT_AMOUNT;
				if($x == 0){
					$header = array(
									'CUSTOMER_TRX_ID' => $row->CUSTOMER_TRX_ID,
									'TRX_NUMBER' => $row->TRX_NUMBER,
									'TRX_DATE' => $row->TRX_DATE,
									'ORDER_TYPE' => $row->ORDER_TYPE,
									'PO_NUMBER' => $row->PO_NUMBER,
									'ORDER_NUMBER' => $row->ORDER_NUMBER,
									'ORDERED_DATE' => $row->ORDERED_DATE,
									'PAID_AMOUNT' => $row->PAID_AMOUNT,
									'STATUS' => $row->STATUS,
									'CURRENCY' => $row->CURRENCY,
									'EXCHANGE_RATE' => $row->EXCHANGE_RATE,
									'TOTAL_NET_AMOUNT' => $row->TOTAL_NET_AMOUNT,
									'TOTAL_VAT_AMOUNT' => $row->TOTAL_VAT_AMOUNT,
									'CREDITED_AMOUNT' => $row->CREDITED_AMOUNT,
									'INVOICE_AMOUNT' => $row->INVOICE_AMOUNT,
									'ADJUSTED_AMOUNT' => $row->ADJUSTED_AMOUNT,
									'PAID_AMOUNT' => $row->PAID_AMOUNT,
									'BALANCE' => $row->BALANCE
								);
					$data['header'] = (object)$header;
					$x++;
				}
			}
			$data['lines'] = $lines;
			
			//~ echo '<pre>';
			//~ print_r($lines);
			//~ echo '</pre>';
			
			if($profile_class == 'Dealers-Others' OR $profile_class == 'Other Customers'){
				echo $this->load->view('ajax/others_invoice_details_view',$data,true);
			}
			else{	
				echo $this->load->view('ajax/parts_invoice_details_view',$data,true);
			}
			
		}
		
		
	}
}
