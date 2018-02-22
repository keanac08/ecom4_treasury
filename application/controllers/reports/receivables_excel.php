<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receivables_excel extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('receivables/aging_model');
		session_check();
	}
	
	public function index(){
		
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3600);
			
		$this->load->library('excel');
		$this->load->helper('date_helper');
			
		$as_of_date = DateTime::createFromFormat('mdY', $this->uri->segment(4));
		$as_of_date =  $as_of_date->format('m/d/Y');
		$profile_class_id = $this->uri->segment(5);
		
		$rows = $this->aging_model->get_receivables_summary_excel(date('d-M-y', strtotime($as_of_date)), $profile_class_id);

		$writer = new XLSXWriter();
		
		$header = array(
						'CUSTOMER_ID' => 'integer',
						'CUSTOMER_NAME' => 'string',
						'ACCOUNT_NAME' => 'string',
						'FLEET_NAME' => 'string',
						'CUST_PO_NUMBER' => 'string',
						'PROFILE_CLASS' => 'string',
						'ACCOUNT_CODE' => 'string',
						'INVOICE_ID' => 'integer',
						'INVOICE_NUMBER' => 'string',
						'INVOICE_DATE' => 'MM/DD/YYYY',
						'CS_NUMBER' => 'string',
						'SALES_MODEL' => 'string',
						'BODY_COLOR' => 'string',
						'PAYMENT_TERMS' => 'string',
						'DELIVERY_DATE' => 'MM/DD/YYYY',
						'DUE_DATE' => 'MM/DD/YYYY',
						'DAYS_OVERDUE' => 'integer',
						'INVOICE_AMOUNT_ORIG' => '#,##0.00',
						'VAT_AMOUNT_ORIG' => '#,##0.00',
						'BALANCE_ORIG' => '#,##0.00',
						'CURRENCY_CODE' => 'string',
						'EXCHANGE_RATE' => '#,##0.00',
						'INVOICE_AMOUNT_PHP' => '#,##0.00',
						'VAT_AMOUNT_PHP' => '#,##0.00',
						'WHT_AMOUNT_PHP' => '#,##0.00',
						'BALANCE_PHP' => '#,##0.00',
						'CWT_BALANCE' => '#,##0.00',
						'AMOUNT_DUE_BALANCE' => '#,##0.00',
						'PAST_DUE_1_TO_15' => '#,##0.00',
						'PAST_DUE_16_TO_30' => '#,##0.00',
						'PAST_DUE_31_TO_60' => '#,##0.00',
						'PAST_DUE_61_TO_90' => '#,##0.00',
						'PAST_DUE_91_TO_120' => '#,##0.00',
						'PAST_DUE_120_TO_360' => '#,##0.00',
						'PAST_DUE_361_TO_720' => '#,##0.00',
						'PAST_DUE_OVER_720' => '#,##0.00',
						'PAST_DUE' => '#,##0.00',
						'CURRENT' => '#,##0.00'
					);
		$writer->writeSheetHeader('Sheet1', $header );
		
		foreach($rows as $row){
			$writer->writeSheetRow('Sheet1', $row);
		}
		
		$filename = "receivables_summary_".str_replace('/', '', $as_of_date).".xlsx";
		header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		$writer->writeToStdOut();
	}
}
