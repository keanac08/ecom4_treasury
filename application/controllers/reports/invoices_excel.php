<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices_excel extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/invoice_model');
		session_check();
	}
	
	public function index(){
		
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
			
		$this->load->library('excel');
		$this->load->helper('date_helper');
		$this->load->helper('profile_class_helper');
		
		if($this->input->post('from_date') != NULL){
			$from_date = date('d-M-y', strtotime($this->input->post('from_date')));
			$to_date = date('d-M-y', strtotime($this->input->post('to_date')));
			
			if(!in_array($this->session->tre_portal_user_type,array('Dealer Parts','Dealer Vehicle'))){
				$sales_type = $this->input->post('sales_type');
			}
			else if($this->session->tre_portal_user_type == 'Dealer Parts'){
				$sales_type = 'parts';
			}
			else if($this->session->tre_portal_user_type == 'Dealer Vehicle'){
				$sales_type = 'vehicle-fleet';
			}
			$profile_ids = get_profile_class_ids($sales_type);
		}
		else{
			$from_date = DateTime::createFromFormat('mdY', $this->uri->segment(4));
			$from_date =  $from_date->format('d-M-y');
			
			$to_date = DateTime::createFromFormat('mdY', $this->uri->segment(5));
			$to_date =  $to_date->format('d-M-y');
			
			$profile_ids = 0;
		}
		
		$customer_id = $this->session->tre_portal_customer_id;
		
		$rows = $this->invoice_model->get_invoices($from_date, $to_date, $profile_ids, $customer_id);

		$writer = new XLSXWriter();
		
		$header = array(
						'CUSTOMER_ID' => 'string',
						'PARTY_NAME' => 'string',
						'ACCOUNT_NAME' => 'string',
						'FLEET_NAME' => 'string',
						'CS_NUMBER' => 'string',
						'INVOICE_ID' => 'string',
						'INVOICE_NUMBER' => 'string',
						'INVOICE_DATE' => 'MM/DD/YYYY',
						'PAYMENT_TERMS' => 'string',
						'DELIVERY_DATE' => 'MM/DD/YYYY',
						'DUE_DATE' => 'MM/DD/YYYY',
						'INVOICE_TYPE' => 'string',
						'ORDER_NUMBER' => 'string',
						'ORDERED_DATE' => 'MM/DD/YYYY',
						'ORDER_TYPE' => 'string',
						'CUST_PO_NUMBER' => 'string',
						'PROFILE_CLASS' => 'string',
						'DR_NUMBER' => 'string',
						'INVOICE_AMOUNT' => '#,##0.00',
						'VAT_AMOUNT' => '#,##0.00',
						'WHT_AMOUNT' => '#,##0.00',
						'BALANCE' => '#,##0.00',
						'INVOICE_STATUS' => 'string',
						'RECEIPT_NUMBER' => 'string',
						'INVOICE_CURRECNY_CODE' => 'string',
						'EXCHANGE_RATE' => '#,##0.00'
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		foreach($rows as $row){
				$writer->writeSheetRow('Sheet1', $row);
		}
		
		$filename = "invoices.xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$writer->writeToStdOut();
	}
}
