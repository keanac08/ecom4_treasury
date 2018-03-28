<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_details extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('search/invoice_details_model');
		session_check();
	}
	
		public function index(){
		
		$this->load->helper('profile_class_helper');
		
		//~ $q = 'D0Q042';
		//~ $q = '980167118';
		//~ $q = '980202022';
		//~ $q = '40200000307';
		//~ $q = '40200027949';
		//~ $q = '40300014985';
		
		$q = $this->input->get('q');
		
		
		$data['title'] = 'Search Result for "' . $q . '"';
		$data['head_title'] = 'Treasury | Search';

		$result = $this->invoice_details_model->get_header_details($q);
		if(!empty($result)){
			
			$data['content'] = 'search/invoice_details_view';
			$data['header'] = $result[0];
			
			$sales_type = get_sales_type($result[0]->PROFILE_CLASS_ID);
			$invoice_id = $result[0]->INVOICE_ID;
			$data['payments'] = $this->invoice_details_model->get_payment_details($invoice_id);
			
			//~ echo $invoice_id;
			
			if(in_array($sales_type, array('vehicle','fleet'))){
				$result = $this->invoice_details_model->get_vehicle_line_details($invoice_id);
				//~ print_r($result);
				$data['line'] = $result[0];
				$data['sales_type'] = 'vehicle';
			}
			else{
				$rows = $this->invoice_details_model->get_parts_line_details($invoice_id);

				$data['sales_type'] = 'parts';
				
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
						$lines_header = array(
										'STATUS' => $row->STATUS,
										'CURRENCY' => $row->CURRENCY,
										'EXCHANGE_RATE' => $row->EXCHANGE_RATE,
										'TOTAL_NET_AMOUNT' => $row->TOTAL_NET_AMOUNT,
										'TOTAL_VAT_AMOUNT' => $row->TOTAL_VAT_AMOUNT,
										'INVOICE_AMOUNT' => $row->INVOICE_AMOUNT,
										'ADJUSTED_AMOUNT' => $row->ADJUSTED_AMOUNT,
										'CREDITED_AMOUNT' => $row->CREDITED_AMOUNT,
										'PAID_AMOUNT' => $row->PAID_AMOUNT,
										'BALANCE' => $row->BALANCE
									);
						$data['lines_header'] = (object)$lines_header;
						$x++;
					}
				}
				$data['lines'] = $lines;
			}
		}
		else{
			$data['content'] = 'search/no_results_view';
		}
		
		$this->load->view('include/template',$data);
	}
}
