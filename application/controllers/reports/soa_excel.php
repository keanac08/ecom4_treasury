<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soa_excel extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/soa_model');
		session_check();
	}
	
	public function index(){
			
		$this->load->library('excel');
			
		$sales_type = $this->uri->segment(4);
		
		$this->load->helper('profile_class_helper');
		$this->load->helper('date_helper');

		$customer_id = $this->uri->segment(6) / 101403;
		$as_of_date = DateTime::createFromFormat('mdY', $this->uri->segment(7));
		$as_of_date =  $as_of_date->format('m/d/Y');
		
		$profile_class_ids = get_profile_class_ids($sales_type);
		
		$rows = $this->soa_model->get_soa_per_customer_excel($customer_id, $profile_class_ids, date('d-M-y', strtotime($as_of_date)));

		$writer = new XLSXWriter();
		
		$header = array(
						'Customer_ID' => 'integer',
						'Customer_Name' => 'string',
						'Account_Name' => 'string',
						'Fleet_Name' => 'string',
						'Customer_PO_Number' => 'string',
						'Profile_Class' => 'string',
						'Invoice_ID' => 'integer',
						'Invoice_Number' => 'string',
						'Invoice_Date' => 'MM/DD/YYYY',
						'CS_Number' => 'string',
						'Sales_Model' => 'string',
						'Payment_Term' => 'string',
						'Delivery_Date' => 'MM/DD/YYYY',
						'Due_Date' => 'MM/DD/YYYY',
						'Days_Overdue' => 'integer',
						'Invoice_Amount' => '#,##0.00',
						'WHT_Amount' => '#,##0.00',
						'Balance' => '#,##0.00',
						'Currency' => 'string',
						'Exchange_Rate' => '#,##0.00',
						'As_of_Date' => 'MM/DD/YYYY'
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		foreach($rows as $row){
			
			$line = array(
						$row->CUSTOMER_ID,
						$row->CUSTOMER_NAME,
						$row->ACCOUNT_NAME,
						$row->FLEET_NAME,
						$row->CUST_PO_NUMBER,
						$row->PROFILE_CLASS,
						$row->TRANSACTION_ID,
						$row->TRANSACTION_NUMBER,
						excel_date($row->TRANSACTION_DATE),
						$row->CS_NUMBER,
						$row->SALES_MODEL,
						$row->PAYMENT_TERM,
						excel_date($row->DELIVERY_DATE),
						excel_date($row->DUE_DATE),
						$row->DAYS_OVERDUE,
						$row->TRANSACTION_AMOUNT,
						$row->WHT_AMOUNT,
						$row->BALANCE,
						$row->CURRENCY,
						$row->EXCHANGE_RATE,
						excel_date($as_of_date)
					);
			$writer->writeSheetRow('Sheet1', $line);
		}
		
		//~ $writer->writeToFile('.xlsx');
		$filename = $customer_id."-".strtoupper($sales_type)."-".$this->uri->segment(7).".xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$writer->writeToStdOut();
	}
}
